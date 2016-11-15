<?php

namespace AppBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;

class BilletType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array                $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('nom', TextType::class, array(
                      'label' => 'Nom :', ))
            ->add('prenom', TextType::class, array(
                      'label' => 'Prénom :', ))
            ->add('reduction', CheckboxType::class, array(
                      'label' => 'Tarif réduit',
                      'required' => false, ))
            ->add('dateNaissance', DateType::class, array(
                      'label' => 'Date de naissance :',
                      'years' => range(1916, 2016),
            ))
            ->add('pays', EntityType::class, array(
                'class' => 'AppBundle\Entity\Pays',
                'label' => 'Pays',
                'choice_label' => 'name',
                'placeholder' => 'Choisiser un pays'
            ))
        ;
    }

    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'AppBundle\Entity\Billet',
        ));
    }
}
