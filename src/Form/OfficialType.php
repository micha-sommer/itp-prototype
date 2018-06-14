<?php

namespace App\Form;

use App\Entity\Official;
use App\Entity\Enum\GenderEnum;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class OfficialType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('id', TextType::class, ['disabled' => true])
            ->add('first_name', TextType::class)
            ->add('last_name', TextType::class)
            ->add('role', TextType::class)
            ->add('gender', ChoiceType::class, [
                'choices' => [
                    'male' => 'male',
                    'female' => 'female'
                ]
            ])
            ->add('itc', CheckboxType::class, [ 'required' => false])
            ->add('friday', CheckboxType::class, [ 'required' => false])
            ->add('saturday', CheckboxType::class, [ 'required' => false]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Official::class
        ]);
    }
}
