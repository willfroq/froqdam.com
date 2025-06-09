<?php

declare(strict_types=1);

namespace Froq\PortalBundle\ColourLibrary\Controller;

use Pimcore\Model\DataObject\ColourGuideline;
use Pimcore\Model\DataObject\Organization;
use Pimcore\Model\DataObject\User;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\CurrentUser;

final class DetailController extends AbstractController
{
    #[Route('/detail/{id}', name: 'froq_portal.colour_library.detail', methods: [Request::METHOD_GET])]
    public function __invoke(int $id, #[CurrentUser] User $user): Response
    {
        $colourGuideline = ColourGuideline::getById($id);

        if (!($colourGuideline instanceof ColourGuideline)) {
            throw $this->createNotFoundException(message: 'Page not found.');
        }

        $organization = $colourGuideline->getOrganization();

        if (!($organization instanceof Organization)) {
            throw $this->createNotFoundException(message: 'Page not found.');
        }

        if (!in_array(needle: $user, haystack: $organization->getUsers())) {
            throw $this->createNotFoundException(message: 'Page not found.');
        }

        return $this->render(
            '@FroqPortalBundle/colour-library/detail.html.twig',
            [
                'colourGuideline' => $colourGuideline,
                'printGuidelines' => $colourGuideline->getPrintGuidelines(),
            ]
        );
    }
}
