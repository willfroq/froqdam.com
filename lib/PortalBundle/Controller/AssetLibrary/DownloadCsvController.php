<?php

declare(strict_types=1);

namespace Froq\PortalBundle\Controller\AssetLibrary;

use Froq\PortalBundle\Action\BuildCsvDownloadItems;
use Froq\PortalBundle\DTO\FormData\LibraryFormDto;
use Froq\PortalBundle\Manager\ES\AssetLibrary\AssetLibFormManager;
use Froq\PortalBundle\Manager\ES\AssetLibrary\AssetLibQueryBuilderManager;
use League\Csv\CannotInsertRecord;
use League\Csv\Exception;
use League\Csv\Writer;
use Pimcore\Model\DataObject\User;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\StreamedResponse;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\CurrentUser;

#[Route('/download-csv', name: 'froq_portal_asset_library')]
final class DownloadCsvController extends AbstractController
{
    public function __construct(
        private readonly AssetLibQueryBuilderManager $assetLibQueryBuilder,
        private readonly AssetLibFormManager $assetLibFormManager,
        private readonly LibraryFormDto $libraryFormDto,
    ) {
    }

    /**
     * @throws CannotInsertRecord
     * @throws Exception
     */
    public function __invoke(
        Request $request,
        #[CurrentUser] User $user,
        BuildCsvDownloadItems $buildCsvDownloadItems
    ): Response {
        $formDto = $this->assetLibFormManager->populateLibraryFormDtoFromRequest($user, $this->libraryFormDto, $request);

        $queryResponseDto = $this->assetLibQueryBuilder->search($user, $formDto);

        $csvWriter = Writer::createFromString('');

        $csvData = ($buildCsvDownloadItems)($queryResponseDto?->getObjects());

        $csvWriter->insertAll($csvData);

        $response = new StreamedResponse(function () use ($csvWriter) {
            $output = fopen('php://output', 'w');

            if (is_resource($output)) {
                fwrite($output, $csvWriter->toString());
                fclose($output);
            }
        });

        $response->headers->set('Content-Type', 'text/csv');
        $response->headers->set('Content-Disposition', 'attachment; filename="export.csv"');

        return $response;
    }
}
