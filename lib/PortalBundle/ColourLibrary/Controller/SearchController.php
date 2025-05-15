<?php

declare(strict_types=1);

namespace Froq\PortalBundle\ColourLibrary\Controller;

use Froq\PortalBundle\ColourLibrary\Action\BuildSearchRequest;
use Froq\PortalBundle\ColourLibrary\Action\BuildSearchResponse;
use Pimcore\Model\DataObject\User;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\CurrentUser;

final class SearchController extends AbstractController
{
    /**
     * @throws \Exception
     */
    #[Route('/search', name: 'froq_portal.colour_library.search', methods: [Request::METHOD_GET])]
    public function __invoke(Request $request, BuildSearchRequest $buildSearchRequest, BuildSearchResponse $buildSearchResponse, #[CurrentUser] User $user): Response
    {
        $totalBrands = 8;

        $brands = [
            ['id' => 1, 'name' => 'Amstel'],
            ['id' => 2, 'name' => 'Bernini'],
            ['id' => 3, 'name' => 'Birra Morretti'],
            ['id' => 4, 'name' => 'Desperados'],
        ];

        $markets = [
            ['id' => 1, 'name' => 'Global'],
            ['id' => 2, 'name' => 'EMEA'],
            ['id' => 3, 'name' => 'APAC'],
            ['id' => 4, 'name' => 'Nederland'],
        ];

        $mediums = [
            ['id' => 1, 'name' => 'Packaging'],
            ['id' => 2, 'name' => 'Digital'],
            ['id' => 3, 'name' => 'Merchandise'],
            ['id' => 4, 'name' => 'Other'],
        ];

        $sortOptions = ['Alphabetical', 'Newest', 'Oldest'];

        $templateParams = array_merge(
            [
                'brands' => $brands,
                'markets' => $markets,
                'mediums' => $mediums,
                'totalBrands' => $totalBrands,
                'sortOptions' => $sortOptions
            ]
        );

        $validatedRequest = ($buildSearchRequest)($request, $user);

        if ($validatedRequest->hasErrors) {
            throw $this->createNotFoundException(message: 'Page not found.');
        }

        if ($request->headers->get('Accept') === 'text/vnd.turbo-stream.html') {
            return $this->render(
                view: '@FroqPortalBundle/streams/colour-guideline-items.html.twig',
                parameters: [
                    ...($buildSearchResponse)($validatedRequest, $user)->toArray(),
                    ...$templateParams
                ]
            );
        }

        return $this->render(
            view: '@FroqPortalBundle/colour-library/search.html.twig',
            parameters: [
                ...($buildSearchResponse)($validatedRequest, $user)->toArray(),
                ...$templateParams
            ]
        );
    }
}
