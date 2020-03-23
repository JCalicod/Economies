<?php

namespace App\Repository;

use App\Entity\Account;
use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;

/**
 * @method Account|null find($id, $lockMode = null, $lockVersion = null)
 * @method Account|null findOneBy(array $criteria, array $orderBy = null)
 * @method Account[]    findAll()
 * @method Account[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class AccountRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Account::class);
    }

    /**
     * @param string $accountTitle
     * @param User $owner
     * @return mixed
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function findAccountNameWithOwner(string $accountTitle, User $owner) {
        return $this->createQueryBuilder('a')
            ->where('a.title = :accountTitle')
            ->setParameter('accountTitle', $accountTitle)
            ->andWhere('a.owner = :owner')
            ->setParameter('owner', $owner)
            ->getQuery()
            ->getOneOrNullResult()
            ;
    }

    /**
     * @param Account $account
     * @param User $owner
     * @return mixed
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function findAnotherAccountNameWithOwner(Account $account, User $owner) {
        return $this->createQueryBuilder('a')
            ->where('a.title = :accountTitle')
            ->setParameter('accountTitle', $account->getTitle())
            ->andWhere('a.owner = :owner')
            ->setParameter('owner', $owner)
            ->andWhere('a.id != :id')
            ->setParameter('id', $account->getId())
            ->getQuery()
            ->getOneOrNullResult()
            ;
    }

    /**
     * @param int $accountID
     * @param User $owner
     * @return mixed
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function findAllOtherAccounts(int $accountID, User $owner) {
        return $this->createQueryBuilder('a')
            ->where('a.owner = :owner')
            ->setParameter('owner', $owner)
            ->andWhere('a.id != :id')
            ->setParameter('id', $accountID)
            ->getQuery()
            ->getResult()
            ;
    }


    /*
    public function findOneBySomeField($value): ?Account
    {
        return $this->createQueryBuilder('a')
            ->andWhere('a.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
