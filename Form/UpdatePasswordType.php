<?php

namespace Cyve\PasswordManagerBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints\NotBlank;

class UpdatePasswordType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('password', RepeatedType::class, [
                'type' => PasswordType::class,
                'first_options'  => ['label' => 'Nouveau mot de passe'],
                'second_options' => ['label' => 'Répéter le mot de passe'],
                'invalid_message' => 'Les valeurs ne correspondent pas',
                'constraints' => [
                    new NotBlank(['message' => 'Ce champ ne doit pas être vide']),
                ],
            ])
        ;
    }
}
