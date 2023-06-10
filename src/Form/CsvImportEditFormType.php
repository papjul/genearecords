<?php

declare(strict_types=1);

namespace App\Form;

use App\Entity\Import;
use App\Entity\Location;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Class CsvImportFieldsFormType.
 */
class CsvImportEditFormType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->setMethod('POST')
            ->add('fields', CollectionType::class, [
                'entry_type' => CsvFieldFormType::class,
                'entry_options' => ['label' => false, 'entity_manager' => $options['entity_manager']],
                'allow_add' => false,
                'allow_delete' => false,
            ])
            ->add('fields_additional', CollectionType::class, [
                'entry_type' => CsvFieldAdditionalFormType::class,
                'entry_options' => ['label' => false, 'entity_manager' => $options['entity_manager']],
                'allow_add' => true,
                'allow_delete' => true,
                'by_reference' => false,
            ]);
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setRequired('entity_manager');
        $resolver->setDefaults(['data_class' => Import::class]);
    }
}
