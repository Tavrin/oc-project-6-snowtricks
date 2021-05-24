<?php

namespace App\Entity;

use App\Repository\MediaRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=MediaRepository::class)
 */
class Media
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="smallint")
     */
    private $type;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $url;

    /**
     * @ORM\Column(type="datetime")
     */
    private $createdAt;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $updatedAt;

    /**
     * @ORM\OneToMany(targetEntity=TrickMedia::class, mappedBy="media")
     */
    private $trickMedia;

    public function __construct()
    {
        $this->trickMedia = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getType(): ?int
    {
        return $this->type;
    }

    public function setType(int $type): self
    {
        $this->type = $type;

        return $this;
    }

    public function getUrl(): ?string
    {
        return $this->url;
    }

    public function setUrl(string $url): self
    {
        $this->url = $url;

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
}
