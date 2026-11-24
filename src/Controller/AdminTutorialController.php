<?php

namespace App\Controller;

use App\Repository\TutorialRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/admin/tutorials')]
#[IsGranted('ROLE_ADMIN')]
class AdminTutorialController extends AbstractController
{
    #[Route('/', name: 'app_admin_tutorials', methods: ['GET'])]
    public function index(TutorialRepository $repository): Response
    {
        return $this->render('admin/tutorials/index.html.twig', [
            'tutorials' => $repository->findBy([], ['createdAt' => 'DESC']),
        ]);
    }

    #[Route('/{id}/delete', name: 'app_admin_tutorial_delete', methods: ['POST'])]
    public function delete(
        int $id,
        Request $request,
        TutorialRepository $repository,
        EntityManagerInterface $entityManager
    ): Response {
        $tutorial = $repository->find($id);

        if (!$tutorial) {
            throw $this->createNotFoundException('Tutoriel introuvable.');
        }

        if (!$this->isCsrfTokenValid(
            'admin_delete' . $tutorial->getId(),
            $request->request->get('_token')
        )) {
            throw $this->createAccessDeniedException('Jeton CSRF invalide.');
        }

        $entityManager->remove($tutorial);
        $entityManager->flush();

        $this->addFlash('success', 'Tutoriel supprimé avec succès.');

        return $this->redirectToRoute('app_admin_tutorials');
    }
}
