<?php
/**
 * Created by PhpStorm.
 * User: Gianni GIUDICE
 * Date: 11/11/2019
 * Time: 19:25
 */

namespace App\Form\Type\Manager;


use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Security\Core\Security;

class SettingsType extends AbstractType {
    private $security;

    public function __construct(Security $security) {
        $this->security = $security;
    }

    public function buildForm(FormBuilderInterface $builder, array $options) {

        $builder
            ->add('username', EmailType::class, ['attr' => ['placeholder' => 'Adresse mail', 'value' => $this->security->getUser()->getUsername()]])
            ->add('old_password', PasswordType::class, ['attr' => ['placeholder' => 'Mot de passe actuel']])
            ->add('password', PasswordType::class, ['attr' => ['placeholder' => 'Nouveau mot de passe']])
            ->add('save', SubmitType::class, ['label' => 'Valider'])
        ;
    }

}