<?php
/**
 * Rating controller.
 */

namespace App\Controller;

use App\Entity\Rating;
use App\Form\Type\RatingType;
use App\Service\RatingServiceInterface;
use App\Service\RecipeServiceInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\FormType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\Translation\TranslatorInterface;

/**
 * Class RatingController.
 */
#[Route('/rating')]
class RatingController extends AbstractController
{
    /**
     * Rating service.
     */
    private RatingServiceInterface $ratingService;

    /**
     * Translator.
     */
    private TranslatorInterface $translator;

    /**
     * Recipe service.
     */
    private RecipeServiceInterface $recipeService;

    /**
     * Constructor.
     *
     * @param RatingServiceInterface $ratingService Rating service
     * @param TranslatorInterface     $translator     Translator
     * @param RecipeServiceInterface  $recipeService  Recipe service
     */
    public function __construct(RatingServiceInterface $ratingService, TranslatorInterface $translator, RecipeServiceInterface $recipeService)
    {
        $this->ratingService = $ratingService;
        $this->translator = $translator;
        $this->recipeService = $recipeService;
    }

    /**
     * Create action.
     *
     * @param Request $request HTTP request
     *
     * @return Response HTTP response
     */
    #[Route('/create', name: 'rating_create', requirements: ['id' => '[1-9]\d*'], methods: 'GET|POST')]
    public function create(Request $request): Response
    {
        $recipe = $this->recipeService->getById($request->get('id'));
        if (null === $recipe) {
            $this->addFlash(
                'warning',
                $this->translator->trans('message.recipe_not_found')
            );

            return $this->redirectToRoute('recipe_index');
        }

        $rating = new Rating();
        /** @var \App\Entity\User $user */
        $user = $this->getUser();
        $rating->setAuthor($user);

        $recipeId = $this->recipeService->getById($request->get('id'))->getId();
        $form = $this->createForm(RatingType::class, $rating, ['action' => $this->generateUrl('rating_create', ['id' => $recipeId])]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $recipe = $this->recipeService->getById($request->get('id'));
            if (null === $recipe) {
                $this->addFlash(
                    'warning',
                    $this->translator->trans('message.recipe_not_found')
                );

                return $this->redirectToRoute('recipe_index');
            }

            $recipe->addRating($rating);

            $rating->setRecipe($recipe);
            $this->ratingService->save($rating);

            $this->addFlash(
                'success',
                $this->translator->trans('message.created_successfully')
            );

            $recipeId = $recipe->getId();

            return $this->redirectToRoute('recipe_show', ['id' => $recipeId]);
        }

        $recipeId = $this->recipeService->getById($request->get('id'))->getId();

        return $this->render(
            'rating/create.html.twig',
            [
                'form' => $form->createView(),
                'id' => $recipeId,
            ]
        );
    }

    /**
     * Index action.
     *
     * @param Request $request HTTP Request
     *
     * @return Response HTTP response
     */
    #[Route(name: 'rating_index', methods: 'GET')]
    #[IsGranted('IS_AUTHENTICATED_FULLY')]
    public function index(Request $request): Response
    {
        $pagination = $this->ratingService->getPaginatedList(
            $request->query->getInt('page', 1),
            $this->getUser()
        );

        return $this->render('rating/index.html.twig', ['pagination' => $pagination]);
    }

    /**
     * Delete action.
     *
     * @param Request $request HTTP request
     * @param Rating $rating Rating entity
     *
     * @return Response HTTP response
     */
    #[IsGranted('DELETE', subject: 'rating')]
    #[Route('/{id}/delete', name: 'rating_delete', requirements: ['id' => '[1-9]\d*'], methods: 'GET|DELETE')]
    public function delete(Request $request, Rating $rating): Response
    {
        $form = $this->createForm(FormType::class, $rating, [
            'method' => 'DELETE',
            'action' => $this->generateUrl('rating_delete', ['id' => $rating->getId()]),
        ]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->ratingService->delete($rating);

            $this->addFlash(
                'success',
                $this->translator->trans('message.deleted_successfully')
            );

            $recipeId = $rating->getRecipe()->getId();

            return $this->redirectToRoute('recipe_show', ['id' => $recipeId]);
        }

        return $this->render(
            'rating/delete.html.twig',
            [
                'form' => $form->createView(),
                'rating' => $rating,
            ]
        );
    }
}