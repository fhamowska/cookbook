<?php
/**
 * Ingredient controller.
 */

namespace App\Controller;

use App\Entity\Ingredient;
use App\Service\IngredientServiceInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Contracts\Translation\TranslatorInterface;

/**
 * Class IngredientController.
 */
class IngredientController extends AbstractController
{
    /**
     * Ingredient service.
     */
    private IngredientServiceInterface $ingredientService;

    /**
     * Translator.
     */
    private TranslatorInterface $translator;

    /**
     * Constructor.
     *
     * @param IngredientServiceInterface $ingredientService Ingredient service
     * @param TranslatorInterface        $translator        Translator
     */
    public function __construct(IngredientServiceInterface $ingredientService, TranslatorInterface $translator)
    {
        $this->ingredientService = $ingredientService;
        $this->translator = $translator;
    }
}
