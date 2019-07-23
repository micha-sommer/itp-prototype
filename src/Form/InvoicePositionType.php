<?php


namespace App\Form;


use App\Entity\InvoiceItem;
use App\Entity\InvoicePosition;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\CallbackTransformer;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class InvoicePositionType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('item', EntityType::class, ['class' => InvoiceItem::class, 'choice_label' => 'description'])
            ->add('multiplier', NumberType::class)
            ->add('is_add', ChoiceType::class, ['choices' => ['+' => true, '-' => false], 'label' => false])
            ->add('total_euro', NumberType::class, ['scale' => 2]);

        $builder->get('total_euro')->addModelTransformer(new CallbackTransformer(

            static function ($number)
            {
                return $number/100;
            },

            static function ($decimal)
            {
                return $decimal*100;
            }

            ));
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => InvoicePosition::class,
        ]);
    }
}