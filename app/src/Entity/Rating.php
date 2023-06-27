<?php
/**
 * Rating entity.
 */

namespace App\Entity;

use App\Repository\RatingRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Class Rating.
 */
#[ORM\Entity(repositoryClass: RatingRepository::class)]
#[UniqueEntity(fields: ['author', 'recipe'], message: 'You have already rated this recipe.')]
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
    #[ORM\Column(type: 'integer', length: 255)]
    #[Assert\Type('integer')]
    #[Assert\NotBlank]
    private ?int $value;

    /**
     * Recipe.
     */
    #[ORM\ManyToOne(targetEntity: Recipe::class, inversedBy: 'ratings')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Recipe $recipe = null;

    /**
     * Author.
     *
     * @var User|null
     *
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
     *
     * @return $this
     */
    public function setValue(?int $value): self
    {
        $this->value = $value;

        return $this;
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
     *
     * @return $this
     */
    public function setRecipe(?Recipe $recipe): self
    {
        $this->recipe = $recipe;

        return $this;
    }

    public function getAuthor(): ?User
    {
        return $this->author;
    }

    public function setAuthor(?User $author): self
    {
        $this->author = $author;

        return $this;
    }
}