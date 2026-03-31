<?php

namespace App\Form;

use App\Entity\Appointment;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\TelType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\TimeType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints as Assert;

class AppointmentType extends AbstractType
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
                'attr' => ['placeholder' => '06 12 34 56 78']
            ])
            ->add('organization', TextType::class, [
                'label' => 'Organisation / Établissement',
                'required' => false,
                'attr' => ['placeholder' => 'Nom de votre établissement']
            ])
            ->add('service', ChoiceType::class, [
                'label' => 'Service souhaité',
                'placeholder' => 'Choisissez un service',
                'choices' => [
                    'Direction de transition' => 'Direction de transition',
                    'Diagnostic & Audit' => 'Diagnostic & Audit',
                    'Accompagnement' => 'Accompagnement',
                    'Formations' => 'Formations',
                    'Autre' => 'Autre',
                ],
                'constraints' => [
                    new Assert\NotBlank(['message' => 'Veuillez choisir un service'])
                ]
            ])
            ->add('desiredDate', DateType::class, [
                'label' => 'Date souhaitée',
                'widget' => 'single_text',
                'constraints' => [
                    new Assert\NotBlank(['message' => 'La date est obligatoire']),
                    new Assert\GreaterThan(['value' => 'today', 'message' => 'La date doit être dans le futur'])
                ]
            ])
            ->add('desiredTime', ChoiceType::class, [
                'label' => 'Heure souhaitée',
                'placeholder' => 'Choisissez un créneau',
                'choices' => [
                    '09:00' => '09:00',
                    '09:30' => '09:30',
                    '10:00' => '10:00',
                    '10:30' => '10:30',
                    '11:00' => '11:00',
                    '11:30' => '11:30',
                    '14:00' => '14:00',
                    '14:30' => '14:30',
                    '15:00' => '15:00',
                    '15:30' => '15:30',
                    '16:00' => '16:00',
                    '16:30' => '16:30',
                    '17:00' => '17:00',
                ],
                'constraints' => [
                    new Assert\NotBlank(['message' => 'Veuillez choisir un créneau horaire'])
                ]
            ])
            ->add('notes', TextareaType::class, [
                'label' => 'Informations complémentaires',
                'required' => false,
                'attr' => [
                    'placeholder' => 'Décrivez brièvement l\'objet de votre rendez-vous...',
                    'rows' => 5
                ]
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Appointment::class,
        ]);
    }
}
