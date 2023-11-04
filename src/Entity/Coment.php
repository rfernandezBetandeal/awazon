<?php

namespace App\Entity;

use App\Repository\ComentRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ComentRepository::class)]
class Coment
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(type: Types::TEXT)]
    private ?string $coment = null;

    #[ORM\ManyToOne(inversedBy: 'coments')]
    #[ORM\JoinColumn(nullable: false)]
    private ?User $user = null;

    #[ORM\ManyToOne(inversedBy: 'coment')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Item $item = null;

    public function __construct($user, $item1, $content)
    {
        $this->user = $user;
        $this->item = $item1;
        $this->coment = $content;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getComent(): ?string
    {
        return $this->coment;
    }

    public function setComent(string $coment): static
    {
        $this->coment = $coment;

        return $this;
    }

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(?User $user): static
    {
        $this->user = $user;

        return $this;
    }

    public function getItem(): ?Item
    {
        return $this->item;
    }

    public function setItem(?Item $item1): static
    {
        $this->item = $item1;

        return $this;
    }
}
