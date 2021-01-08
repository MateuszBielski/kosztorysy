<?php

namespace App\Form;

use App\Entity\ItemPrice;
use App\Entity\PriceList;
use App\Entity\Circulation\CirculationNameAndUnit;
use Symfony\Component\Form\AbstractType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;


class ItemPriceType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            // ->add('value')
            // ->add('groupNumber')
            ->add('priceValue')
            ->add('priceList',EntityType::class,[
                'class' => PriceList::class,
                'choice_label' => function(PriceList $pr){return $pr->getName();},
                'label' => 'lista cen'
                ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => ItemPrice::class,
        ]);
    }
}
