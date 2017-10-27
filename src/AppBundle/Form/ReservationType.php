<?php

namespace AppBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Valid;

class ReservationType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array                $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('dateReservation', TextType::class)
            ->add('email', TextType::class)
            ->add('demiJournee', CheckboxType::class, array(
                        'label' => 'Demi journée* :',
                        'required' => false,
                        ))
            ->add('billets', CollectionType::class, array(
                           'entry_type' => BilletType::class,
                           'by_reference' => false,
                           'allow_add' => true,
                           'allow_delete' => true,
                           'label' => 'Billet',
                           'label_attr' => array('class' => 'col-xs-2'),
                            'constraints' => [ new Valid()]
                          ))
            ->add('save', SubmitType::class, array(
                          'label' => 'Valider',
            ))
        ;
    }

    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'AppBundle\Entity\Reservation'
        ));
    }
}
