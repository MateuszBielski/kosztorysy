<?php

namespace App\Form;

use App\Entity\Kosztorys;
use App\Entity\PriceList;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\MoneyType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class KosztorysType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder

            ->add('poczatkowaListaCen',EntityType::class,[
            'class' => PriceList::class,
            'choice_label' => function(PriceList $pl){return $pl->getName();},
            ])
            ->add('roboczogodzina', MoneyType::class, [
                'divisor' => 100,
                'currency' => 'PLN'
            ])
            
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Kosztorys::class,
        ]);
    }
}
