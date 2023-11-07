<?php

namespace App\Entity;

use App\Repository\ItemRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ItemRepository::class)]
class Item
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $name = null;

    #[ORM\Column(length: 100)]
    private ?string $size = null;

    #[ORM\Column]
    private ?float $price = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $brand = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $description = null;

    #[ORM\OneToMany(mappedBy: 'item', targetEntity: Image::class, orphanRemoval: true)]
    private Collection $images;

    #[ORM\ManyToMany(targetEntity: User::class, mappedBy: 'userValueItem')]
    private Collection $users;

/*     #[ORM\ManyToMany(targetEntity: User::class, mappedBy: 'userFavorite')]
    private Collection $usersFavorite; */

    #[ORM\ManyToMany(targetEntity: Basket::class, mappedBy: 'items')]
    private Collection $baskets;

    #[ORM\OneToMany(mappedBy: 'item', targetEntity: Coment::class)]
    private Collection $coment;

    #[ORM\Column(length: 255)]
    private ?string $url = null;

    #[ORM\Column(nullable: true)]
    private ?bool $important = null;

    public function __construct($name = NULL, $size = "M", $price = 0, $brand = NULL, $description = NULL)
    {
        $this->images = new ArrayCollection();
        $this->users = new ArrayCollection();
/*         $this->usersFavorite = new ArrayCollection();
 */     $this->baskets = new ArrayCollection();
        $this->coment = new ArrayCollection();

        $this->name = $name;
        $this->size = $size;
        $this->price = $price;
        $this->brand = $brand;
        $this->description = $description;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): static
    {
        $this->name = $name;

        return $this;
    }

    public function getSize(): ?string
    {
        return $this->size;
    }

    public function setSize(string $size): static
    {
        $this->size = $size;

        return $this;
    }

    public function getPrice(): ?float
    {
        return $this->price;
    }

    public function setPrice(float $price): static
    {
        $this->price = $price;

        return $this;
    }

    public function getBrand(): ?string
    {
        return $this->brand;
    }

    public function setBrand(?string $brand): static
    {
        $this->brand = $brand;

        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(?string $description): static
    {
        $this->description = $description;

        return $this;
    }

    /**
     * @return Collection<int, image>
     */
    public function getImages(): Collection
    {
        return $this->images;
    }

    public function addImage(Image $img): static
    {
        if (!$this->images->contains($img)) {
            $this->images->add($img);
            $img->setItem($this);
        }

        return $this;
    }

    public function removeImage(Image $img): static
    {
        if ($this->images->removeElement($img)) {
            // set the owning side to null (unless already changed)
            if ($img->getItem() === $this) {
                $img->setItem(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, User>
     */
    public function getUsers(): Collection
    {
        return $this->users;
    }

    public function addUser(User $user): static
    {
        if (!$this->users->contains($user)) {
            $this->users->add($user);
            $user->addUserValueItem($this);
        }

        return $this;
    }

    public function removeUser(User $user): static
    {
        if ($this->users->removeElement($user)) {
            $user->removeUserValueItem($this);
        }

        return $this;
    }

    /**
     * @return Collection<int, User>
     */
    /* public function getUsersFavorite(): Collection
    {
        return $this->usersFavorite;
    }

    public function addUsersFavorite(User $usersFavorite): static
    {
        if (!$this->usersFavorite->contains($usersFavorite)) {
            $this->usersFavorite->add($usersFavorite);
            $usersFavorite->addUserFavorite($this);
        }

        return $this;
    }

    public function removeUsersFavorite(User $usersFavorite): static
    {
        if ($this->usersFavorite->removeElement($usersFavorite)) {
            $usersFavorite->removeUserFavorite($this);
        }

        return $this;
    } */

    /**
     * @return Collection<int, Basket>
     */
    public function getBaskets(): Collection
    {
        return $this->baskets;
    }

    public function addBasket(Basket $bask): static
    {
        if (!$this->baskets->contains($bask)) {
            $this->baskets->add($bask);
            $bask->addItem($this);
        }

        return $this;
    }

    public function removeBasket(Basket $bask): static
    {
        if ($this->baskets->removeElement($bask)) {
            $bask->removeItem($this);
        }

        return $this;
    }

    /**
     * @return Collection<int, Coment>
     */
    public function getComent(): Collection
    {
        return $this->coment;
    }

    public function addComent(Coment $coment): static
    {
        if (!$this->coment->contains($coment)) {
            $this->coment->add($coment);
            $coment->setItem($this);
        }

        return $this;
    }

    public function removeComent(Coment $coment): static
    {
        if ($this->coment->removeElement($coment)) {
            // set the owning side to null (unless already changed)
            if ($coment->getItem() === $this) {
                $coment->setItem(null);
            }
        }

        return $this;
    }

    public function getUrl(): ?string
    {
        return $this->url;
    }

    public function setUrl(string $url): static
    {
        $this->url = $url;

        return $this;
    }

    public function isImportant(): ?bool
    {
        return $this->important;
    }

    public function setImportant(?bool $important): static
    {
        $this->important = $important;

        return $this;
    }
}
