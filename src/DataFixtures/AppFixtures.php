<?php

namespace App\DataFixtures;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use App\Entity\User;

class AppFixtures extends Fixture
{
    public function load(ObjectManager $manager)
    {
        $user = new User();
        $user->setCreatedat(new \DateTime());
        $user->setEmail('etienne.doux@gmail.com');
        $user->setIsVerified(true);
        $user->setUsername('tavrin');
        $user->setPassword('$argon2id$v=19$m=65536,t=4,p=1$6m6NOZOIYMDCJj9ybkG0UQ$FZaFSMKfxBfnCQsolw0h6fSFQPhdHHpq+U2wLUHVeEM');

        $manager->persist($user);
        $manager->flush();


        // $product = new Product();
        // $manager->persist($product);

        $manager->flush();
    }
}
