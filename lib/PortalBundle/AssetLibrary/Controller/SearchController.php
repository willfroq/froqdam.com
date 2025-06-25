<?php

declare(strict_types=1);

namespace Froq\PortalBundle\AssetLibrary\Controller;

use Froq\PortalBundle\AssetLibrary\Action\BuildSearchRequest;
use Froq\PortalBundle\AssetLibrary\Action\BuildSearchResponse;
use Pimcore\Model\DataObject\User;
use Psr\Cache\InvalidArgumentException;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\CurrentUser;

final class SearchController extends AbstractController
{
    /**
     * @throws \Exception
     * @throws InvalidArgumentException
     */
    #[Route('/search', name: 'froq_portal.asset_library.search', methods: [Request::METHOD_GET])]
    public function __invoke(Request $request, BuildSearchRequest $buildSearchRequest, BuildSearchResponse $buildSearchResponse, #[CurrentUser] User $user): Response
    {
        $validatedRequest = ($buildSearchRequest)($request, $user);

        if ($validatedRequest->hasErrors) {
            throw $this->createNotFoundException(message: 'Page not found.');
        }

        if ($request->headers->get('Accept') === 'text/vnd.turbo-stream.html') {
            return $this->render(
                view: '@FroqPortalBundle/streams/asset-resource-items.html.twig',
                parameters: ($buildSearchResponse)($validatedRequest, $user)->toArray()
            );
        }

        return $this->render(
            view: '@FroqPortalBundle/asset-library/search.html.twig',
            parameters: ($buildSearchResponse)($validatedRequest, $user)->toArray()
        );
    }
}
