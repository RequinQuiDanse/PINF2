<?php

namespace App\Form;

use App\Entity\Message;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\TelType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints as Assert;

class ContactType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('firstName', TextType::class, [
                'label' => 'Prénom',
                'attr' => ['placeholder' => 'Votre prénom'],
                'constraints' => [
                    new Assert\NotBlank(['message' => 'Le prénom est obligatoire']),
                    new Assert\Length(['min' => 2, 'max' => 255])
                ]
            ])
            ->add('lastName', TextType::class, [
                'label' => 'Nom',
                'attr' => ['placeholder' => 'Votre nom'],
                'constraints' => [
                    new Assert\NotBlank(['message' => 'Le nom est obligatoire']),
                    new Assert\Length(['min' => 2, 'max' => 255])
                ]
            ])
            ->add('email', EmailType::class, [
                'label' => 'Email',
                'attr' => ['placeholder' => 'votre.email@exemple.fr'],
                'constraints' => [
                    new Assert\NotBlank(['message' => 'L\'email est obligatoire']),
                    new Assert\Email(['message' => 'Veuillez entrer un email valide'])
                ]
            ])
            ->add('phone', TelType::class, [
                'label' => 'Téléphone',
                'required' => false,
                'attr' => ['placeholder' => '06 12 34 56 78'],
                'constraints' => [
                    new Assert\Length([
                        'max' => 20,
                        'maxMessage' => 'Le numéro de téléphone ne peut pas dépasser {{ limit }} caractères'
                    ]),
                    new Assert\Regex([
                        'pattern' => '/^[\d\s\+\-\.\(\)]*$/',
                        'message' => 'Le numéro de téléphone contient des caractères invalides'
                    ])
                ]
            ])
            ->add('subject', TextType::class, [
                'label' => 'Sujet',
                'attr' => ['placeholder' => 'Objet de votre message'],
                'constraints' => [
                    new Assert\NotBlank(['message' => 'Le sujet est obligatoire']),
                    new Assert\Length(['min' => 3, 'max' => 255])
                ]
            ])
            ->add('message', TextareaType::class, [
                'label' => 'Message',
                'attr' => [
                    'placeholder' => 'Votre message',
                    'rows' => 8
                ],
                'constraints' => [
                    new Assert\NotBlank(['message' => 'Le message est obligatoire']),
                    new Assert\Length(['min' => 10])
                ]
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Message::class,
        ]);
    }
}
