<?php
/**
 * Created by PhpStorm.
 * User: Gianni GIUDICE
 * Date: 26/11/2019
 * Time: 16:15
 */

namespace App\Services;


use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class SettingsServices {
    private $encoder;
    private $em;

    public function __construct(UserPasswordEncoderInterface $encoder, EntityManagerInterface $em) {
        $this->encoder = $encoder;
        $this->em = $em;
    }

    public function updateUserSettings($data, User $user) {
        $user->setEmail($data['username']);
        $user->setPassword($this->encoder->encodePassword($user, $data['password']));
        $this->em->flush();
    }

    /**
     * @param $form
     * @return array
     */
    public function getErrors($form) {
        $errors = array();
        foreach ($form->getErrors(true, true) as $error) {
            $propertyPath = str_replace('data.', '', $error->getCause()->getPropertyPath());
            $errors[$propertyPath] = $error->getMessage();
        }

        return $errors;
    }

}