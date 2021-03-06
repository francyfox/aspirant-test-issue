<?php declare(strict_types=1);

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\ORM\Mapping\Index;

/**
 * @ORM\Entity(repositoryClass="App\Repository\MoviesRepository")
 * @ORM\Table(name="movie", indexes={@Index(columns={"title"})})
 */
class Movie
{
    /**
     * @ORM\Id()
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private ?int $id;

    /**
     * @ORM\Column()
     */
    private ?string $title;

    /**
     * @ORM\Column()
     */
    private ?string $link;

    /**
     * @ORM\Column(type="text")
     */
    private ?string $description;

    /**
     * @ORM\Column(type="datetime", name="pub_date")
     */
    private ?\DateTime $pubDate;

    /**
     * @ORM\Column(nullable=true)
     */
    private ?string $image;

    /**
     * @ORM\Column(type="integer")
     */
    private int $likes;

    public function __construct()
    {
        $this->likes = 0;
    }

    /**
     * @return int
     */
    public function getLikes(): int
    {
        return $this->likes;
    }

    /**
     * @param int $likes
     */
    public function setLikes(): void
    {
        ++$this->likes;
    }

    /**
     * @param int $likes
     */
    public function disLikes(): void
    {
        if ($this->likes !== 0) {
            --$this->likes;
        }
    }

    public function getImage(): ?string
    {
        return $this->image;
    }

    public function setImage(?string $image): self
    {
        $this->image = $image;

        return $this;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function setTitle(?string $title): self
    {
        $this->title = $title;

        return $this;
    }

    public function getLink(): ?string
    {
        return $this->link;
    }

    public function setLink(?string $link): self
    {
        $this->link = $link;

        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(?string $description): self
    {
        $this->description = $description;

        return $this;
    }

    public function getPubDate(): ?\DateTime
    {
        return $this->pubDate;
    }

    public function setPubDate(?\DateTime $pubDate): self
    {
        $this->pubDate = $pubDate;

        return $this;
    }
}
