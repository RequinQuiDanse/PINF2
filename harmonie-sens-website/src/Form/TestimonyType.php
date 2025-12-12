<?php

namespace App\Form;

use App\Entity\Testimony;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints as Assert;

class TestimonyType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('clientName', TextType::class, [
                'label' => 'Nom du client',
                'attr' => ['placeholder' => 'Nom du client']
            ])
            ->add('position', TextType::class, [
                'label' => 'Poste',
                'required' => false,
                'attr' => ['placeholder' => 'Directeur, Manager, etc.']
            ])
            ->add('organization', TextType::class, [
                'label' => 'Organisation',
                'attr' => ['placeholder' => 'Nom de l\'organisation']
            ])
            ->add('content', TextareaType::class, [
                'label' => 'Témoignage',
                'attr' => ['rows' => 6, 'placeholder' => 'Le témoignage du client']
            ])
            ->add('rating', ChoiceType::class, [
                'label' => 'Note',
                'choices' => [
                    '⭐ 1 étoile' => 1,
                    '⭐⭐ 2 étoiles' => 2,
                    '⭐⭐⭐ 3 étoiles' => 3,
                    '⭐⭐⭐⭐ 4 étoiles' => 4,
                    '⭐⭐⭐⭐⭐ 5 étoiles' => 5,
                ],
                'expanded' => false,
            ])
            ->add('imageFile', FileType::class, [
                'label' => 'Photo (optionnel)',
                'mapped' => false,
                'required' => false,
                'constraints' => [
                    new Assert\File([
                        'maxSize' => '2M',
                        'mimeTypes' => ['image/jpeg', 'image/png', 'image/webp'],
                        'mimeTypesMessage' => 'Veuillez uploader une image valide (JPG, PNG, WebP)'
                    ])
                ],
                'attr' => ['accept' => 'image/*']
            ])
            ->add('displayOrder', IntegerType::class, [
                'label' => 'Ordre d\'affichage',
                'attr' => ['min' => 0],
                'data' => $options['data']->getDisplayOrder() ?? 0
            ])
            ->add('isPublished', CheckboxType::class, [
                'label' => 'Publié',
                'required' => false
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Testimony::class,
        ]);
    }
}
