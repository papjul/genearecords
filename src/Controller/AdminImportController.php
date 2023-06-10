<?php
declare(strict_types=1);

namespace App\Controller;

use App\Entity\Event;
use App\Entity\Import;
use App\Entity\ImportField;
use App\Entity\Location;
use App\Entity\Person;
use App\Entity\Record;
use App\Form\CsvImportCreateFormType;
use App\Form\CsvImportEditFormType;
use App\Form\LocationCreateFormType;
use Doctrine\ORM\EntityManagerInterface;
use League\Csv\Exception;
use League\Csv\Reader;
use League\Csv\UnavailableStream;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\FormError;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PropertyAccess\PropertyAccess;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\String\Slugger\SluggerInterface;

#[Route('/{_locale<%app.supported_locales%>}/admin/import')]
class AdminImportController extends AbstractController
{
    #[Route('', name: 'admin_import', methods: ["GET"])]
    public function imports(EntityManagerInterface $entityManager): Response
    {
        return $this->render('admin/imports.html.twig', [
            'imports' => $entityManager->getRepository(Import::class)->findAll()
        ]);
    }

    #[Route('/new', name: 'admin_import_new', methods: ["GET", "POST"])]
    public function import_new(Request $request, FormFactoryInterface $formFactory, EntityManagerInterface $entityManager, SluggerInterface $slugger): Response|RedirectResponse
    {
        $form = $formFactory->createBuilder(CsvImportCreateFormType::class)->getForm();
        $form->handleRequest($request);
        $templateVariables = [];
        $templateVariables['form'] = $form->createView();

        if ($form->isSubmitted() && $form->isValid()) {
            $csv = $form->get('csv')->getData();
            if ($csv && $csv->guessExtension() === 'csv') {
                $originalFilename = pathinfo($csv->getClientOriginalName(), PATHINFO_FILENAME);
                // this is needed to safely include the file name as part of the URL
                $safeFilename = $slugger->slug($originalFilename);
                $newFilename = $safeFilename . '-' . uniqid() . '.csv';

                try {
                    $csv->move(
                        $this->getParameter('csv_directory'),
                        $newFilename
                    );
                } catch (FileException $e) {
                    $templateVariables['error'] = 'An error happened when uploading the file to the server.';
                    if (true) { // TODO: Group debug only
                        $templateVariables['detailed_error'] = $e->getMessage();
                    }

                    return $this->render('admin/import-add.html.twig', $templateVariables);
                }

                try {
                    $reader = Reader::createFromPath($this->getParameter('csv_directory') . '/' . $newFilename);
                    $reader->setHeaderOffset(0);

                    $import = (new Import())->setCsvFilename($newFilename);
                    $entityManager->persist($import);
                    foreach ($reader->getHeader() as $columnNb => $columnHeader) {
                        $entityManager->persist((new ImportField())->setNumber($columnNb)->setImport($import));
                    }
                    $entityManager->flush();
                    return $this->redirectToRoute('admin_import_edit_properties', ['id' => $import->getId()]);
                } catch (UnavailableStream $e) {
                    $templateVariables = [];
                    $templateVariables['error'] = 'An error happened when loading the file from the server.';
                    if (true) { // TODO: Group debug only
                        $templateVariables['detailed_error'] = $e->getMessage();
                    }

                    return $this->render('admin/import-add.html.twig', $templateVariables);
                } catch (Exception $e) {
                    $templateVariables = [];
                    $templateVariables['error'] = 'An error happened when parsing the CSV file.';
                    if (true) { // TODO: Group debug only
                        $templateVariables['detailed_error'] = $e->getMessage();
                    }

                    return $this->render('admin/import-add.html.twig', $templateVariables);
                }
            } else {
                $templateVariables = [];
                $templateVariables['form'] = $form->createView();
                // TODO: Use Validator instead
                $templateVariables['error'] = $csv ? 'Filetype of the file selected is incorrect.' : 'No file provided.';
                if ($csv && true) { // TODO: Group debug only
                    $templateVariables['detailed_error'] = 'Detected extension: ' . $csv->guessExtension();
                }
                return $this->render('admin/import-add.html.twig', $templateVariables);
            }
        }

        return $this->render('admin/import-add.html.twig', $templateVariables);
    }


    #[Route('/edit/{id<[A-Z0-9]{26}>}', name: 'admin_import_edit', methods: ["GET"])]
    public function import_edit(Import $import): Response|RedirectResponse
    {
        if ($import->isReadyToBeImported()) {
            return $this->render('admin/import-edit.html.twig', ['import' => $import]);
        } else {
            return $this->redirectToRoute('admin_import_edit_properties', ['id' => $import->getId()]);
        }
    }

    #[Route('/edit/{id<[A-Z0-9]{26}>}/properties', name: 'admin_import_edit_properties', methods: ["GET", "POST"])]
    public function import_edit_properties(Request $request, EntityManagerInterface $entityManager, FormFactoryInterface $formFactory, Import $import): Response
    {
        $form = $formFactory->createBuilder(CsvImportEditFormType::class, $import, ['entity_manager' => $entityManager])->getForm();
        $form->handleRequest($request);
        $templateVariables = [];
        $templateVariables['import'] = $import;

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->getConnection()->beginTransaction(); // suspend auto-commit
            try {
                $import = $form->getData();
                // TODO: If place is changed, create place in location statistics if doesn't exist
                // TODO: If place is changed, update records + location statistics for old place + location statistics for new place
                // TODO: If fields are updated, update records
                $entityManager->persist($import); // Save changes to the import
                $entityManager->flush();
                $entityManager->getConnection()->commit();

                if ($import->isReadyToBeImported()) {
                    return $this->redirectToRoute('admin_import_edit_preview', ['id' => $import->getId()]);
                } else {
                    $form->get('fields')->addError(new FormError('Information was saved, however it is not ready to be imported because of incomplete information: fields appearing twice or mandatory fields (record year, surname, given names) not selected.'));
                }
            } catch (Exception $e) {
                $entityManager->getConnection()->rollBack();
                $templateVariables['error'] = 'An error happened when saving changes.';
                throw $e;
            }
        }
        try {
            $reader = Reader::createFromPath($this->getParameter('csv_directory') . '/' . $import->getCsvFilename());
            $reader->setHeaderOffset(0);
            $templateVariables['headers'] = $reader->getHeader();
        } catch (UnavailableStream $e) {
            $templateVariables['error'] = 'An error happened when loading the file from the server.';
            if (true) { // TODO: Group debug only
                $templateVariables['detailed_error'] = $e->getMessage();
            }

        } catch (Exception $e) {
            $templateVariables['error'] = 'An error happened when parsing the CSV file.';
            if (true) { // TODO: Group debug only
                $templateVariables['detailed_error'] = $e->getMessage();
            }
        }
        $templateVariables['form'] = $form->createView();
        return $this->render('admin/import-edit-properties.html.twig', $templateVariables);
    }

    #[Route('/edit/{id<[A-Z0-9]{26}>}/preview', name: 'admin_import_edit_preview', methods: ["GET", "POST"])]
    public function import_edit_preview(Request $request, EntityManagerInterface $entityManager, Import $import): Response
    {
        if ($import->isReadyToBeImported()) {
            $templateVariables = [];
            $persist = $request->isMethod('post');
            try {
                $reader = Reader::createFromPath($this->getParameter('csv_directory') . '/' . $import->getCsvFilename());
                $reader->skipEmptyRecords();

                $categoryFields = $import->getFieldsCategorized();
                $rows = iterator_to_array($reader->getRecords($import->getFieldsAsArray()));
                array_splice($rows, 0, 1);
                if (!$persist) {
                    $templateVariables['rows'] = $rows;
                }

                // 1. Delete all existing records
                if ($persist) {
                    $entityManager->getRepository(Record::class)->deleteAllFromImport($import, false);
                }

                // 2. Import all records
                $errors = [];
                $fieldsAdditional = $import->getFieldsAdditionalAsArray();
                foreach ($rows as $rowNb => $row) {
                    $processedFields = $categoryFields;
                    $row = [...$row, ...$fieldsAdditional];

                    // 2.1 Check mandatory records are filled in or skip records
                    foreach ($processedFields['person'] as $relationship => $personFields) {
                        if ($relationship === 'individual') {
                            if (empty($row[$personFields['surname']]) && empty($row[$personFields['given_names']])) {
                                $errors[$rowNb][] = 'Missing surname or given names of individual.';
                            }
                        } else {
                            // Skip some persons without given names
                            if (in_array($relationship, ['father', 'father_father', 'mother_father', 'spouse_father', 'spouse_father_father', 'spouse_mother_father'])
                                && (!array_key_exists('given_names', $personFields) || empty($row[$personFields['given_names']]))) {
                                unset($processedFields['person'][$relationship]);
                            }
                        }
                    }
                    foreach ($processedFields['event'] as $eventType => $eventFields) {
                        if (empty($row[$eventFields['year']]) || empty($row[$eventFields['country']])) { // TODO: Check as well that the \Assert is correct
                            unset ($processedFields['event'][$eventType]);
                        }
                        if ($eventType === 'other' && empty($row[$eventFields['type']])) {
                            unset ($processedFields['event']['other']);
                        }
                    }
                    if (!count($processedFields['event'])) {
                        $errors[$rowNb][] = 'No event with mandatory year and country found. If event is of type “other”, “type” is also mandatory.';
                    }
                    if (array_key_exists($rowNb, $errors) && count($errors[$rowNb])) {
                        continue;
                    }

                    // 2.2 Create record
                    $record = new Record();
                    $record->setImport($import);
                    $propertyAccessor = PropertyAccess::createPropertyAccessor();
                    foreach ($processedFields['record'] as $recordField => $rowField) {
                        if (!empty($row[$rowField])) {
                            try {
                                $propertyAccessor->setValue($record, $recordField, $row[$rowField]);
                            } catch (\Exception $e) {
                                $errors[$rowNb][] = 'Cannot set '.$row[$rowField].' for '.$recordField.'.';
                            }
                        }
                    }

                    // 2.3 Create each person
                    foreach ($processedFields['person'] as $relationship => $personFields) {
                        // Skip creating a person when there is no surname or given names
                        if (empty($row[$personFields['surname']]) && empty($row[$personFields['given_names']])) {
                            continue;
                        }

                        $person = new Person();
                        $person->setRelationship($relationship);
                        foreach ($personFields as $personField => $rowField) {
                            if (!empty($row[$rowField])) {
                                try {
                                    $propertyAccessor->setValue($person, $personField, $row[$rowField]);
                                } catch (\Exception $e) {
                                    $errors[$rowNb][] = 'Cannot set '.$row[$rowField].' for '.$personField.'.';
                                }
                            }
                        }
                        $record->addPerson($person);
                    }

                    // 2.4 Create each event
                    foreach ($processedFields['event'] as $eventType => $eventFields) {
                        $event = new Event();
                        $event->setRecord($record);
                        $event->setType($eventType);
                        foreach ($eventFields as $eventField => $rowField) {
                            if (!empty($row[$rowField])) {
                                try {
                                    $propertyAccessor->setValue($event, $eventField, $row[$rowField]);
                                } catch (\Exception $e) {
                                    $errors[$rowNb][] = 'Cannot set '.$row[$rowField].' for '.$eventField.'.';
                                }
                            }
                        }
                        $record->addEvent($event);
                    }

                    // 3. Persist
                    if ($persist) {
                        // Will cascade persist persons and events
                        $entityManager->persist($record);
                    }
                }

                if ($persist) {
                    $entityManager->flush();
                    $this->addFlash('success', 'Records imported successfully.');
                    return $this->redirectToRoute('admin_import');
                }

                $templateVariables['csvErrors'] = $errors;
            } catch (UnavailableStream $e) {
                $templateVariables['error'] = 'An error happened when loading the file from the server.';
                if (true) { // TODO: Group debug only
                    $templateVariables['detailed_error'] = $e->getMessage();
                }

            } catch (Exception $e) {
                $templateVariables['error'] = 'An error happened when parsing the CSV file.';
                if (true) { // TODO: Group debug only
                    $templateVariables['detailed_error'] = $e->getMessage();
                }
            }

            return $this->render('admin/import-edit-preview.html.twig', $templateVariables);
        } else {
            return $this->redirectToRoute('admin_import_edit_properties', ['id' => $import->getId()]);
        }
    }
}
