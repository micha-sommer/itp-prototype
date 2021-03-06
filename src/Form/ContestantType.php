<?php

namespace App\Form;

use App\Entity\Contestant;
use App\Enum\AgeCategoryEnum;
use App\Enum\ITCEnum;
use App\Enum\WeightCategoryEnum;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ContestantType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('first_name', TextType::class, ['attr'=> ['class' => 'hidden-first_name']])
            ->add('last_name', TextType::class, ['attr'=> ['class' => 'hidden-last_name']])
            ->add('year', ChoiceType::class, [
                'choices' => [
                    2000 => '2000',
                    2001 => '2001',
                    2002 => '2002',
                    2003 => '2003',
                    2004 => '2004',
                    2005 => '2005',
                ]
            ])
            ->add('weight_category', ChoiceType::class, [
                'choices' => WeightCategoryEnum::asArray()
            ])
            ->add('age_category', ChoiceType::class, [
                'choices' => AgeCategoryEnum::asArray()
            ])
            ->add('itc', ChoiceType::class, [
                'choices' => ITCEnum::asArray()
            ])
            ->add('friday', CheckboxType::class, ['required' => false, 'label' => false])
            ->add('saturday', CheckboxType::class, ['required' => false, 'label' => false])
            ->add('comment', HiddenType::class, ['attr'=> ['class' => 'hidden-comment']]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Contestant::class,
            'error_bubbling' => false,
        ]);
    }
}
