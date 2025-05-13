<?php

namespace App\Entity;

use App\Repository\ImageRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Class Image
 * *
 * @ORM\Entity
 * @ORM\Table(name="image")
 * @package DanielBundle\Entity
 */
#[ORM\Entity(repositoryClass: ImageRepository::class)]
#[ORM\Table(name: 'image')]
class Image
{
    /**
     * @var int
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    protected ?int $id = null;

    /**
     * @Assert\Length(
     *      min = 2,
     *      max = 50,
     * )
     * @Assert\NotBlank()
     * @var string
     * @ORM\Column(type="string")
     */
    #[Assert\NotBlank]
    #[Assert\Length(min: 2, max: 50)]
    #[ORM\Column]
    protected string $title;

    /**
     * @var string
     * @ORM\Column(type="string")
     */
    #[ORM\Column]
    protected string|File|null $image;

    /**
     * @Assert\Length(
     *      min = 0,
     *      max = 250,
     * )
     * @var string|null
     * @ORM\Column(type="string")
     */
    #[Assert\Length(min: 0, max: 250)]
    #[ORM\Column(nullable: true)]
    protected ?string $description = '';

    /**
     * @var int
     * @ORM\Column(type="integer")
     */
    #[ORM\Column]
    protected int $sorting  = 0;

    /**
     * @var \DateTime
     * @ORM\Column(type="datetime", nullable=true)
     */
    #[ORM\Column(type: 'datetime', nullable: true)]
    protected \DateTime $created;

    /**
     * Image constructor.
     */
    public function __construct()
    {
        $this->created = new \DateTime('now');
    }

    /**
     * Get Id.
     *
     * @return int|null
     */
    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * Get Title.
     *
     * @return string
     */
    public function getTitle(): string
    {
        return $this->title;
    }

    /**
     * @param string $title
     *
     * @return Image
     */
    public function setTitle(string $title): Image
    {
        $this->title = $title;

        return $this;
    }

    /**
     * Get Image.
     *
     * @return mixed
     */
    public function getImage()
    {
        return $this->image;
    }

    /**
     * @param mixed $image
     *
     * @return Image
     */
    public function setImage(mixed $image): Image
    {
        $this->image = $image;

        return $this;
    }

    /**
     * Get Description.
     *
     * @return string|null
     */
    public function getDescription(): ?string
    {
        return $this->description;
    }

    /**
     * @param string|null $description
     *
     * @return Image
     */
    public function setDescription(?string $description)
    {
        $this->description = $description;

        return $this;
    }

    /**
     * Get Sorting.
     *
     * @return int
     */
    public function getSorting(): int
    {
        return $this->sorting;
    }

    /**
     * @param int $sorting
     *
     * @return Image
     */
    public function setSorting(int $sorting): Image
    {
        $this->sorting = $sorting;

        return $this;
    }

    /**
     * Get Created.
     *
     * @return \DateTime
     */
    public function getCreated(): \DateTime
    {
        return $this->created;
    }
}
