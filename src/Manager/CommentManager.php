<?php


namespace App\Manager;


use App\Entity\Comment;
use App\Entity\Trick;
use DateTime;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Form\FormInterface;
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
    public function saveComment(Comment $comment, $user, FormInterface $form): Comment
    {
        $trickId = null;
        $trick = null;
        if ($form->has('trickId')) {
            $trickId = $form->get('trickId')->getData();
        }
        $parentId = null;
        if ($form->has('parentId')) {
            $parentId = $form->get('parentId')->getData();
        }
        if (null !== $trickId) {
            $trick = $this->em->getRepository(Trick::class)->find($trickId);
        }
        if (null !== $parentId) {
            $parent = $this->em->getRepository(Comment::class)->find($parentId);
        }

        $comment->setStatus(true);
        $comment->setTrick($trick);
        if (isset($parent)) {
            $comment->setParent($parent);
        }
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
            if ($this->em->getRepository(Trick::class)->find($content['trickId'])) {
                $content['trickId'] = (int)$content['trickId'];
            } else {
                $content['trickId'] = null;
            }
        } else {
            $content['trickId'] = null;
        }

        return $content;
    }

    public function hydrateCommentArray($entities): array
    {
        $content = [];

        foreach ($entities as $key => $entity) {
            $content[$key]['id'] = $entity->getId();
            $content[$key]['content'] = $entity->getContent();
            $content[$key]['status'] = $entity->getStatus();
            $content[$key]['user'] = $entity->getUser()->getUsername();
            $content[$key]['createdAt'] = $entity->getCreatedAt()->format("d/m/Y");
        }

        return $content;
    }
}