<?php

namespace App\Entity;

use App\Repository\UserRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Utilisateur de la plateforme DevDocs (géré par le SecurityBundle).
 */
#[ORM\Entity(repositoryClass: UserRepository::class)]
#[ORM\UniqueConstraint(name: 'UNIQ_IDENTIFIER_EMAIL', fields: ['email'])]
#[UniqueEntity(fields: ['email'], message: 'Un compte existe déjà avec cette adresse e-mail.')]
class User implements UserInterface, PasswordAuthenticatedUserInterface
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 180)]
    #[Assert\NotBlank(message: 'L\'adresse e-mail est obligatoire.')]
    #[Assert\Email(message: 'L\'adresse e-mail « {{ value }} » n\'est pas valide.')]
    #[Assert\Length(max: 180, maxMessage: 'L\'adresse e-mail ne doit pas dépasser {{ limit }} caractères.')]
    private ?string $email = null;

    /**
     * @var list<string> Les rôles de l'utilisateur
     */
    #[ORM\Column]
    private array $roles = [];

    /**
     * @var string Le mot de passe haché (jamais en clair)
     */
    #[ORM\Column]
    private ?string $password = null;

    #[ORM\Column(length: 30)]
    #[Assert\NotBlank(message: 'Le pseudonyme est obligatoire.')]
    #[Assert\Length(
        min: 3,
        max: 50,
        minMessage: 'Le pseudonyme doit contenir au moins {{ limit }} caractères.',
        maxMessage: 'Le pseudonyme ne doit pas dépasser {{ limit }} caractères.'
    )]
    private ?string $pseudonyme = null;

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
     * Identifiant visuel de l'utilisateur (ici, son e-mail).
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
        // Tout utilisateur a au minimum le rôle ROLE_USER
        $roles[] = 'ROLE_USER';

        return array_unique($roles);
    }

    /**
     * @param list<string> $roles
     */
    public function setRoles(array $roles): static
    {
        $this->roles = $roles;

        return $this;
    }

    /**
     * @see PasswordAuthenticatedUserInterface
     */
    public function getPassword(): ?string
    {
        return $this->password;
    }

    public function setPassword(string $password): static
    {
        $this->password = $password;

        return $this;
    }

    /**
     * Évite de stocker le vrai hash du mot de passe dans la session.
     */
    public function __serialize(): array
    {
        $data = (array) $this;
        $data["\0" . self::class . "\0password"] = hash('crc32c', $this->password);

        return $data;
    }

    #[\Deprecated]
    public function eraseCredentials(): void
    {
        // Obsolète, sera supprimé avec Symfony 8
    }

    public function getPseudonyme(): ?string
    {
        return $this->pseudonyme;
    }

    public function setPseudonyme(string $pseudonyme): static
    {
        $this->pseudonyme = $pseudonyme;

        return $this;
    }
}
