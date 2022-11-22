<?php

namespace App\Form;

use App\Entity\Invoice;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\CallbackTransformer;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class InvoiceType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('subId', TextType::class, ['required' => false])
            ->add('name', TextType::class)
            ->add('invoiceAddress', TextareaType::class, ['required' => false])
            ->add('totalInHundreds', NumberType::class, ['scale' => 2, 'grouping' => true])
            ->add('invoicePositions', CollectionType::class, [
                'entry_type' => InvoicePositionType::class,
                'entry_options' => ['label' => false],
                'allow_add' => true,
                'allow_delete' => true,
                'by_reference' => false,
            ]);

        $moneyTransformer = new CallbackTransformer(
            static function ($number) {
                return $number / 100;
            },
            static function ($decimal) {
                return (int)($decimal * 100);
            }
        );

        $builder->get('totalInHundreds')->addModelTransformer($moneyTransformer);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Invoice::class,
        ]);
    }
}
