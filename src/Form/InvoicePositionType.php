<?php


namespace App\Form;


use App\Entity\InvoiceItem;
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
            ->add('price', NumberType::class, ['scale' => 2, 'grouping' => true, 'attr' => ['class' => 'hidden-price']])
            ->add('multiplier', NumberType::class, ['attr' => ['class' => 'hidden-multiplier']])
            ->add('total', NumberType::class, ['scale' => 2, 'grouping' => true, 'attr' => ['class' => 'hidden-total','style' => 'text-align: right']]);

        $moneyTransformer = new CallbackTransformer(

            static function ($number)
            {
                return $number/100;
            },

            static function ($decimal)
            {
                return (int)($decimal*100);
            }

        );

        $builder->get('price')->addModelTransformer($moneyTransformer);
        $builder->get('multiplier')->addModelTransformer($moneyTransformer);
        $builder->get('total')->addModelTransformer($moneyTransformer);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => InvoicePosition::class,
        ]);
    }
}