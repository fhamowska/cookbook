<?php

/**
 * Ingredients data transformer.
 */

namespace App\Form\DataTransformer;

use App\Entity\Ingredient;
use App\Service\IngredientServiceInterface;
use Doctrine\Common\Collections\Collection;
use Symfony\Component\Form\DataTransformerInterface;

/**
 * Class IngredientsDataTransformer.
 *
 * @implements DataTransformerInterface<mixed, mixed>
 */
class IngredientsDataTransformer implements DataTransformerInterface
{
    /**
     * Constructor.
     *
     * @param IngredientServiceInterface $ingredientService Ingredient service
     */
    public function __construct(private readonly IngredientServiceInterface $ingredientService)
    {
    }

    /**
     * Transform array of ingredients to string of ingredient titles.
     *
     * @param Collection<int, Ingredient> $value Ingredients entity collection
     *
     * @return string Result
     */
    public function transform($value): string
    {
        if ($value->isEmpty()) {
            return '';
        }

        $ingredientTitles = [];

        foreach ($value as $ingredient) {
            $ingredientTitles[] = $ingredient->getTitle();
        }

        return implode(', ', $ingredientTitles);
    }

    /**
     * Transform string of ingredient names into array of Ingredient entities.
     *
     * @param string $value String of ingredient names
     *
     * @return array<int, Ingredient> Result
     */
    public function reverseTransform($value): array
    {
        $ingredientTitles = explode(',', $value);

        $ingredients = [];

        foreach ($ingredientTitles as $ingredientTitle) {
            if ('' !== trim($ingredientTitle)) {
                $ingredient = $this->ingredientService->findOneByTitle(strtolower($ingredientTitle));
                if (!$ingredient instanceof Ingredient) {
                    $ingredient = new Ingredient();
                    $ingredient->setTitle($ingredientTitle);

                    $this->ingredientService->save($ingredient);
                }
                $ingredients[] = $ingredient;
            }
        }

        return $ingredients;
    }
}
