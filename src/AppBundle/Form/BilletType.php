<?php

namespace AppBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

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