<?php

namespace App\Entity;

use App\Repository\UserRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;

#[ORM\Entity(repositoryClass: UserRepository::class)]
#[UniqueEntity(fields: ['email'], message: 'There is already an account with this email')]
class User implements UserInterface, PasswordAuthenticatedUserInterface
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 180, unique: true)]
    private ?string $email = null;

    #[ORM\Column]
    private array $roles = [];

    /**
     * @var string The hashed password
     */
    #[ORM\Column]
    private ?string $password = null;

    #[ORM\Column(length: 75)]
    private ?string $name = null;

    #[ORM\Column(length: 100)]
    private ?string $surname1 = null;

    #[ORM\Column(length: 100, nullable: true)]
    private ?string $surname2 = null;

    #[ORM\Column(length: 75)]
    private ?string $username = null;

    #[ORM\Column]
    private ?float $wallet = null;

    #[ORM\Column(type: Types::DATE_MUTABLE)]
    private ?\DateTimeInterface $bithDate = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $profilePicture = null;

    #[ORM\OneToMany(mappedBy: 'user', targetEntity: Adress::class, orphanRemoval: true)]
    private Collection $adress;

    #[ORM\OneToMany(mappedBy: 'user', targetEntity: Basket::class)]
    private Collection $baskets;

    #[ORM\ManyToMany(targetEntity: Item::class, inversedBy: 'users')]
    private Collection $userValueItem;

    #[ORM\OneToMany(mappedBy: 'user', targetEntity: Coment::class)]
    private Collection $coments;

    #[ORM\Column(type: 'boolean')]
    private $isVerified = false;

/*     #[ORM\ManyToMany(targetEntity: item1::class, inversedBy: 'usersFavorite')]
    private Collection $userFavorite; */

    public function __construct($name = NULL, $surname1 = NULL, $surname2 = NULL, $username = NULL, $email = NULL, $password = NULL, $wallet = 0, $bithDate = null, $profilePicture = null)
    {
        $this->adress = new ArrayCollection();
        $this->baskets = new ArrayCollection();
        $this->userValueItem = new ArrayCollection();
        $this->coments = new ArrayCollection();
        $this->name = $name;
        $this->surname1 = $surname1;
        $this->surname2 = $surname2;
        $this->username = $username;
        $this->email = $email;
        $this->password = $password;
        $this->wallet = $wallet;
        $this->bithDate = $bithDate;
        $this->profilePicture = $profilePicture;

}

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): static
    {
        $this->email = $email;

        return $this;
    }

    /**
     * A visual identifier that represents this user.
     *
     * @see UserInterface
     */
    public function getUserIdentifier(): string
    {
        return (string) $this->email;
    }

    /**
     * @see UserInterface
     */
    public function getRoles(): array
    {
        $roles = $this->roles;
        // guarantee every user at least has ROLE_USER
        $roles[] = 'ROLE_USER';

        return array_unique($roles);
    }

    public function setRoles(array $roles): static
    {
        $this->roles = $roles;

        return $this;
    }

    /**
     * @see PasswordAuthenticatedUserInterface
     */
    public function getPassword(): string
    {
        return $this->password;
    }

    public function setPassword(string $password): static
    {
        $this->password = $password;

        return $this;
    }

    /**
     * @see UserInterface
     */
    public function eraseCredentials(): void
    {
        // If you store any temporary, sensitive data on the user, clear it here
        // $this->plainPassword = null;
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

    public function getSurname1(): ?string
    {
        return $this->surname1;
    }

    public function setSurname1(string $surname1): static
    {
        $this->surname1 = $surname1;

        return $this;
    }

    public function getSurname2(): ?string
    {
        return $this->surname2;
    }

    public function setSurname2(?string $surname2): static
    {
        $this->surname2 = $surname2;

        return $this;
    }

    public function getUsername(): ?string
    {
        return $this->username;
    }

    public function setUsername(string $username): static
    {
        $this->username = $username;

        return $this;
    }

    public function getWallet(): ?float
    {
        return $this->wallet;
    }

    public function setWallet(float $wallet): static
    {
        $this->wallet = $wallet;

        return $this;
    }

    public function getBithDate(): ?\DateTimeInterface
    {
        return $this->bithDate;
    }

    public function setBithDate(\DateTimeInterface $bithDate): static
    {
        $this->bithDate = $bithDate;

        return $this;
    }

    public function getProfilePicture(): ?string
    {
        return $this->profilePicture;
    }

    public function setProfilePicture(?string $profilePicture): static
    {
        $this->profilePicture = $profilePicture;

        return $this;
    }

    /**
     * @return Collection<int, Adress>
     */
    public function getAdress(): Collection
    {
        return $this->adress;
    }

    public function addAdress(Adress $adress): static
    {
        if (!$this->adress->contains($adress)) {
            $this->adress->add($adress);
            $adress->setUser($this);
        }

        return $this;
    }

    public function removeAdress(Adress $adress): static
    {
        if ($this->adress->removeElement($adress)) {
            // set the owning side to null (unless already changed)
            if ($adress->getUser() === $this) {
                $adress->setUser(null);
            }
        }

        return $this;
    }

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
            $bask->setUser($this);
        }

        return $this;
    }

    public function removeBasket(Basket $bask): static
    {
        if ($this->baskets->removeElement($bask)) {
            // set the owning side to null (unless already changed)
            if ($bask->getUser() === $this) {
                $bask->setUser(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Item>
     */
    public function getUserValueItem(): Collection
    {
        return $this->userValueItem;
    }

    public function addUserValueItem(Item $userValueItem): static
    {

        if (!$this->userValueItem->contains($userValueItem)) {
            $this->userValueItem->add($userValueItem);
        }else{
            $this->userValueItem->removeElement($userValueItem);
        }

        return $this;
    }
/* 
    public function removeUserValueItem(Item $userValueItem): static
    {
        $this->userValueItem->removeElement($userValueItem);

        return $this;
    } */

    /**
     * @return Collection<int, item1>
     */
    /* public function getUserFavorite(): Collection
    {
        return $this->userFavorite;
    }

    public function addUserFavorite(item1 $userFavorite): static
    {
        if (!$this->userFavorite->contains($userFavorite)) {
            $this->userFavorite->add($userFavorite);
        }

        return $this;
    }

    public function removeUserFavorite(item1 $userFavorite): static
    {
        $this->userFavorite->removeElement($userFavorite);

        return $this;
    } */

    /**
     * @return Collection<int, Coment>
     */
    public function getComents(): Collection
    {
        return $this->coments;
    }

    public function addComent(Coment $coment): static
    {
        if (!$this->coments->contains($coment)) {
            $this->coments->add($coment);
            $coment->setUser($this);
        }

        return $this;
    }

    public function removeComent(Coment $coment): static
    {
        if ($this->coments->removeElement($coment)) {
            // set the owning side to null (unless already changed)
            if ($coment->getUser() === $this) {
                $coment->setUser(null);
            }
        }

        return $this;
    }

    public function isVerified(): bool
    {
        return $this->isVerified;
    }

    public function setIsVerified(bool $isVerified): static
    {
        $this->isVerified = $isVerified;

        return $this;
    }
}
