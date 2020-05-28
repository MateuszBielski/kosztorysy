<?php

namespace App\Form;

use App\Entity\CostItem;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class CostItemType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('description')
            ->add('subDescription')
            ->add('myNumber')
            ->add('unit')
            ->add('survey')
            ->add('myTable')
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => CostItem::class,
        ]);
    }
}
