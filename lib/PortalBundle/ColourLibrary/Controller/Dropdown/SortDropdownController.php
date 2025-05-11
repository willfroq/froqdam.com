<?php

declare(strict_types=1);

namespace Froq\PortalBundle\ColourLibrary\Controller\Dropdown;

use Pimcore\Model\DataObject\User;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\CurrentUser;

class SortDropdownController extends AbstractController
{
    #[Route('/dropdown-search', name: 'froq_portal.colour_library.dropdown_search', methods: [Request::METHOD_GET])]
    public function __invoke(Request $request, #[CurrentUser] User $user): Response
    {
        $query = $request->query->get('q', '');
        $type = $request->query->get('type', '');

        $allOptions = $this->getOptionsForType($type);

        $filteredOptions = [];
        if (!empty($query)) {
            foreach ($allOptions as $option) {
                if (stripos($option['name'], $query) !== false) {
                    $filteredOptions[] = $option;
                }
            }
        } else {
            $filteredOptions = $allOptions;
        }

        return $this->render('@FroqPortalBundle/components/form/_dropdown_options.html.twig', [
            'options' => $filteredOptions,
            'id' => $request->query->get('id', 'default')
        ]);
    }

    /** @return array<int, mixed> */
    private function getOptionsForType(string $type): array
    {
        return match ($type) {
            'brands' => [
                ['id' => 1, 'name' => 'Amstel'],
                ['id' => 2, 'name' => 'Bernini'],
                ['id' => 3, 'name' => 'Birra Morretti'],
                ['id' => 4, 'name' => 'Desperados'],
            ],
            'markets' => [
                ['id' => 1, 'name' => 'Global'],
                ['id' => 2, 'name' => 'EMEA'],
                ['id' => 3, 'name' => 'APAC'],
                ['id' => 4, 'name' => 'Nederland'],
            ],
            'mediums' => [
                ['id' => 1, 'name' => 'Packaging'],
                ['id' => 2, 'name' => 'Digital'],
                ['id' => 3, 'name' => 'Merchandise'],
                ['id' => 4, 'name' => 'Other'],
            ],
            'sort' => array_map(function ($item) {
                return ['id' => $item, 'name' => $item];
            }, ['Alphabetical', 'Newest', 'Oldest']),
            default => [],
        };
    }
}
