<?php

namespace App\DataFixtures;

use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;

class UserFixture extends Fixture {
    public function load(ObjectManager $manager) {
        $user = new User();
        $user->setEmail('gianni.giudice@lacatholille.fr');
        $user->setPassword('$argon2i$v=19$m=65536,t=4,p=1$UmtMODJOVEtVWWh3eDNObQ$fKPcxsUhp6fPxbKOSSMBEdWHnHZQ2znCNU4olqMCCQc');
        $user->setRoles(['ROLE_USER']);
        $manager->persist($user);

        $manager->flush();
    }
}
