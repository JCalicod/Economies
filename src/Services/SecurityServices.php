<?php
/**
 * Created by PhpStorm.
 * User: Gianni GIUDICE
 * Date: 25/11/2019
 * Time: 19:39
 */

namespace App\Services;


use App\Entity\Account;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class SecurityServices {
    private $encoder;
    private $entityManager;

    public function __construct(UserPasswordEncoderInterface $encoder, EntityManagerInterface $entityManager) {
        $this->encoder = $encoder;
        $this->entityManager = $entityManager;
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

    /**
     * @param User $user
     */
    public function createUser(User $user) {
        $encodedPassword = $this->encoder->encodePassword($user, $user->getPassword());
        $user->setPassword($encodedPassword);
        $user->setRoles(["ROLE_USER"]);
        $this->entityManager->persist($user);
        $this->entityManager->flush();
    }

    /**
     * @param User $user
     */
    public function createFirstAccount(User $user) {
        $account = new Account();
        $account->setAmount(0);
        $account->setColor('blue');
        $account->setDescription('Compte de dépôt ou compte chèque.');
        $account->setTitle('Compte courant');
        $account->setOwner($user);

        $this->entityManager->persist($account);
        $this->entityManager->flush();
    }
}