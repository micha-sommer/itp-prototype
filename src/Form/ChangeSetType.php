<?php
/**
 * Created by PhpStorm.
 * User: mremm
 * Date: 31.10.2018
 * Time: 16:44
 */

namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ChangeSetType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('from_date', DateTimeType::class, [
                'date_widget' => 'single_text',
                'data' => (new \DateTime())->sub(new \DateInterval('P1D')),
            ])
            ->add('to_date', DateTimeType::class, [
                'date_widget' => 'single_text',
                'data' => new \DateTime(),
            ]);
    }

    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([]);
    }
}