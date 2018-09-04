<?php
/**
 * Created by PhpStorm.
 * User: mremm
 * Date: 30.08.2018
 * Time: 21:48
 */

namespace App\Form;


use App\Entity\Transport;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\TimeType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class TransportType extends AbstractType
{

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('date', DateType::class, [
                'years' => ['2019'],
                'months' => ['3'],
                'days' => range(20,28),
            ])
            ->add('time', TimeType::class)
            ->add('place', TextType::class, ['required' => false])
            ->add('information', TextType::class, ['required' => false])
            ->add('comment', TextareaType::class, ['required' => false])
        ;
    }

    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Transport::class,
        ]);
    }
}