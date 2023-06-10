<?php

declare(strict_types=1);

namespace App\Form;

use App\Entity\ImportField;
use App\Entity\Record;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Class CsvFieldFormType.
 */
class CsvFieldFormType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('number', HiddenType::class)
            ->add('database_field', ChoiceType::class, [
                'choices' => $options['entity_manager']->getRepository(Record::class)->getFormChoices(),
                'choice_translation_domain' => 'record_fields',
                'label' => false,
                'required' => false
            ]);
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setRequired('entity_manager');
        $resolver->setDefaults(['data_class' => ImportField::class]);
    }
}
