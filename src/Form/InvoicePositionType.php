<?php

namespace App\Form;

use App\Entity\InvoicePosition;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\CallbackTransformer;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class InvoicePositionType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('description', TextType::class)
            ->add('amountInHundreds', NumberType::class)
            ->add('priceInHundreds', NumberType::class, ['scale' => 2, 'grouping' => true])
            ->add('totalInHundreds', NumberType::class, ['scale' => 2, 'grouping' => true]);

        $moneyTransformer = new CallbackTransformer(
            static function ($number) {
                return $number / 100;
            },
            static function ($decimal) {
                return (int)($decimal * 100);
            }
        );

        $builder->get('amountInHundreds')->addModelTransformer($moneyTransformer);
        $builder->get('priceInHundreds')->addModelTransformer($moneyTransformer);
        $builder->get('totalInHundreds')->addModelTransformer($moneyTransformer);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => InvoicePosition::class,
        ]);
    }
}
