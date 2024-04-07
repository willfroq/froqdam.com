<?php

declare(strict_types=1);

namespace Froq\PortalBundle\Twig;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Core\Security;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

class PortalLoginExtension extends AbstractExtension
{
    private ?Request $request;

    public function __construct(protected RequestStack $requestStack)
    {
        $this->request = $requestStack->getCurrentRequest();
    }

    public function getFunctions(): array
    {
        return [
            new TwigFunction('portal_login_error', [$this, 'portalLoginError']),
        ];
    }

    public function getName(): string
    {
        return 'froq_portal_login_twig_extension';
    }

    public function portalLoginError(): string
    {
        if (!$this->request) {
            return '';
        }

        $authErrorKey = Security::AUTHENTICATION_ERROR;

        $session = $this->request->getSession();

        $error = '';

        // get the error if any (works with forward and redirect -- see below)
        if ($this->request->attributes->has($authErrorKey)) {
            $error = $this->request->attributes->get($authErrorKey);
        } elseif ($session->has($authErrorKey)) {
            $error = $session->get($authErrorKey);
            $session->remove($authErrorKey);
        }

        if ($error instanceof AuthenticationException) {
            return 'Incorrect username or password.'; // The value comes from the security component.
        }

        if ($error instanceof \Exception) {
            return $error->getMessage();
        }

        if (!empty($error) && !is_string($error)) {
            return 'Something went wrong with login process, try again later!';
        }

        if (is_array($error)) {
            return '';
        }

        return (string) $error;
    }
}
