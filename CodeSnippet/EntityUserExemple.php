<?php

namespace App\Entity;

use App\Repository\UserRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use phpDocumentor\Reflection\Types\Boolean;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Serializer\Annotation\Groups;

/**
 * @ORM\Entity(repositoryClass=UserRepository::class)
 * @UniqueEntity(fields={"email"}, message="There is already an account with this email")
 */
class User implements UserInterface, PasswordAuthenticatedUserInterface
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=180, unique=true)
     */
    private $email;

    /**
     * @ORM\Column(type="json")
     */
    private $roles = [];

    /**
     * @var string The hashed password
     * @ORM\Column(type="string")
     */
    private $password;

    /**
     * @ORM\Column(type="string", length=255)
     * @Groups({"ajax_list"})
     * @Groups({"ajax_list_two"})
     */
    private $surname;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private $bio;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $picture_profil;

    /**
     * @ORM\OneToMany(targetEntity=Actuality::class, mappedBy="user")
     */
    private $actualities;

    /**
     * @ORM\OneToMany(targetEntity=Beer::class, mappedBy="user")
     */
    private $beers;

    /**
     * @ORM\ManyToOne(targetEntity=Region::class, inversedBy="users")
     */
    private $regiontolive;

    /**
     * @ORM\OneToMany(targetEntity=FormResponses::class, mappedBy="users")
     */
    private $formResponses;

    /**
     * @ORM\OneToMany(targetEntity=ActualityComments::class, mappedBy="user")
     */
    private $actualityComments;

    /**
     * @ORM\OneToMany(targetEntity=BeerComments::class, mappedBy="user")
     */
    private $beerComments;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $reset_token;

    public function __construct()
    {
        $this->actualities = new ArrayCollection();
        $this->beers = new ArrayCollection();
        $this->formResponses = new ArrayCollection();
        $this->actualityComments = new ArrayCollection();
        $this->beerComments = new ArrayCollection();
    }

    public function __toString()
    {
        return $this->surname;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): self
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
     * @deprecated since Symfony 5.3, use getUserIdentifier instead
     */
    public function getUsername(): string
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

    public function setRoles(array $roles): self
    {
        $this->roles = $roles;

        return $this;
    }

    public function hasRole($role) : bool
    {
        return in_array($role,$this->roles);

    }

    /**
     * @see PasswordAuthenticatedUserInterface
     */
    public function getPassword(): string
    {
        return $this->password;
    }

    public function setPassword(string $password): self
    {
        $this->password = $password;

        return $this;
    }

    /**
     * Returning a salt is only needed, if you are not using a modern
     * hashing algorithm (e.g. bcrypt or sodium) in your security.yaml.
     *
     * @see UserInterface
     */
    public function getSalt(): ?string
    {
        return null;
    }

    /**
     * @see UserInterface
     */
    public function eraseCredentials()
    {
        // If you store any temporary, sensitive data on the user, clear it here
        // $this->plainPassword = null;
    }

    public function getSurname(): ?string
    {
        return $this->surname;
    }

    public function setSurname(string $surname): self
    {
        $this->surname = $surname;

        return $this;
    }

    
    public function getBio(): ?string
    {
        return $this->bio;
    }

    public function setBio(string $bio): self
    {
        $this->bio = $bio;

        return $this;
    }

    public function getPictureProfil(): ?string
    {
        return $this->picture_profil;
    }

    public function setPictureProfil(?string $picture_profil): self
    {
        $this->picture_profil = $picture_profil;

        return $this;
    }

    /**
     * @return Collection|Actuality[]
     */
    public function getActualities(): Collection
    {
        return $this->actualities;
    }

    public function addActuality(Actuality $actuality): self
    {
        if (!$this->actualities->contains($actuality)) {
            $this->actualities[] = $actuality;
            $actuality->setUser($this);
        }

        return $this;
    }

    public function removeActuality(Actuality $actuality): self
    {
        if ($this->actualities->removeElement($actuality)) {
            // set the owning side to null (unless already changed)
            if ($actuality->getUser() === $this) {
                $actuality->setUser(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|Beer[]
     */
    public function getBeers(): Collection
    {
        return $this->beers;
    }

    public function addBeer(Beer $beer): self
    {
        if (!$this->beers->contains($beer)) {
            $this->beers[] = $beer;
            $beer->setUser($this);
        }

        return $this;
    }

    public function removeBeer(Beer $beer): self
    {
        if ($this->beers->removeElement($beer)) {
            // set the owning side to null (unless already changed)
            if ($beer->getUser() === $this) {
                $beer->setUser(null);
            }
        }

        return $this;
    }

    public function getRegiontolive(): ?Region
    {
        return $this->regiontolive;
    }

    public function setRegiontolive(?Region $regiontolive): self
    {
        $this->regiontolive = $regiontolive;

        return $this;
    }

    /**
     * @return Collection|FormResponses[]
     */
    public function getFormResponses(): Collection
    {
        return $this->formResponses;
    }

    public function addFormResponse(FormResponses $formResponse): self
    {
        if (!$this->formResponses->contains($formResponse)) {
            $this->formResponses[] = $formResponse;
            $formResponse->setUsers($this);
        }

        return $this;
    }

    public function removeFormResponse(FormResponses $formResponse): self
    {
        if ($this->formResponses->removeElement($formResponse)) {
            // set the owning side to null (unless already changed)
            if ($formResponse->getUsers() === $this) {
                $formResponse->setUsers(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|ActualityComments[]
     */
    public function getActualityComments(): Collection
    {
        return $this->actualityComments;
    }

    public function addActualityComment(ActualityComments $actualityComment): self
    {
        if (!$this->actualityComments->contains($actualityComment)) {
            $this->actualityComments[] = $actualityComment;
            $actualityComment->setUser($this);
        }

        return $this;
    }

    public function removeActualityComment(ActualityComments $actualityComment): self
    {
        if ($this->actualityComments->removeElement($actualityComment)) {
            // set the owning side to null (unless already changed)
            if ($actualityComment->getUser() === $this) {
                $actualityComment->setUser(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|BeerComments[]
     */
    public function getBeerComments(): Collection
    {
        return $this->beerComments;
    }

    public function addBeerComment(BeerComments $beerComment): self
    {
        if (!$this->beerComments->contains($beerComment)) {
            $this->beerComments[] = $beerComment;
            $beerComment->setUser($this);
        }

        return $this;
    }

    public function removeBeerComment(BeerComments $beerComment): self
    {
        if ($this->beerComments->removeElement($beerComment)) {
            // set the owning side to null (unless already changed)
            if ($beerComment->getUser() === $this) {
                $beerComment->setUser(null);
            }
        }

        return $this;
    }

    public function getResetToken(): ?string
    {
        return $this->reset_token;
    }

    public function setResetToken(?string $reset_token): self
    {
        $this->reset_token = $reset_token;

        return $this;
    }
}
