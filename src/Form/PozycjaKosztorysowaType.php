<?php

namespace App\Form;

use App\Entity\Kosztorys;
use App\Entity\PozycjaKosztorysowa;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class PozycjaKosztorysowaType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('obmiar')
            ->add('kosztorys',EntityType::class,[
                'class' => Kosztorys::class,
                'choice_label' => function(Kosztorys $ko){return $ko->getId();},
                ])
            // ->add('podstawaNormowa')
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => PozycjaKosztorysowa::class,
        ]);
    }
}
