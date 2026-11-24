<?php

namespace App\Controller;

use App\Repository\CategoryRepository;
use App\Repository\TutorialRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class HomeController extends AbstractController
{
    #[Route('/', name: 'app_home')]
    public function index(
        TutorialRepository $tutorialRepository,
        CategoryRepository $categoryRepository
    ): Response {
        $tutorials = $tutorialRepository->findBy(
            [],
            ['createdAt' => 'DESC'],
            6
        );

        $categories = $categoryRepository->findBy(
            [],
            ['name' => 'ASC'],
            6
        );

        return $this->render('home/index.html.twig', [
            'tutorials' => $tutorials,
            'categories' => $categories,
        ]);
    }
}