<?php


namespace App\Manager;


use App\Entity\Comment;
use App\Entity\Trick;
use DateTime;
use Doctrine\ORM\EntityManagerInterface;

class CommentManager
{
    private ?EntityManagerInterface $em;
    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }

    /**
     * @throws \Doctrine\ORM\OptimisticLockException
     * @throws \Doctrine\ORM\ORMException
     */
    public function saveComment(Comment $comment, $user, int $trickId = null, int $parentId = null)
    {
        $trick = $this->em->getRepository(Trick::class)->find($trickId);
        $parent = $this->em->getRepository(Comment::class)->find($parentId);
        $comment->setStatus(true);
        $comment->setTrick($trick);
        $comment->setParent($parent);
        $comment->setCreatedat(new DateTime);
        $comment->setUser($user);
        $this->em->persist($comment);
        $this->em->flush();
        return $comment;
    }
}