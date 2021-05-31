<?php


namespace App\DataFixtures;


use App\Entity\Group;
use App\Entity\Trick;
use App\Enum\GroupEnum;
use App\Enum\TricksEnum;
use DateTime;
use Doctrine\Persistence\ObjectManager;

class TricksFixture extends \Doctrine\Bundle\FixturesBundle\Fixture
{

    /**
     * @inheritDoc
     */
    public function load(ObjectManager $manager)
    {
        $groups = [];
        foreach (GroupEnum::TRICK_GROUPS as $groupType) {
            $group = new Group();
            $group->setName($groupType['name']);
            $group->setDescription($groupType['description']);
            $group->setCreatedat(new DateTime);
            $manager->persist($group);
            $groups[$groupType['name']] = $group;
        }

        foreach (TricksEnum::TRICKS as $trickType) {
            $trick = new Trick();
            $trick->setName($trickType['name']);
            $trick->setDescription($trickType['description']);
            $trick->setTrickGroup($groups[$trickType['group']]);
            $trick->setCreatedat(new DateTime);
            $manager->persist($trick);
        }

        $manager->flush();
    }
}