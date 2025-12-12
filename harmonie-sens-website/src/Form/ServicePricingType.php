<?php

namespace App\Form;

use App\Entity\ServicePricing;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\MoneyType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ServicePricingType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('serviceName', TextType::class, ['label' => 'Nom du service'])
            ->add('description', TextareaType::class, [
                'label' => 'Description',
                'required' => false,
                'attr' => ['rows' => 4]
            ])
            ->add('unitPrice', MoneyType::class, [
                'label' => 'Prix unitaire',
                'required' => false,
                'currency' => 'EUR'
            ])
            ->add('pricingUnit', ChoiceType::class, [
                'label' => 'Unité de tarification',
                'required' => false,
                'choices' => [
                    'Jour' => 'jour',
                    'Heure' => 'heure',
                    'Mission' => 'mission',
                    'Forfait' => 'forfait',
                ],
                'placeholder' => 'Choisir une unité'
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
            'data_class' => ServicePricing::class,
        ]);
    }
}
