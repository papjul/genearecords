<?php

declare(strict_types=1);

namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Regex;

/**
 * Class SearchFormType.
 */
class SearchFormType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->setMethod('GET')
            ->add('surname', TextType::class, [
                'required' => true,
                'constraints' => [new NotBlank(), new Length(['min' => 3])]
            ])
            ->add('given_names', TextType::class, [
                'required' => false,
                'constraints' => [new Length(['min' => 3])]
            ])
            ->add('location', TextType::class, [
                'required' => false,
                'attr' => ['placeholder' => 'City, Province OR Country']
            ])
            ->add('year', TextType::class, [
                'required' => false,
                'constraints' => [new Regex(['pattern' => '/(^((1[0-9]{3})|(20[0-9]{2}))$)|(^(((1[0-9]{3})|(20[0-9]{2}))-((1[0-9]{3})|(20[0-9]{2})))$)/'])],
                'attr' => ['placeholder' => '1810-1812 or 1811']
            ]);
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver): void
    {

    }
}
