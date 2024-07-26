<?php

declare(strict_types=1);

namespace Froq\PortalBundle\Controller\Dashboard;

use Doctrine\DBAL\Driver\Exception;
use Froq\PortalBundle\Action\BuildMessengerCollection;
use Froq\PortalBundle\Security\IsAdmin;
use MembersBundle\Controller\AbstractController;
use Pimcore\Model\DataObject\User;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class MessengerController extends AbstractController
{
    /**
     * @throws Exception
     * @throws \Doctrine\DBAL\Exception
     */
    #[Route('/messenger', name: 'froq_dashboard.messenger', methods: Request::METHOD_GET)]
    public function __invoke(Request $request, IsAdmin $isAdmin, BuildMessengerCollection $buildMessengerCollection): Response
    {
        $currentUser = $this->getUser();

        if (!($currentUser instanceof User)) {
            throw $this->createAccessDeniedException();
        }

        if (!($isAdmin)($currentUser)) {
            throw $this->createAccessDeniedException();
        }

        $currentPage = $request->query->getInt('page', 1) ?? 1;
        $queueName = $request->query->get('queue_name', '') ?? '';

        return $this->render(
            view: '@FroqPortal/messages.html.twig',
            parameters: ['user' => $currentUser, ...($buildMessengerCollection)($currentPage, $queueName)->toArray()]
        );
    }
}
