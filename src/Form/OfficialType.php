<?php

namespace App\Form;

use App\Entity\Official;
use Symfony\Component\Form\AbstractType;
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
            ->add('firstName', TextType::class)
            ->add('lastName', TextType::class)
            ->add('role', ChoiceType::class, [
                'choices' => [
                    'participants.officials.data.role.trainer' => 'trainer',
                    'participants.officials.data.role.physio' => 'physio',
                    'participants.officials.data.role.psycho' => 'psycho',
                    'participants.officials.data.role.referee' => 'referee',
                    'participants.officials.data.role.photo' => 'photo',
                    'participants.officials.data.role.other' => 'other',
                ]
            ])
            ->add('gender', ChoiceType::class, [
                'choices' => [
                    'participants.officials.data.gender.male' => 'male',
                    'participants.officials.data.gender.female' => 'female',
                    'participants.officials.data.gender.divers' => 'divers',
                ]
            ])
            ->add('itcSelection', ChoiceType::class, [
                'choices' => [
                    'participants.contestants.data.itc-selection.none' => 'none',
                    'participants.contestants.data.itc-selection.pack-A' => 'pack-A',
                    'participants.contestants.data.itc-selection.pack-B' => 'pack-B',
                    'participants.contestants.data.itc-selection.pack-C' => 'pack-C',
                    'participants.contestants.data.itc-selection.pack-D' => 'pack-D',
                ]
            ])
            ->add('comment', HiddenType::class, ['attr' => ['class' => 'hidden-comment']]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Official::class,
        ]);
    }
}
