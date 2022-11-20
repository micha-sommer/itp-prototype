<?php

namespace App\Form;

use App\Entity\Contestant;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ContestantType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $yearChoices = [];
        $year = $options['year'];
        $yearBegin = $year - 20;
        $yearEnd = $year - 15;
        for ($i = $yearBegin; $i <= $yearEnd; $i++) {
            $yearChoices[$i] = "" . $i;
        }

        $builder
            ->add('firstName', TextType::class)
            ->add('lastName', TextType::class)
            ->add('year', ChoiceType::class, ['choices' => $yearChoices])
            ->add('ageCategory', ChoiceType::class, [
                'choices' => [
                    'participants.contestants.data.age-category.cadet' => 'cadet',
                    'participants.contestants.data.age-category.junior' => 'junior',
                ]
            ])
            ->add('weightCategory', ChoiceType::class, [
                'choices' => [
                    '-40' => '-40',
                    '-44' => '-44',
                    '-48' => '-48',
                    '-52' => '-52',
                    '-57' => '-57',
                    '-63' => '-63',
                    '-70' => '-70',
                    '+70' => '+70',
                    '-78' => '-78',
                    '+78' => '+78',
                    'participants.contestants.data.camp-only' => 'camp_only',
                ]
            ])
            ->add('itcSelection', ChoiceType::class, [
                'choices' => [
                    'participants.contestants.data.itc-selection.none' => 'none',
                    'participants.contestants.data.itc-selection.pack-A' => 'pack-A',
                    'participants.contestants.data.itc-selection.pack-B' => 'pack-B',
                    'participants.contestants.data.itc-selection.pack-C' => 'pack-C',
                    'participants.contestants.data.itc-selection.1-day' => '1-day',
                    'participants.contestants.data.itc-selection.2-day' => '2-day',
                    'participants.contestants.data.itc-selection.3-day' => '3-day',
                ]
            ])
            ->add('comment', HiddenType::class, ['attr'=> ['class' => 'hidden-comment']]);
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Contestant::class,
            'year' => null,
        ]);

        $resolver->setAllowedTypes('year', 'int');
    }
}
