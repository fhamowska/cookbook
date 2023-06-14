<?php
/**
 * User controller.
 */

namespace App\Controller;

use App\Form\Type\UserEmailType;
use App\Form\Type\UserType;
use App\Entity\User;
use App\Service\UserServiceInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\FormType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\Translation\TranslatorInterface;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

/**
 * Class UserController.
 */
#[Route('/user')]
class UserController extends AbstractController
{
    /**
     * Password hasher.
     */
    private UserPasswordHasherInterface $passwordHasher;

    /**
     * User service.
     */
    private UserServiceInterface $userService;

    /**
     * Translator.
     */
    private TranslatorInterface $translator;

    /**
     * Constructor.
     *
     * @param UserServiceInterface        $userService    User service
     * @param TranslatorInterface         $translator     Translator
     * @param UserPasswordHasherInterface $passwordHasher Password hasher
     */
    public function __construct(UserServiceInterface $userService, TranslatorInterface $translator, UserPasswordHasherInterface $passwordHasher)
    {
        $this->userService = $userService;
        $this->translator = $translator;
        $this->passwordHasher = $passwordHasher;
    }

    /**
     * Index action.
     *
     * @param Request $request HTTP Request
     *
     * @return Response HTTP response
     */
    #[IsGranted('ROLE_ADMIN')]
    #[Route('/all', name: 'user_index', methods: 'GET')]
    public function index(Request $request): Response
    {
        $pagination = $this->userService->getPaginatedList(
            $request->query->getInt('page', 1)
        );

        return $this->render('user/index.html.twig', ['pagination' => $pagination]);
    }

    /**
     * Edit action.
     *
     * @param Request $request HTTP request
     * @param User    $user    User entity
     *
     * @return Response HTTP response
     */
    #[Route('/{id}/edit', name: 'user_edit', requirements: ['id' => '[1-9]\d*'], methods: 'GET|PUT')]
    public function edit(Request $request, User $user): Response
    {
        if (!in_array('ROLE_ADMIN', $this->getUser()->getRoles(), true)) {
            if ($user->getId() !== $this->getUser()->getId()) {
                $this->addFlash(
                    'warning',
                    $this->translator->trans('message.record_not_found')
                );

                return $this->redirectToRoute('recipe_index');
            }
        }

        $form = $this->createForm(
            UserType::class,
            $user,
            [
                'method' => 'PUT',
                'action' => $this->generateUrl('user_edit', ['id' => $user->getId()]),
            ]
        );
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $user->setPassword(
                $this->passwordHasher->hashPassword(
                    $user,
                    $form->get('password')->getData()
                )
            );

            $this->userService->save($user);

            $this->addFlash(
                'success',
                $this->translator->trans('message.edited_successfully')
            );

            if (in_array('ROLE_ADMIN', $this->getUser()->getRoles(), true)) {
                return $this->redirectToRoute('user_index');
            }

            return $this->redirectToRoute('recipe_index');
        }

        return $this->render(
            'user/edit.html.twig',
            [
                'form' => $form->createView(),
                'user' => $user,
            ]
        );
    }

    /**
     * Delete action.
     *
     * @param Request $request HTTP request
     * @param User                          $user    User entity
     *
     * @return Response HTTP response
     *
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     *
     * @Route(
     *     "/{id}/delete",
     *     methods={"GET", "DELETE"},
     *     requirements={"id": "[1-9]\d*"},
     *     name="user_delete",
     * )
     *
     * )
     */
    public function delete(Request $request, User $user): Response
    {
        if (!in_array('ROLE_ADMIN', $this->getUser()->getRoles(), true)) {
            if ($user->getId() !== $this->getUser()->getId()) {
                $this->addFlash(
                    'warning',
                    $this->translator->trans('message.record_not_found')
                );

                return $this->redirectToRoute('recipe_index');
            }
        }

        $form = $this->createForm(
            FormType::class,
            $user,
            [
                'method' => 'DELETE',
                'action' => $this->generateUrl('user_delete', ['id' => $user->getId()]),
            ]
        );
        $form->handleRequest($request);

        if ($request->isMethod('DELETE') && !$form->isSubmitted()) {
            $form->submit($request->request->get($form->getName()));
        }

        if ($form->isSubmitted() && $form->isValid()) {
            $this->userService->delete($user);
            $this->addFlash(
                'success',
                $this->translator->trans('message.edited_successfully')
            );

            if (in_array('ROLE_ADMIN', $this->getUser()->getRoles(), true)) {
                return $this->redirectToRoute('user_index');
            }

            return $this->redirectToRoute('user_index');
        }

        return $this->render(
            'user/delete.html.twig',
            [
                'form' => $form->createView(),
                'user' => $user,
            ]
        );
    }

    /**
     * EditEmail action.
     *
     * @param Request $request HTTP request
     * @param User    $user    User entity
     *
     * @return Response HTTP response
     */
    #[Route('/{id}/edit_email', name: 'user_edit_email', requirements: ['id' => '[1-9]\d*'], methods: 'GET|PUT')]
    public function edit_email(Request $request, User $user): Response
    {
        if (!in_array('ROLE_ADMIN', $this->getUser()->getRoles(), true)) {
            if ($user->getId() !== $this->getUser()->getId()) {
                $this->addFlash(
                    'warning',
                    $this->translator->trans('message.record_not_found')
                );

                return $this->redirectToRoute('recipe_index');
            }
        }

        $form = $this->createForm(
            UserEmailType::class,
            $user,
            [
                'method' => 'PUT',
                'action' => $this->generateUrl('user_edit_email', ['id' => $user->getId()]),
            ]
        );
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->userService->save($user);

            $this->addFlash(
                'success',
                $this->translator->trans('message.edited_successfully')
            );

            if (in_array('ROLE_ADMIN', $this->getUser()->getRoles(), true)) {
                return $this->redirectToRoute('user_index');
            }

            return $this->redirectToRoute('recipe_index');
        }

        return $this->render(
            'user/edit_email.html.twig',
            [
                'form' => $form->createView(),
                'user' => $user,
            ]
        );
    }
}