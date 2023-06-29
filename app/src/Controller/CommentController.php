<?php
/**
 * Comment controller.
 */

namespace App\Controller;

use App\Entity\Comment;
use App\Entity\User;
use App\Form\Type\CommentType;
use App\Service\CommentServiceInterface;
use App\Service\RecipeServiceInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\FormType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\Translation\TranslatorInterface;

/**
 * Class CommentController.
 */
#[Route('/comment')]
class CommentController extends AbstractController
{
    /**
     * Comment service.
     */
    private CommentServiceInterface $commentService;

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
     * @param CommentServiceInterface $commentService Comment service
     * @param TranslatorInterface     $translator     Translator
     * @param RecipeServiceInterface  $recipeService  Recipe service
     */
    public function __construct(CommentServiceInterface $commentService, TranslatorInterface $translator, RecipeServiceInterface $recipeService)
    {
        $this->commentService = $commentService;
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
    #[Route('/create', name: 'comment_create', requirements: ['id' => '[1-9]\d*'], methods: 'GET|POST')]
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

        $comment = new Comment();
        /** @var User $user */
        $user = $this->getUser();
        $comment->setAuthor($user);

        $recipeId = $this->recipeService->getById($request->get('id'))->getId();
        $form = $this->createForm(CommentType::class, $comment, ['action' => $this->generateUrl('comment_create', ['id' => $recipeId])]);
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

            $recipe->addComment($comment);

            $comment->setRecipe($recipe);
            $this->commentService->save($comment);

            $this->addFlash(
                'success',
                $this->translator->trans('message.created_successfully')
            );

            $recipeId = $recipe->getId();

            return $this->redirectToRoute('recipe_show', ['id' => $recipeId]);
        }

        $recipeId = $this->recipeService->getById($request->get('id'))->getId();

        return $this->render(
            'comment/create.html.twig',
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
    #[Route(name: 'comment_index', methods: 'GET')]
    #[IsGranted('IS_AUTHENTICATED_FULLY')]
    public function index(Request $request): Response
    {
        $pagination = $this->commentService->getPaginatedList(
            $request->query->getInt('page', 1),
            $this->getUser()
        );

        return $this->render('comment/index.html.twig', ['pagination' => $pagination]);
    }

    /**
     * Delete action.
     *
     * @param Request $request HTTP request
     * @param Comment $comment Comment entity
     *
     * @return Response HTTP response
     */
    #[IsGranted('DELETE', subject: 'comment')]
    #[Route('/{id}/delete', name: 'comment_delete', requirements: ['id' => '[1-9]\d*'], methods: 'GET|DELETE')]
    public function delete(Request $request, Comment $comment): Response
    {
        $form = $this->createForm(FormType::class, $comment, [
            'method' => 'DELETE',
            'action' => $this->generateUrl('comment_delete', ['id' => $comment->getId()]),
        ]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->commentService->delete($comment);

            $this->addFlash(
                'success',
                $this->translator->trans('message.deleted_successfully')
            );

            $recipeId = $comment->getRecipe()->getId();

            return $this->redirectToRoute('recipe_show', ['id' => $recipeId]);
        }

        return $this->render(
            'comment/delete.html.twig',
            [
                'form' => $form->createView(),
                'comment' => $comment,
            ]
        );
    }
}
