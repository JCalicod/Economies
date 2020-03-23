<?php
/**
 * Created by PhpStorm.
 * User: Gianni GIUDICE
 * Date: 03/11/2019
 * Time: 23:02
 */

namespace App\Form\Type\Manager;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;

class EditAccountType extends AbstractType {
    public function buildForm(FormBuilderInterface $builder, array $options) {
        $builder
            ->add('title', TextType::class, ['attr' => ['placeholder' => 'Nom du compte']])
            ->add('description', TextType::class, ['attr' => ['placeholder' => 'Courte description']])
            ->add('amount', IntegerType::class, ['attr' => ['placeholder' => 'Somme', 'min' => 0, 'max' => 10000000, 'step' => 0.5, 'type' => 'number']])
            ->add('color', ChoiceType::class, [
                'choices' => [
                    'Couleur' => null,
                    'Basique' => 'blue',
                    'Rouge' => 'red',
                    'Orange' => 'orange',
                    'Jaune' => 'yellow',
                    'Vert' => 'green',
                    'Violet' => 'purple'
                ]
            ])
            ->add('save', SubmitType::class, ['label' => 'Valider'])
        ;
    }
}