<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\ORM\Mapping\Index;

/**
 * @ORM\Entity(repositoryClass="App\Repository\UserRepository")
 * @ORM\Table(name="User", indexes={@Index(columns={"username"})})
 */
class User
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
    private string $username;

    /**
     * @var string The hashed password
     * @ORM\Column(type="string")
     */
    private string $password;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Movie", cascade={"persist", "remove"}, orphanRemoval=true)
     * @ORM\JoinTable(name="likes",
     *      joinColumns={@ORM\JoinColumn(name="user_id", referencedColumnName="id")},
     *      inverseJoinColumns={@ORM\JoinColumn(name="movie_id", referencedColumnName="id")}
     *      )
     */
    private $my_likes;

    public function __construct()
    {
        $this->my_likes = new ArrayCollection();
    }

    /**
     * @return ArrayCollection
     */
    public function getMyLikes(): ArrayCollection
    {
        return $this->my_likes;
    }

    /**
     * Add like.
     *
     * @param Movie $movie
     */
    public function addMyLike(Movie $movie): void
    {
        $this->my_likes->add($movie);
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getUsername(): string
    {
        return (string) $this->username;
    }

    public function setUsername(string $username): self
    {
        $this->username = $username;

        return $this;
    }

    public function getPassword(): string
    {
        return (string) $this->password;
    }

    public function setPassword(string $password): self
    {
        $this->password = $password;

        return $this;
    }
}
