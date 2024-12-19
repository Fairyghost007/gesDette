<?php

namespace App\Controller;

use App\Entity\Article;
use App\Form\ArticleLibelleType;
use App\Repository\ArticleRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\ORM\EntityManagerInterface;
use App\Form\ArticleType;
use Symfony\Component\HttpFoundation\JsonResponse;


class ArticleController extends AbstractController
{


    #[Route('/articles', name: 'articles.index', methods: ['GET'])]
    public function index(): Response
    {
        return $this->render('views/article/index.html');
    }
    #[Route('/api/client', name: 'api_client', methods: ['GET'])]
    public function getArticles(ArticleRepository $articleRepository): JsonResponse
    {
        $articles = $articleRepository->findAll();

        $data = array_map(function ($article) {
            return [
                'id' => $article->getId(),
                'libelle' => $article->getLibelle(),
                'prix' => $article->getPrix(),
                'qteStock' => $article->getQteStock(),
            ];
        }, $articles);

        return new JsonResponse($data);
    }

    #[Route('/article/store', name: 'article.store', methods: ['GET', 'POST'])]
    public function store(Request $request, EntityManagerInterface $entityManager): Response
    {
        if ($request->isMethod('GET')) {
            return $this->render('views/article/form.html');
        }

        $data = json_decode($request->getContent(), true);

        // Log the incoming data
        if ($data === null) {
            return new JsonResponse(['message' => 'Invalid JSON'], Response::HTTP_BAD_REQUEST);
        }

        if (!isset($data['libelle'], $data['prix'], $data['qteStock'])) {
            return new JsonResponse(['message' => 'Invalid input fields'], Response::HTTP_BAD_REQUEST);
        }

        $article = new Article();
        $article->setLibelle($data['libelle']);
        $article->setPrix((float)$data['prix']);
        $article->setQteStock((int)$data['qteStock']);

        $entityManager->persist($article);
        $entityManager->flush();

        return new JsonResponse(['message' => 'Article added successfully'], Response::HTTP_CREATED);
    }
}
