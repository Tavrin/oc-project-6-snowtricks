<?php


namespace App\Manager;


use App\Entity\Comment;
use App\Entity\Trick;
use DateTime;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;

class CommentManager
{
    private ?EntityManagerInterface $em;

    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }

    /**
     * @param Comment $comment
     * @param $user
     * @param int|null $trickId
     * @param int|null $parentId
     * @return Comment
     */
    public function saveComment(Comment $comment, $user, int $trickId = null, int $parentId = null): Comment
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

    public function getResponseContent(Request $request): ?array
    {
        $content = null;
        if (!empty($request->getContent())) {
            $content = $request->toArray();
        }
        if(isset($content['pid'])) {
            if ($this->em->getRepository(Comment::class)->find($content['pid'])) {
                $content['pid'] = (int)$content['pid'];
            } else {
                $content['pid'] = null;
            }
        } else {
            $content['pid'] = null;
        }

        if (isset($content['trickId'])) {
            if ($this->em->getRepository(Comment::class)->find($content['trickId'])) {
                $content['trickId'] = (int)$content['trickId'];
            } else {
                $content['trickId'] = null;
            }
        } else {
            $content['trickId'] = null;
        }

        return $content;
    }
}