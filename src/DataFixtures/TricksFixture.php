<?php


namespace App\DataFixtures;


use App\Entity\Group;
use App\Entity\Trick;
use App\Entity\User;
use App\Entity\VideoType;
use App\Enum\GroupEnum;
use App\Enum\TricksEnum;
use App\Manager\TrickManager;
use DateTime;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\String\Slugger\SluggerInterface;

class TricksFixture extends \Doctrine\Bundle\FixturesBundle\Fixture
{
    private ?SluggerInterface $slugger = null;
    private ?TrickManager $trickManager = null;

    public function __construct(SluggerInterface $slugger, TrickManager $trickManager)
    {
        $this->slugger = $slugger;
        $this->trickManager = $trickManager;
    }

    /**
     * @inheritDoc
     */
    public function load(ObjectManager $manager)
    {
        $user = new User();
        $user->setCreatedat(new \DateTime());
        $user->setEmail('admin@snowtricks.com');
        $user->setIsVerified(true);
        $user->setUsername('admin');
        $user->setSlug($this->slugger->slug($user->getUsername()));
        $user->setPassword('$argon2id$v=19$m=65536,t=4,p=1$6m6NOZOIYMDCJj9ybkG0UQ$FZaFSMKfxBfnCQsolw0h6fSFQPhdHHpq+U2wLUHVeEM');

        $manager->persist($user);

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
            $trick->setSlug($this->slugger->slug($trick->getName()));
            $trick->setCreatedat(new DateTime);

            $this->trickManager->createTrickAudit($trick, $user, true);
            $manager->persist($trick);
        }

        $videoType = new VideoType();
        $videoType->setName('youtube');
        $videoType->setCreatedat(new DateTime);
        $manager->persist($videoType);

        $manager->flush();
    }
}