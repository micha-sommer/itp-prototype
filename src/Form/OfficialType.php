<?php

namespace App\Form;

use App\Entity\Official;
use App\Entity\Enum\GenderEnum;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class OfficialType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('first_name', TextType::class, ['attr'=> ['class' => 'hidden-first_name']])
            ->add('last_name', TextType::class, ['attr'=> ['class' => 'hidden-last_name']])
            ->add('role', ChoiceType::class, [
                'choices' => [
                    'trainer' => 'trainer',
                    'physio/psychotherapist' => 'physio/psychotherapist',
                    'referee' => 'referee',
                    'others' => 'others'
                ]
            ])
            ->add('gender', ChoiceType::class, [
                'choices' => [
                    'male' => 'male',
                    'female' => 'female'
                ]
            ])
            ->add('itc', ChoiceType::class, [
                'choices' => [
                    'no' => 'no',
                    'su-tu' => 'su-tu',
                    'su-we' => 'su-we'
                ]
            ])
            ->add('friday', CheckboxType::class, ['required' => false, 'label' => false])
            ->add('saturday', CheckboxType::class, ['required' => false, 'label' => false])
            ->add('comment', HiddenType::class, ['attr'=> ['class' => 'hidden-comment']]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Official::class
        ]);
    }
}
