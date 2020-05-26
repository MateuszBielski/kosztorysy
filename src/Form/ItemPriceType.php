<?php

namespace App\Form;

use App\Entity\ItemPrice;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ItemPriceType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('value')
            ->add('groupNumber')
            ->add('priceValue')
            ->add('nameAndUnit')
            ->add('priceList')
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => ItemPrice::class,
        ]);
    }
}
