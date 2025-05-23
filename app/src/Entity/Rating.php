<?php

/**
 * Rating entity.
 */

namespace App\Entity;

use App\Repository\RatingRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Class Rating.
 */
#[ORM\Entity(repositoryClass: RatingRepository::class)]
#[ORM\Table(name: 'ratings')]
class Rating
{
    /**
     * Primary key.
     */
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private ?int $id = null;

    /**
     * Value.
     */
    #[ORM\Column(type: 'integer')]
    #[Assert\Type('integer')]
    #[Assert\NotBlank]
    #[Assert\Range(min: 1, max: 5)]
    private ?int $value;

    /**
     * Recipe.
     */
    #[ORM\ManyToOne(targetEntity: Recipe::class, inversedBy: 'ratings')]
    #[ORM\JoinColumn(nullable: false)]
    #[Assert\Type(Recipe::class)]
    private ?Recipe $recipe = null;

    /**
     * Author.
     */
    #[ORM\ManyToOne(targetEntity: User::class, fetch: 'EXTRA_LAZY')]
    #[ORM\JoinColumn(nullable: false, onDelete: 'CASCADE')]
    #[Assert\NotBlank]
    #[Assert\Type(User::class)]
    private ?User $author;

    /**
     * Getter for id.
     *
     * @return int|null id
     */
    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * Getter for value.
     *
     * @return int|null value
     */
    public function getValue(): ?int
    {
        return $this->value;
    }

    /**
     * Setter for Value.
     *
     * @param int|null $value Value
     */
    public function setValue(?int $value): void
    {
        $this->value = $value;
    }

    /**
     * Getter for recipe.
     *
     * @return Recipe|null Recipe
     */
    public function getRecipe(): ?Recipe
    {
        return $this->recipe;
    }

    /**
     * Setter for recipe.
     *
     * @param Recipe|null $recipe Recipe
     */
    public function setRecipe(?Recipe $recipe): void
    {
        $this->recipe = $recipe;
    }

    /**
     * Getter for author.
     *
     * @return User|null Author
     */
    public function getAuthor(): ?User
    {
        return $this->author;
    }

    /**
     * Setter for author.
     *
     * @param User|null $author Author
     */
    public function setAuthor(?User $author): void
    {
        $this->author = $author;
    }
}
