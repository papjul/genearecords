<?php

declare(strict_types=1);

namespace App\Controller;

use App\Entity\Record;
use App\Form\SearchFormType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/{_locale<%app.supported_locales%>}/search')]
class SearchController extends AbstractController
{
    #[Route('', name: 'search_advanced', methods: ["GET"])]
    public function advanced(Request $request, FormFactoryInterface $formFactory, EntityManagerInterface $entityManager): Response
    {
        $form = $formFactory->createBuilder(SearchFormType::class)
            ->setAction($this->generateUrl('search_results'))
            ->getForm();
        $form->handleRequest($request);
        return $this->render('search/advanced.html.twig', [
            'form' => $form->createView()
        ]);
    }

    #[Route('/results', name: 'search_results', methods: ["GET", "POST"])]
    public function results(Request $request, FormFactoryInterface $formFactory, EntityManagerInterface $entityManager): Response
    {
        $form = $formFactory->createBuilder(SearchFormType::class)->getForm();

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $data = $form->getData();

            // TODO: Paginate results
            $records = $entityManager->getRepository(Record::class)->manualSearch($data);

            // TODO: If no result found, find approximate Levenshtein names

            return $this->render('search/results.html.twig', [
                'records' => $records
            ]);
        }

        // FIXME: Don't show index page here, make a forbidden page
        return $this->render('index.html.twig', [
            'form' => $form->createView()
        ]);
    }
}
