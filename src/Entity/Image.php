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
     * @var string
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
    protected ?\DateTime $created;

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
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Get Title.
     *
     * @return string
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * @param string $title
     *
     * @return Image
     */
    public function setTitle($title)
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
    public function setImage($image)
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
     * @param string $description
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
    public function getSorting()
    {
        return $this->sorting;
    }

    /**
     * @param int $sorting
     *
     * @return Image
     */
    public function setSorting($sorting)
    {
        $this->sorting = $sorting;

        return $this;
    }

    /**
     * Get Created.
     *
     * @return \DateTime
     */
    public function getCreated()
    {
        return $this->created;
    }

    /**
     * Set Created.
     *
     * @param \DateTime $created
     *
     * @return Image
     */
    public function setCreated($created)
    {
        $this->created = $created;

        return $this;
    }
}
