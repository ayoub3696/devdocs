<?php

namespace App\DataFixtures;

use App\Entity\User;
use App\Entity\Category;
use App\Entity\Tag;
use App\Entity\Tutorial;
use App\Entity\Comment;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class AppFixtures extends Fixture
{
    public function __construct(
        private UserPasswordHasherInterface $passwordHasher
    ) {
    }

    public function load(ObjectManager $manager): void
    {
        // Users
        $users = [];

        $userData = [
            ['Ayoub', 'ayoub@devdocs.tn'],
            ['Ghofrane', 'ghofrane@devdocs.tn'],
            ['Admin', 'admin@devdocs.tn'],
        ];

        foreach ($userData as [$pseudo, $email]) {
            $user = new User();
            $user->setPseudonyme($pseudo);
            $user->setEmail($email);
            $user->setRoles($pseudo === 'Admin' ? ['ROLE_ADMIN'] : ['ROLE_USER']);
            $user->setPassword($this->passwordHasher->hashPassword($user, 'Password123!'));

            $manager->persist($user);
            $users[] = $user;
        }

        // Categories
        $categories = [];

        $categoryData = [
            ['PHP', 'Tutoriels sur le langage PHP.'],
            ['Symfony', 'Tutoriels sur le framework Symfony.'],
            ['JavaScript', 'Tutoriels sur JavaScript et le développement web.'],
            ['Python', 'Tutoriels pour apprendre Python.'],
            ['Database', 'Tutoriels sur les bases de données.'],
        ];

        foreach ($categoryData as [$name, $description]) {
            $category = new Category();
            $category->setName($name);
            $category->setDescription($description);

            $manager->persist($category);
            $categories[] = $category;
        }

        // Tags
        $tags = [];

        $tagNames = [
            'Débutant', 'Avancé', 'Backend', 'Frontend', 'API',
            'MySQL', 'Doctrine', 'Programmation', 'Web', 'Framework'
        ];

        foreach ($tagNames as $name) {
            $tag = new Tag();
            $tag->setName($name);

            $manager->persist($tag);
            $tags[] = $tag;
        }

        // Tutorials
        $tutorials = [];

        for ($i = 1; $i <= 15; $i++) {
            $tutorial = new Tutorial();
            $tutorial->setTitle("Tutoriel DevDocs $i");
            $tutorial->setSlug("tutoriel-devdocs-$i");
            $tutorial->setContent(
                "Ce tutoriel numéro $i présente les notions essentielles du développement web. Il explique les concepts avec des exemples pratiques pour faciliter l'apprentissage."
            );
            $tutorial->setCreatedAt(new \DateTimeImmutable("-$i days"));
            $tutorial->setAuteur($users[$i % 2]);
            $tutorial->setCategory($categories[($i - 1) % 5]);

            $tutorial->addTag($tags[($i - 1) % 10]);
            $tutorial->addTag($tags[$i % 10]);

            $manager->persist($tutorial);
            $tutorials[] = $tutorial;
        }

        // Comments
        for ($i = 1; $i <= 30; $i++) {
            $comment = new Comment();
            $comment->setContent(
                "Commentaire numéro $i : ce tutoriel est intéressant et permet de mieux comprendre les notions présentées."
            );
            $comment->setCreatedAt(new \DateTimeImmutable("-$i hours"));
            $comment->setAuteur($users[$i % 2]);
            $comment->setTutorial($tutorials[($i - 1) % 15]);

            $manager->persist($comment);
        }

        $manager->flush();
    }
}