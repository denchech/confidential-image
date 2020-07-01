<?php

namespace App\Entity;

use App\Repository\ImageRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\UserInterface;

/**
 * @ORM\Entity(repositoryClass=ImageRepository::class)
 */
class Image implements UserInterface
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=180, unique=true)
     */
    private $uuid;

    /**
     * @var string The hashed password
     * @ORM\Column(type="string")
     */
    private $password;

    /**
     * @ORM\Column(type="integer")
     */
    private $openingsNumber;

    /**
     * @ORM\Column(type="integer")
     */
    private $maxOpeningsNumber;

    /**
     * @ORM\Column(type="datetime")
     */
    private $expiresAt;

    /**
     * Image constructor.
     */
    public function __construct()
    {
        $this->openingsNumber = 0;
        $this->expiresAt = new \DateTime("midnight");
        $this->expiresAt->modify('+1 week');
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getUuid(): ?string
    {
        return $this->uuid;
    }

    public function setUuid(string $uuid): self
    {
        $this->uuid = $uuid;

        return $this;
    }

    /**
     * A visual identifier that represents this user.
     *
     * @see UserInterface
     */
    public function getUsername(): string
    {
        return (string)$this->uuid;
    }

    /**
     * @see UserInterface
     */
    public function getRoles(): array
    {
        return ['ROLE_USER'];
    }

    /**
     * @see UserInterface
     */
    public function getPassword(): string
    {
        return (string)$this->password;
    }

    public function setPassword(string $password): self
    {
        $this->password = $password;

        return $this;
    }

    /**
     * @see UserInterface
     */
    public function getSalt()
    {

    }

    /**
     * @see UserInterface
     */
    public function eraseCredentials()
    {

    }

    public function getExtension(): string
    {
        return '.png';
    }

    public function getFilename(): string
    {
        return $this->getUuid() . $this->getExtension();
    }

    public function open(): ?int
    {
        return ++$this->openingsNumber;
    }

    public function getMaxOpeningsNumber(): ?int
    {
        return $this->maxOpeningsNumber;
    }

    public function setMaxOpeningsNumber(int $maxOpeningsNumber): self
    {
        $this->maxOpeningsNumber = $maxOpeningsNumber;

        return $this;
    }

    public function getExpiresAt(): ?\DateTimeInterface
    {
        return $this->expiresAt;
    }

    public function setExpiresAt(\DateTimeInterface $expiresAt): self
    {
        $this->expiresAt = $expiresAt;

        return $this;
    }
}
