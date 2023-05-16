<?php
/**
 * Recipe controller.
 */

namespace App\Controller;

use App\Entity\Recipe;
use App\Service\RecipeServiceInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Class RecipeController.
 */
#[Route('/recipe')]
class RecipeController extends AbstractController
{
    /**
     * Recipe service.
     */
    private RecipeServiceInterface $recipeService;

    /**
     * Constructor.
     */
    public function __construct(RecipeServiceInterface $recipeService)
    {
        $this->recipeService = $recipeService;
    }

    /**
     * Index action.
     *
     * @param Request $request HTTP Request
     *
     * @return Response HTTP response
     */
    #[Route(name: 'recipe_index', methods: 'GET')]
    public function index(Request $request): Response
    {
        $pagination = $this->recipeService->getPaginatedList(
            $request->query->getInt('page', 1)
        );

        return $this->render('recipe/index.html.twig', ['pagination' => $pagination]);
    }

    /**
     * Show action.
     *
     * @param Recipe $recipe Recipe
     *
     * @return Response HTTP response
     */
    #[Route(
        '/{id}',
        name: 'recipe_show',
        requirements: ['id' => '[1-9]\d*'],
        methods: 'GET'
    )]
    public function show(Recipe $recipe): Response
    {
        return $this->render('recipe/show.html.twig', ['recipe' => $recipe]);
    }
}
