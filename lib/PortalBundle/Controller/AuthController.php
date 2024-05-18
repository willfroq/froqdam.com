<?php

declare(strict_types=1);

namespace Froq\PortalBundle\Controller;

use MembersBundle\Controller\AuthController as MembersAuthController;
use MembersBundle\Form\Factory\FactoryInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\Security\Http\Util\TargetPathTrait;

#[Route('/auth', name: 'froq_portal.auth.')]
class AuthController extends MembersAuthController
{
    use TargetPathTrait;

    public function __construct(protected FactoryInterface $membersSecurityLoginFormFactory)
    {
        parent::__construct($membersSecurityLoginFormFactory);
    }

    #[Route('/login', name: 'login', methods: ['GET'])]
    public function loginAction(Request $request): Response
    {
        if ($this->getUser()) {
            $routeName = !is_array($this->getParameter('default_portal_dashboard_path')) ? $this->getParameter('default_portal_dashboard_path') : '';

            return $this->redirectToRoute((string) $routeName);
        }

        $authErrorKey = Security::AUTHENTICATION_ERROR;
        $lastUsernameKey = Security::LAST_USERNAME;
        $session = $request->getSession();
        $targetPath = $this->getTargetPath($session, 'portal');
        $failurePath = $request->get('_failure_path', null);

        // last username entered by the user
        $lastUsername = $session->get($lastUsernameKey);

        $form = $this->formFactory->createUnnamedFormWithOptions([
            'last_username' => $lastUsername,
            '_target_path' => $targetPath,
            '_failure_path' => $failurePath
        ]);

        // get the error if any (works with forward and redirect -- see below)
        if ($request->attributes->has($authErrorKey)) {
            $error = $request->attributes->get($authErrorKey);
        } elseif ($session->has($authErrorKey)) {
            $error = $session->get($authErrorKey);
            $session->remove($authErrorKey);
        } else {
            $error = null;
        }

        if (!$error instanceof AuthenticationException) {
            $error = null; // The value does not come from the security component.
        }

        return $this->renderForm('@FroqPortalBundle/auth/login.html.twig', [
            'form' => $form,
            'last_username' => $lastUsername,
            'error' => $error
        ]);
    }

    #[Route('/login-check', name: 'login_check', methods: ['POST'])]
    public function loginCheckAction(): void
    {
        parent::checkAction();
    }

    #[Route('/logout', name: 'logout', methods: ['GET', 'POST'])]
    public function logoutAction(): void
    {
        parent::logoutAction();
    }

    #[Route('/is-active', name: 'is_active', methods: ['POST'])]
    public function isLoggedInAction(): Response
    {
        if ($this->getUser()) {
            return $this->json('ok');
        }

        return $this->json(null, 403);
    }
}
