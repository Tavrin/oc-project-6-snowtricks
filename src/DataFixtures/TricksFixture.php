<?php


namespace App\DataFixtures;


use App\Entity\Trick;
use DateTime;
use Doctrine\Persistence\ObjectManager;

class TricksFixture extends \Doctrine\Bundle\FixturesBundle\Fixture
{

    /**
     * @inheritDoc
     */
    public function load(ObjectManager $manager)
    {
        for ($i = 0; $i < 10; $i++) {
            $trick = new Trick();
            $trick->setName('trick '.$i);
            $trick->setDescription('trick description');
            $trick->setCreatedat(new DateTime);
            $manager->persist($trick);
        }

        $manager->flush();
    }
}