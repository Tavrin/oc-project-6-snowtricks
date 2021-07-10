<?php

namespace App\Entity;

use App\Repository\TrickRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

/**
 * @ORM\Entity(repositoryClass=TrickRepository::class)
 * @UniqueEntity("name", message="Cette figure existe déjà")
 * @UniqueEntity("slug", message="Cette figure existe déjà")
 */
class Trick
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255, unique=true)
     */
    private $name;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private $description;

    /**
     * @ORM\Column(type="datetime")
     */
    private $createdAt;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $updatedAt;

    /**
     * @ORM\ManyToOne(targetEntity=Group::class, inversedBy="tricks", fetch="EAGER")
     */
    private $trickGroup;

    /**
     * @ORM\OneToMany(targetEntity=TrickMedia::class, mappedBy="trick", orphanRemoval=true)
     */
    private $trickMedia;

    /**
     * @ORM\OneToMany(targetEntity=TrickModify::class, mappedBy="trick")
     */
    private $trickModifies;

    /**
     * @ORM\OneToMany(targetEntity=Comment::class, mappedBy="trick", cascade={"remove"})
     */
    private $comments;

    /**
     * @ORM\ManyToOne(targetEntity=Media::class)
     */
    private $mainMedia;

    /**
     * @ORM\Column(type="string", length=255, unique=true)
     */
    private $slug;

    /**
     * @ORM\OneToMany(targetEntity=Video::class, mappedBy="trick", orphanRemoval=true)
     */
    private $videos;

    public function __construct()
    {
        $this->trickMedia = new ArrayCollection();
        $this->trickModifies = new ArrayCollection();
        $this->comments = new ArrayCollection();
        $this->videos = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(?string $description): self
    {
        $this->description = $description;

        return $this;
    }

    public function getCreatedAt(): ?\DateTimeInterface
    {
        return $this->createdAt;
    }

    public function setCreatedAt(\DateTimeInterface $createdAt): self
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    public function getUpdatedAt(): ?\DateTimeInterface
    {
        return $this->updatedAt;
    }

    public function setUpdatedAt(?\DateTimeInterface $updatedAt): self
    {
        $this->updatedAt = $updatedAt;

        return $this;
    }

    public function getTrickGroup(): ?Group
    {
        return $this->trickGroup;
    }

    public function setTrickGroup(?Group $trickGroup): self
    {
        $this->trickGroup = $trickGroup;

        return $this;
    }

    /**
     * @return Collection|TrickMedia[]
     */
    public function getTrickMedias(): Collection
    {
        return $this->trickMedia;
    }


    /**
     * @param int $mediaId
     * @return TrickMedia|null
     */
    public function getTrickMedia(int $mediaId): ?TrickMedia
    {
        foreach ($this->trickMedia as $trickMedia) {
            if ($mediaId === $trickMedia->getMedia()->getId()) {
                return $trickMedia;
            }
        }

        return null;
    }

    public function addTrickMedia(TrickMedia $trickMedia): self
    {
        if (!$this->trickMedia->contains($trickMedia)) {
            $this->trickMedia[] = $trickMedia;
            $trickMedia->setMedia($this);
        }

        return $this;
    }

    /**
     * @param int $mediaId
     * @return bool
     */
    public function hasMedia(int $mediaId): bool
    {
        foreach ($this->trickMedia as $trickMedia) {
            if ($mediaId === $trickMedia->getMedia()->getId()) {
                return true;
            }
        }

        return false;
    }

    public function removeTrickMedia(TrickMedia $trickMedia): self
    {
        if ($this->trickMedia->removeElement($trickMedia)) {
            // set the owning side to null (unless already changed)
            if ($trickMedia->getMedia() === $this) {
                $trickMedia->setMedia(null);
            }
        }

        return $this;
    }

    public function removeTrickMediaFromMediaId(int $mediaId): self
    {
        foreach ($this->trickMedia as $trickMedia) {
            if ($mediaId === $trickMedia->getMedia()->getId()) {
                $this->trickMedia->removeElement($trickMedia);
                if ($trickMedia->getMedia() === $this) {
                    $trickMedia->setMedia(null);
                }

            }
        }

        return $this;
    }

    /**
     * @return Collection|TrickModify[]
     */
    public function getTrickModifies(): Collection
    {
        return $this->trickModifies;
    }

    public function addTrickModify(TrickModify $trickModify): self
    {
        if (!$this->trickModifies->contains($trickModify)) {
            $this->trickModifies[] = $trickModify;
            $trickModify->setTrick($this);
        }

        return $this;
    }

    public function removeTrickModify(TrickModify $trickModify): self
    {
        if ($this->trickModifies->removeElement($trickModify)) {
            // set the owning side to null (unless already changed)
            if ($trickModify->getTrick() === $this) {
                $trickModify->setTrick(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|Comment[]
     */
    public function getComments(): Collection
    {
        return $this->comments;
    }

    /**
     * @return array|Comment[]
     */
    public function getCommentsWithoutParent(): array
    {
        $comments = [];
        foreach ($this->comments as $comment) {
            if (null === $comment->getParent()) {
                $comments[] = $comment;
            }
        }
        return $comments;
    }


    public function addComment(Comment $comment): self
    {
        if (!$this->comments->contains($comment)) {
            $this->comments[] = $comment;
            $comment->setTrick($this);
        }

        return $this;
    }

    public function removeComment(Comment $comment): self
    {
        if ($this->comments->removeElement($comment)) {
            // set the owning side to null (unless already changed)
            if ($comment->getTrick() === $this) {
                $comment->setTrick(null);
            }
        }

        return $this;
    }

    public function getMainMedia(): ?Media
    {
        return $this->mainMedia;
    }

    public function setMainMedia(?Media $mainMedia): self
    {
        $this->mainMedia = $mainMedia;

        return $this;
    }

    public function getSlug(): ?string
    {
        return $this->slug;
    }

    public function setSlug(string $slug): self
    {
        $this->slug = $slug;

        return $this;
    }

    /**
     * @return Collection|Video[]
     */
    public function getVideos(): Collection
    {
        return $this->videos;
    }

    public function addVideo(Video $video): self
    {
        if (!$this->videos->contains($video)) {
            $this->videos[] = $video;
            $video->setTrick($this);
        }

        return $this;
    }

    public function removeVideo(Video $video): self
    {
        if ($this->videos->removeElement($video)) {
            // set the owning side to null (unless already changed)
            if ($video->getTrick() === $this) {
                $video->setTrick(null);
            }
        }

        return $this;
    }
}
