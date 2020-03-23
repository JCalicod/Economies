<?php
/**
 * Created by PhpStorm.
 * User: Gianni GIUDICE
 * Date: 03/11/2019
 * Time: 23:02
 */

namespace App\Form\Type\Manager;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;

class DeleteAccountType extends AbstractType {
    public function buildForm(FormBuilderInterface $builder, array $options) {
        $builder
            ->add('save', SubmitType::class, ['label' => 'Confirmer la suppression'])
        ;
    }
}