<?php

/**
 * Rating controller.
 */

namespace App\Controller;

use App\Entity\Rating;
use App\Entity\User;
use App\Form\Type\RatingType;
use App\Service\RatingServiceInterface;
use App\Service\RecipeServiceInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\Translation\TranslatorInterface;

/**
 * Class RatingController.
 */
class RatingController extends AbstractController
{
    /**
     * Constructor.
     *
     * @param RatingServiceInterface $ratingService Rating service
     * @param TranslatorInterface    $translator    Translator
     * @param RecipeServiceInterface $recipeService Recipe service
     */
    public function __construct(private readonly RatingServiceInterface $ratingService, private readonly TranslatorInterface $translator, private readonly RecipeServiceInterface $recipeService)
    {
    }

    /**
     * Create action.
     *
     * @param Request $request HTTP request
     *
     * @return Response HTTP response
     */
    #[\Symfony\Component\Routing\Attribute\Route('/rating/create', name: 'rating_create', requirements: ['id' => '[1-9]\d*'], methods: 'GET|POST')]
    public function create(Request $request): Response
    {
        $recipe = $this->recipeService->getById($request->get('id'));
        if (!$recipe instanceof \App\Entity\Recipe) {
            $this->addFlash(
                'warning',
                $this->translator->trans('message.recipe_not_found')
            );

            return $this->redirectToRoute('recipe_index');
        }

        $rating = new Rating();
        /** @var User $user */
        $user = $this->getUser();
        $rating->setAuthor($user);

        $recipeId = $this->recipeService->getById($request->get('id'))->getId();
        $form = $this->createForm(RatingType::class, $rating, ['action' => $this->generateUrl('rating_create', ['id' => $recipeId])]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $recipe = $this->recipeService->getById($request->get('id'));
            if (!$recipe instanceof \App\Entity\Recipe) {
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
}
