<?php

namespace App\Form;

use App\Entity\Service;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\MoneyType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints as Assert;

class ServiceType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('name', TextType::class, [
                'label' => 'Nom du service',
                'attr' => ['class' => 'form-control'],
                'constraints' => [
                    new Assert\NotBlank(['message' => 'Le nom du service est obligatoire']),
                    new Assert\Length([
                        'max' => 255,
                        'maxMessage' => 'Le nom ne peut pas dépasser {{ limit }} caractères'
                    ]),
                ],
            ])
            ->add('slug', TextType::class, [
                'label' => 'Slug (URL)',
                'attr' => [
                    'class' => 'form-control',
                    'placeholder' => 'ex: direction-transition'
                ],
                'help' => 'Identifiant unique pour l\'URL (lettres minuscules, chiffres et tirets uniquement)',
                'constraints' => [
                    new Assert\NotBlank(['message' => 'Le slug est obligatoire']),
                    new Assert\Regex([
                        'pattern' => '/^[a-z0-9-]+$/',
                        'message' => 'Le slug ne peut contenir que des lettres minuscules, chiffres et tirets'
                    ]),
                ],
            ])
            ->add('description', TextareaType::class, [
                'label' => 'Description',
                'attr' => [
                    'class' => 'form-control',
                    'rows' => 3
                ],
                'required' => false,
            ])
            ->add('priceMin', MoneyType::class, [
                'label' => 'Prix minimum (€)',
                'attr' => ['class' => 'form-control'],
                'required' => false,
                'currency' => 'EUR',
                'divisor' => 1,
            ])
            ->add('priceMax', MoneyType::class, [
                'label' => 'Prix maximum (€)',
                'attr' => ['class' => 'form-control'],
                'required' => false,
                'currency' => 'EUR',
                'divisor' => 1,
            ])
            ->add('pricingUnit', TextType::class, [
                'label' => 'Unité de tarification',
                'attr' => [
                    'class' => 'form-control',
                    'placeholder' => 'ex: séance, journée, mission'
                ],
                'required' => false,
            ])
            ->add('pricingDetails', TextareaType::class, [
                'label' => 'Détails de la tarification',
                'attr' => [
                    'class' => 'form-control',
                    'rows' => 6,
                    'placeholder' => 'Détaillez les différentes formules et options tarifaires...'
                ],
                'required' => false,
                'help' => 'Vous pouvez utiliser des retours à la ligne et des puces (•) pour structurer l\'information',
            ])
            ->add('isActive', CheckboxType::class, [
                'label' => 'Service actif',
                'required' => false,
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Service::class,
        ]);
    }
}
