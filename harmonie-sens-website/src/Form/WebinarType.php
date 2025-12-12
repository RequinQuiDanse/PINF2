<?php

namespace App\Form;

use App\Entity\Webinar;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\UrlType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class WebinarType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('name', TextType::class, ['label' => 'Nom du webinaire'])
            ->add('description', TextareaType::class, [
                'label' => 'Description',
                'attr' => ['rows' => 5]
            ])
            ->add('date', DateTimeType::class, [
                'label' => 'Date et heure',
                'widget' => 'single_text'
            ])
            ->add('link', UrlType::class, ['label' => 'Lien de connexion'])
            ->add('duration', IntegerType::class, [
                'label' => 'Durée (minutes)',
                'required' => false
            ])
            ->add('isActive', CheckboxType::class, [
                'label' => 'Actif',
                'required' => false
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Webinar::class,
        ]);
    }
}
