<?php

namespace App\Form;

use App\Entity\Registration;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CountryType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\TelType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class RegistrationEditFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('email', EmailType::class, ['disabled' => true])
            ->add('club', TextType::class)
            ->add('country', CountryType::class, [
                'preferred_choices' => array('DE', 'UK', 'NL', 'KZ', 'AU', 'BR', 'JP', 'CH', 'SE', 'AT', 'BE', 'CA'),
            ])
            ->add('firstName', TextType::class)
            ->add('lastName', TextType::class)
            ->add('telephone', TelType::class, ['required' => false])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Registration::class,
        ]);
    }
}
