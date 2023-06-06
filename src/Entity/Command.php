<?php

namespace App\Entity;

use App\Repository\CommandRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: CommandRepository::class)]
class Command
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(inversedBy: 'commands')]
    #[ORM\JoinColumn(nullable: false)]
    private ?User $user = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    private ?\DateTimeInterface $createdAt = null;

    #[ORM\Column(length: 255)]
    private ?string $status = null;

    #[ORM\OneToMany(mappedBy: 'command', targetEntity: CommandItem::class, orphanRemoval: true)]
    private Collection $commandItems;

    public function __construct()
    {
        $this->commandItems = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(?User $user): self
    {
        $this->user = $user;

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

    public function getStatus(): ?string
    {
        return $this->status;
    }

    public function setStatus(string $status): self
    {
        $this->status = $status;

        return $this;
    }

    /**
     * @return Collection<int, CommandItem>
     */
    public function getCommandItems(): Collection
    {
        return $this->commandItems;
    }

    public function addCommandItem(CommandItem $commandItem): self
    {
        if (!$this->commandItems->contains($commandItem)) {
            $this->commandItems->add($commandItem);
            $commandItem->setCommand($this);
        }

        return $this;
    }

    public function removeCommandItem(CommandItem $commandItem): self
    {
        if ($this->commandItems->removeElement($commandItem)) {
            if ($commandItem->getCommand() === $this) {
                $commandItem->setCommand(null);
            }
        }

        return $this;
    }
}
