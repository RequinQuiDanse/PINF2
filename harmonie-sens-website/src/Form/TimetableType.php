<?php

namespace App\Form;

use App\Entity\Timetable;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\TimeType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class TimetableType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('dayOfWeek', ChoiceType::class, [
                'label' => 'Jour de la semaine',
                'choices' => [
                    'Lundi' => 'lundi',
                    'Mardi' => 'mardi',
                    'Mercredi' => 'mercredi',
                    'Jeudi' => 'jeudi',
                    'Vendredi' => 'vendredi',
                    'Samedi' => 'samedi',
                    'Dimanche' => 'dimanche',
                ]
            ])
            ->add('startTime', TextType::class, [
                'label' => 'Heure de début',
                'attr' => ['placeholder' => '09:00']
            ])
            ->add('endTime', TextType::class, [
                'label' => 'Heure de fin',
                'attr' => ['placeholder' => '17:00']
            ])
            ->add('isAvailable', CheckboxType::class, [
                'label' => 'Disponible',
                'required' => false
            ])
            ->add('notes', TextType::class, [
                'label' => 'Notes',
                'required' => false
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Timetable::class,
        ]);
    }
}
