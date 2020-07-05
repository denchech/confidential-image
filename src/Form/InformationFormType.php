<?php

namespace App\Form;

use App\Entity\Image;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class InformationFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('text', TextareaType::class, [
                'help' => 'Insert information you\'d like to convert',
                'label' => 'Information',
            ])
            ->add('maxOpeningsNumber', IntegerType::class, [
                'label' => 'Max number of openings',
                'required' => false,
                'help' => 'Keep it blank if you don\'t want to set a number of openings',
            ])
            ->add('expiresAt', DateType::class, [
                'widget' => 'single_text',
                'data' => new \DateTime("+7 days"),
                'help' => 'Set the future date, but no more than a year',
            ]);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Image::class,
        ]);
    }
}
