<?php

namespace App\Form;

use App\Entity\Profile;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ProfileType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('firstName', TextType::class, [
                'label' => 'First Name',
                'required' => true,
            ])
            ->add('lastName', TextType::class, [
                'label' => 'Last Name',
                'required' => true,
            ])
            ->add('bio', TextareaType::class, [
                'label' => 'Bio',
                'required' => false,
            ])
            ->add('phoneNumber', TextType::class, [
                'label' => 'Phone Number',
                'required' => false,
            ])
            ->add('address', TextType::class, [
                'label' => 'Address',
                'required' => false,
            ])
            ->add('city', TextType::class, [
                'label' => 'City',
                'required' => false,
            ])
            ->add('country', TextType::class, [
                'label' => 'Country',
                'required' => false,
            ])
            ->add('userType', ChoiceType::class, [
                'label' => 'User Type',
                'choices' => [
                    'Student' => 'student',
                    'Tutor' => 'tutor',
                ],
                'required' => true,
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Profile::class,
        ]);
    }
} 