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
#[Route('/ingredient')]
#[IsGranted('ROLE_ADMIN')]
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
     * @param TranslatorInterface $translator    Translator
     */
    public function __construct(IngredientServiceInterface $ingredientService, TranslatorInterface $translator)
    {
        $this->ingredientService = $ingredientService;
        $this->translator = $translator;
    }

    /**
     * Index action.
     *
     * @param Request $request HTTP Request
     *
     * @return Response HTTP response
     */
    #[Route(name: 'ingredient_index', methods: 'GET')]
    public function index(Request $request): Response
    {
        $pagination = $this->ingredientService->getPaginatedList(
            $request->query->getInt('page', 1)
        );

        return $this->render('ingredient/index.html.twig', ['pagination' => $pagination]);
    }

    /**
     * Show action.
     *
     * @param Ingredient $ingredient Ingredient
     *
     * @return Response HTTP response
     */
    #[Route(
        '/{id}',
        name: 'ingredient_show',
        requirements: ['id' => '[1-9]\d*'],
        methods: 'GET'
    )]
    public function show(Ingredient $ingredient): Response
    {
        return $this->render('ingredient/show.html.twig', ['ingredient' => $ingredient]);
    }

    /**
     * Create action.
     *
     * @param Request $request HTTP request
     *
     * @return Response HTTP response
     */
    #[Route(
        '/create',
        name: 'ingredient_create',
        methods: 'GET|POST',
    )]
    public function create(Request $request): Response
    {
        $ingredient = new Ingredient();
        $form = $this->createForm(IngredientType::class, $ingredient);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->ingredientService->save($ingredient);

            $this->addFlash(
                'success',
                $this->translator->trans('message.created_successfully')
            );

            return $this->redirectToRoute('ingredient_index');
        }

        return $this->render(
            'ingredient/create.html.twig',
            ['form' => $form->createView()]
        );
    }

    /**
     * Edit action.
     *
     * @param Request $request HTTP request
     * @param Ingredient     $ingredient     Ingredient entity
     *
     * @return Response HTTP response
     */
    #[Route('/{id}/edit', name: 'ingredient_edit', requirements: ['id' => '[1-9]\d*'], methods: 'GET|PUT')]
    public function edit(Request $request, Ingredient $ingredient): Response
    {
        $form = $this->createForm(
            IngredientType::class,
            $ingredient,
            [
                'method' => 'PUT',
                'action' => $this->generateUrl('ingredient_edit', ['id' => $ingredient->getId()]),
            ]
        );
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->ingredientService->save($ingredient);

            $this->addFlash(
                'success',
                $this->translator->trans('message.edited_successfully')
            );

            return $this->redirectToRoute('ingredient_index');
        }

        return $this->render(
            'ingredient/edit.html.twig',
            [
                'form' => $form->createView(),
                'ingredient' => $ingredient,
            ]
        );
    }

    /**
     * Delete action.
     *
     * @param Request $request HTTP request
     * @param Ingredient     $ingredient     Ingredient entity
     *
     * @return Response HTTP response
     */
    #[Route('/{id}/delete', name: 'ingredient_delete', requirements: ['id' => '[1-9]\d*'], methods: 'GET|DELETE')]
    public function delete(Request $request, Ingredient $ingredient): Response
    {
        if (!$this->ingredientService->canBeDeleted($ingredient)) {
            $this->addFlash(
                'warning',
                $this->translator->trans('message.ingredient_contains_recipes')
            );

            return $this->redirectToRoute('ingredient_index');
        }

        $form = $this->createForm(
            FormType::class,
            $ingredient,
            [
                'method' => 'DELETE',
                'action' => $this->generateUrl('ingredient_delete', ['id' => $ingredient->getId()]),
            ]
        );
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->ingredientService->delete($ingredient);

            $this->addFlash(
                'success',
                $this->translator->trans('message.deleted_successfully')
            );

            return $this->redirectToRoute('ingredient_index');
        }

        return $this->render(
            'ingredient/delete.html.twig',
            [
                'form' => $form->createView(),
                'ingredient' => $ingredient,
            ]
        );
    }
}