<?php

namespace App\Controller;

use App\Entity\Tutorial;
use App\Form\TutorialType;
use App\Repository\TutorialRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use App\Security\Voter\TutorialVoter;
use Symfony\Component\String\Slugger\SluggerInterface;
use App\Repository\CategoryRepository;



#[Route('/tutorial')]
class TutorialController extends AbstractController
{
    #[Route('/', name: 'app_tutorial_index', methods: ['GET'])]
public function index(
    Request $request,
    TutorialRepository $tutorialRepository,
    CategoryRepository $categoryRepository
): Response {
    $search = trim($request->query->get('q', ''));

    $categoryId = $request->query->get('category');
    $selectedCategory = null;

    if ($categoryId !== null && ctype_digit($categoryId)) {
        $selectedCategory = $categoryRepository->find((int) $categoryId);
    }

    $tutorials = $tutorialRepository->findByFilters(
        $search,
        $selectedCategory
    );

    return $this->render('tutorial/index.html.twig', [
        'tutorials' => $tutorials,
        'categories' => $categoryRepository->findBy([], ['name' => 'ASC']),
        'search' => $search,
        'selectedCategory' => $selectedCategory,
    ]);
}

    #[Route('/{id}', name: 'app_tutorial_show', requirements: ['id' => '\d+'], methods: ['GET'])]
    public function show(Tutorial $tutorial): Response
    {
        return $this->render('tutorial/show.html.twig', [
            'tutorial' => $tutorial,
        ]);
    }

    #[Route('/new', name: 'app_tutorial_new', methods: ['GET', 'POST'])]
    #[IsGranted('ROLE_USER')]
    public function new(
    Request $request,
    EntityManagerInterface $entityManager,
    SluggerInterface $slugger
): Response
    {
        $tutorial = new Tutorial();
        $tutorial->setAuteur($this->getUser());
        $tutorial->setCreatedAt(new \DateTimeImmutable());

        $form = $this->createForm(TutorialType::class, $tutorial);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($tutorial);
            $entityManager->flush();

            return $this->redirectToRoute('app_tutorial_index');
        }

        return $this->render('tutorial/new.html.twig', [
            'form' => $form,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_tutorial_edit', requirements: ['id' => '\d+'], methods: ['GET', 'POST'])]
    #[IsGranted('ROLE_USER')]
    public function edit(Request $request, Tutorial $tutorial, EntityManagerInterface $entityManager): Response
    {
        $this->denyAccessUnlessGranted(TutorialVoter::EDIT, $tutorial);

        $form = $this->createForm(TutorialType::class, $tutorial);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_tutorial_index');
        }

        return $this->render('tutorial/edit.html.twig', [
            'form' => $form,
            'tutorial' => $tutorial,
        ]);
    }

    #[Route('/{id}/delete', name: 'app_tutorial_delete', requirements: ['id' => '\d+'], methods: ['POST'])]
    #[IsGranted('ROLE_USER')]
    public function delete(Request $request, Tutorial $tutorial, EntityManagerInterface $entityManager): Response
    {
        $this->denyAccessUnlessGranted('DELETE', $tutorial);

        if ($this->isCsrfTokenValid('delete'.$tutorial->getId(), $request->getPayload()->get('_token'))) {
            $entityManager->remove($tutorial);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_tutorial_index');
    }
}