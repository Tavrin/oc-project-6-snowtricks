<?php

namespace App\Entity;

use App\Repository\TrickRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=TrickRepository::class)
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
     * @ORM\Column(type="string", length=255)
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
     * @ORM\OneToMany(targetEntity=TrickMedia::class, mappedBy="media", orphanRemoval=true)
     */
    private $trickMedia;

    /**
     * @ORM\OneToMany(targetEntity=TrickModify::class, mappedBy="trick")
     */
    private $trickModifies;

    /**
     * @ORM\OneToMany(targetEntity=Comment::class, mappedBy="trick", fetch="EAGER")
     */
    private $comments;

    /**
     * @ORM\ManyToOne(targetEntity=Media::class)
     */
    private $mainMedia;

    public function __construct()
    {
        $this->trickMedia = new ArrayCollection();
        $this->trickModifies = new ArrayCollection();
        $this->comments = new ArrayCollection();
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
    public function getTrickMedia(): Collection
    {
        return $this->trickMedia;
    }

    public function addTrickMedium(TrickMedia $trickMedium): self
    {
        if (!$this->trickMedia->contains($trickMedium)) {
            $this->trickMedia[] = $trickMedium;
            $trickMedium->setMedia($this);
        }

        return $this;
    }

    public function removeTrickMedium(TrickMedia $trickMedium): self
    {
        if ($this->trickMedia->removeElement($trickMedium)) {
            // set the owning side to null (unless already changed)
            if ($trickMedium->getMedia() === $this) {
                $trickMedium->setMedia(null);
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
}
