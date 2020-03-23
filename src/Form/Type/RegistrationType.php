<?php
/**
 * Created by PhpStorm.
 * User: Gianni GIUDICE
 * Date: 03/11/2019
 * Time: 23:02
 */

namespace App\Form\Type;


use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;

class RegistrationType extends AbstractType {
    public function buildForm(FormBuilderInterface $builder, array $options) {
        $builder
            ->add('email', EmailType::class, ['attr' => ['placeholder' => 'Adresse mail']])
            ->add('password', RepeatedType::class, [
                'type' => PasswordType::class,
                'options' => ['attr' => ['class' => 'password-field']],
                'required' => true,
                'first_options'  => ['label' => 'Password', 'attr' => ['placeholder' => 'Mot de passe']],
                'second_options' => ['label' => 'Repeat Password', 'attr' => ['placeholder' => 'Confirmation']]
            ])
            ->add('save', SubmitType::class, ['label' => 'Valider'])
        ;
    }
}