<?php

namespace App\Entity;

use App\Repository\UserRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;

#[ORM\Entity(repositoryClass: UserRepository::class)]
#[ORM\Table(name: '`user`')]
class User implements UserInterface, PasswordAuthenticatedUserInterface
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column]
    private bool $admin = false;

    // Nom utilisé maintenant comme identifiant de connexion : "ina"
    #[ORM\Column]
    private ?string $name = null;

    #[ORM\Column(type: 'text', nullable: true)]
    private ?string $description = null;

    #[ORM\Column(length: 180, unique: true)]
    private ?string $email = null;

    // Ajouté : rôles Symfony stockés en base
    #[ORM\Column(type: 'json')]
    private array $roles = [];

    // Ajouté : mot de passe hashé stocké en base
    #[ORM\Column]
    private ?string $password = null;

    #[ORM\OneToMany(targetEntity: Media::class, mappedBy: 'user')]
    private Collection $medias;

    public function __construct()
    {
        $this->medias = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    // Modifié : Symfony utilise maintenant le champ "name" pour la connexion
    public function getUserIdentifier(): string
    {
        return (string) $this->name;
    }

    // Compatibilité avec anciennes versions Symfony
    public function getUsername(): string
    {
        return $this->getUserIdentifier();
    }

    // Ajouté : retourne les rôles de l'utilisateur
    public function getRoles(): array
    {
        $roles = $this->roles;

        // Tout utilisateur connecté a au minimum ROLE_USER
        $roles[] = 'ROLE_USER';

        // Si admin = true, alors on ajoute ROLE_ADMIN
        if ($this->admin) {
            $roles[] = 'ROLE_ADMIN';
        }

        return array_unique($roles);
    }

    // Ajouté : permet d’enregistrer les rôles en base
    public function setRoles(array $roles): static
    {
        $this->roles = $roles;

        return $this;
    }

    // Ajouté : retourne le mot de passe hashé
    public function getPassword(): ?string
    {
        return $this->password;
    }

    // Ajouté : enregistre le mot de passe hashé
    public function setPassword(string $password): static
    {
        $this->password = $password;

        return $this;
    }

    // Pas de salt séparé avec les hash modernes
    public function getSalt(): ?string
    {
        return null;
    }

    // Nettoyage des données sensibles temporaires si besoin
    public function eraseCredentials(): void
    {
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

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(?string $name): void
    {
        $this->name = $name;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(?string $description): void
    {
        $this->description = $description;
    }

    public function getMedias(): Collection
    {
        return $this->medias;
    }

    public function setMedias(Collection $medias): void
    {
        $this->medias = $medias;
    }

    public function isAdmin(): bool
    {
        return $this->admin;
    }

    public function setAdmin(bool $admin): void
    {
        $this->admin = $admin;
    }
}