<?php

namespace App\Entity;

use App\Repository\ImageRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\Context\ExecutionContextInterface;

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
     * @ORM\Column(type="integer", nullable=true)
     * @Assert\Positive()
     * @Assert\LessThanOrEqual(1000)
     */
    private $maxOpeningsNumber;

    /**
     * @ORM\Column(type="date")
     * @Assert\Type("\DateTime")
     */
    private $expiresAt;

    /**
     * @ORM\Column(type="json", nullable=true)
     */
    private $openings = [];

    /**
     * @Assert\NotBlank()
     */
    private $text;

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

    public static function getExtension(): string
    {
        return '.png';
    }

    public function getFilename(): string
    {
        return $this->getUuid() . self::getExtension();
    }

    public function open($url): ?int
    {
        $time = new \DateTime("now", new \DateTimeZone("UTC"));
        $this->openings[] = $time;
        setcookie('last_opening_time', $time->format("Y-m-d H:i:s"), time() + 300);
        setcookie('last_opening_image_path', $url, time() + 300);
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

    /**
     * @return array
     */
    public function getOpenings(): array
    {
        return $this->openings;
    }

    /**
     * @return string
     */
    public function getText()
    {
        return $this->text;
    }

    /**
     * @param string $text
     */
    public function setText($text): void
    {
        $this->text = $text;
    }

    /**
     * @Assert\Callback()
     */
    public function validate(ExecutionContextInterface $context, $payload)
    {
        $now = new \DateTime("midnight");
        $maxTime = new \DateTime("+1 year");
        if ($this->getExpiresAt() < $now) {
            $context->buildViolation("The date must be future")
                ->atPath('expiresAt')
                ->addViolation();
        }
        if ($this->getExpiresAt() > $maxTime) {
            $context->buildViolation("The date must be less than a year")
                ->atPath('expiresAt')
                ->addViolation();
        }
    }
}
