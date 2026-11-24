<?php

namespace App\Controller;

use App\Repository\UserRepository;
use App\Repository\CategoryRepository;
use App\Repository\TutorialRepository;
use App\Repository\CommentRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/admin')]
#[IsGranted('ROLE_ADMIN')]
class AdminController extends AbstractController
{
    #[Route('', name: 'app_admin')]
    public function index(
        UserRepository $userRepository,
        CategoryRepository $categoryRepository,
        TutorialRepository $tutorialRepository,
        CommentRepository $commentRepository
    ): Response {
        return $this->render('admin/index.html.twig', [
            'usersCount' => $userRepository->count([]),
            'categoriesCount' => $categoryRepository->count([]),
            'tutorialsCount' => $tutorialRepository->count([]),
            'commentsCount' => $commentRepository->count([]),
        ]);
    }
}
