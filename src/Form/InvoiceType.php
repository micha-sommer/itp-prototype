<?php
/**
 * Created by PhpStorm.
 * User: mremm
 * Date: 10.06.2018
 * Time: 20:14
 */

namespace App\Form;


use App\Entity\Invoice;
use App\Entity\InvoicePositionsList;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class InvoiceType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options) : void
    {
        $builder
            ->add('invoice_address', TextareaType::class, ['required' => false])
            ->add('name', TextType::class, ['required' => true])
            ->add('invoicePositions', CollectionType::class, [
                'entry_type' => InvoicePositionType::class,
                'entry_options' => ['label' => false],
                'allow_add' => true,
                'allow_delete' => true,
                'by_reference' => false,
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver) : void
    {
        $resolver->setDefaults([
            'data_class' => Invoice::class
        ]);
    }
}