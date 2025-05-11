<?php

declare(strict_types=1);

namespace Froq\PortalBundle\ColourLibrary\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

final class EditController extends AbstractController
{
    #[Route('/edit/{id}', name: 'froq_portal.colour_library.edit', methods: [Request::METHOD_GET, Request::METHOD_POST])]
    public function __invoke(int $id): Response
    {
        $detailData = [
            'id' => $id,
            'name' => 'Amstel Lager',
            'description' => 'Colour is key to the visual brand identity of Amstel. The 3 colours, red, white and gold are the three essential colours for the brand.',
            'additionalDescription' => 'For the right balance there always have to be at least two of the dominant colours and one of the secondary colours. This combination is simple yet powerful, ensuring that moving forward is truly iconic and recognizable worldwide.',
            'colors' => [
                [
                    'name' => 'White',
                    'brand_name' => '',
                    'background_color' => '#FFFFFF',
                    'border' => 'border-width: 1px; border-style: solid; border-color: #000000;',
                    'text_color' => 'text-black',
                    'specs' => [
                        ['key' => 'Pantone', 'value' => 'PMS 1234C'],
                        ['key' => 'Process Colour', 'value' => 'C12 M34 Y56 K78'],
                        ['key' => 'sRGB', 'value' => 'R12 G34 B56'],
                        ['key' => 'Textile', 'value' => '12-3456 TCX'],
                        ['key' => 'Paint', 'value' => 'RAL 1234'],
                    ]
                ],
                [
                    'name' => 'Amstel Red',
                    'brand_name' => 'Amstel',
                    'background_color' => '#E53829',
                    'text_color' => 'text-white',
                    'specs' => [
                        ['key' => 'Pantone', 'value' => 'PMS 1234C'],
                        ['key' => 'Process Colour', 'value' => 'C12 M34 Y56 K78'],
                        ['key' => 'sRGB', 'value' => 'R12 G34 B56'],
                        ['key' => 'Textile', 'value' => '12-3456 TCX'],
                        ['key' => 'Paint', 'value' => 'RAL 1234'],
                    ]
                ],
                [
                    'name' => 'Amstel Gold',
                    'brand_name' => 'Amstel',
                    'background_color' => '#B29466',
                    'text_color' => 'text-white',
                    'specs' => [
                        ['key' => 'Pantone', 'value' => 'PMS 1234C'],
                        ['key' => 'Process Colour', 'value' => 'C12 M34 Y56 K78'],
                        ['key' => 'sRGB', 'value' => 'R12 G34 B56'],
                        ['key' => 'Textile', 'value' => '12-3456 TCX'],
                        ['key' => 'Paint', 'value' => 'RAL 1234'],
                    ]
                ],
                [
                    'name' => 'Black',
                    'brand_name' => '',
                    'background_color' => '#021C00',
                    'text_color' => 'text-white',
                    'specs' => [
                        ['key' => 'Pantone', 'value' => 'PMS 1234C'],
                        ['key' => 'Process Colour', 'value' => 'C12 M34 Y56 K78'],
                        ['key' => 'sRGB', 'value' => 'R12 G34 B56'],
                        ['key' => 'Textile', 'value' => '12-3456 TCX'],
                        ['key' => 'Paint', 'value' => 'RAL 1234'],
                    ]
                ],
                [
                    'name' => 'Amstel Blue',
                    'brand_name' => 'Amstel',
                    'background_color' => '#045BA2',
                    'text_color' => 'text-white',
                    'specs' => [
                        ['key' => 'Pantone', 'value' => 'PMS 1234C'],
                        ['key' => 'Process Colour', 'value' => 'C12 M34 Y56 K78'],
                        ['key' => 'sRGB', 'value' => 'R12 G34 B56'],
                        ['key' => 'Textile', 'value' => '12-3456 TCX'],
                        ['key' => 'Paint', 'value' => 'RAL 1234'],
                    ]
                ],
            ],
            'printInfo' => [
                'title' => 'Print expert notes – Amstel Lager Packaging Primary Bottle – Offset on white paper',
                'questions' => [
                    'What kind of reference did we make? (MCC / Frog mock-up/ Epson print / samples of produced market)',
                    'Do we have labels to share when markets request? Are these ready and in the office or at ITG or do we need to produce these when market reuqest?',
                    'Any adiditonal info to share when sharing the colour reference? Eyemark/ specific order of colours/ 2x white'
                ],
                'description' => 'Different surfaces have different properties and therefore it is necessary to use these specific colours. These colours have been print proofed and matched. In order to get the right Amstel look, it is therefore important to use them as described.',
                'image' => [
                    'src' => 'build/portal/media/amstel.png',
                    'alt' => 'Amstel Logo',
                ]
            ],
            'options' => [
                'mediums' => ['Packaging - Primary Bottle', 'Packaging - Secondary'],
                'substrates' => ['White paper'],
                'techniques' => ['Offset'],
            ],
            'selectedOptions' => [
                'medium' => 'Packaging - Primary Bottle',
                'substrate' => 'White paper',
                'technique' => 'Offset'
            ]
        ];

        $templateParams = array_merge(
            [
                'user' => $this->getUser(),
                'detailData' => $detailData,
            ]
        );

        return $this->render('@FroqPortalBundle/colour-library/edit.html.twig', $templateParams);
    }
}
