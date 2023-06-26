<?php
/**
 * Ingredient controller.
 */

namespace App\Controller;

use App\Entity\Ingredient;
use App\Form\Type\IngredientType;
use App\Service\IngredientServiceInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\FormType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\Translation\TranslatorInterface;

/**
 * Class IngredientController.
 */
#[IsGranted('ROLE_ADMIN')]
class IngredientController extends AbstractController
{
    /**
     * Constructor.
     *
     * @param IngredientServiceInterface $ingredientService Ingredient service
     * @param TranslatorInterface $translator    Translator
     */
    public function __construct(IngredientServiceInterface $ingredientService, TranslatorInterface $translator)
    {
        $this->ingredientService = $ingredientService;
        $this->translator = $translator;
    }

}