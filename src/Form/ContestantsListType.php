<?php
/**
 * Created by PhpStorm.
 * User: mremm
 * Date: 10.06.2018
 * Time: 20:14
 */

namespace App\Form;


use App\Entity\Registration;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ContestantsListType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options) : void
    {
        $builder
            ->add('contestants', CollectionType::class, [
                'entry_type' => ContestantType::class,
                'entry_options' => ['label' => false, 'year' =>  $options['year']],
                'allow_add' => true,
                'allow_delete' => true,
                'by_reference' => false,
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver) : void
    {
        $resolver->setDefaults([
            'data_class' => Registration::class,
            'year' => null,
        ]);

        $resolver->setAllowedTypes('year', 'int');
    }
}
