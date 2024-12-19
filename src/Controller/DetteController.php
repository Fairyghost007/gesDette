<?php

namespace App\Controller;

use App\Entity\Client;
use App\Entity\Dette;
use App\Entity\DetailDette;
use App\Entity\Article;
use App\Repository\ClientRepository;
use App\Repository\DetteRepository;
use App\Repository\ArticleRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class DetteController extends AbstractController
{
    #[Route('/dette/store', name: 'dette.store', methods: ['GET', 'POST'])]
    public function store(Request $request, EntityManagerInterface $entityManager, DetteRepository $detteRepository, ClientRepository $clientRepository, ArticleRepository $articleRepository): Response
    {
        if ($request->isMethod('GET')) {
            return $this->render('views/dette/form.html');
        }

        try {
            $data = json_decode($request->getContent(), true);

            if ($data === null) {
                return new JsonResponse(['message' => 'Invalid JSON'], Response::HTTP_BAD_REQUEST);
            }

            if (!isset($data['montant'], $data['montantVerser'], $data['detailDettes'])) {
                return new JsonResponse(['message' => 'Invalid input fields'], Response::HTTP_BAD_REQUEST);
            }

            $dette = new Dette();
            $dette->setMontant($data['montant']);
            $dette->setMontantVerser($data['montantVerser']);
            
            $client = $clientRepository->find(3);
            if (!$client) {
                return new JsonResponse(['message' => 'Client with ID 3 not found'], Response::HTTP_BAD_REQUEST);
            }
            $dette->setClient($client);

            $totalMontant = 0;
            foreach ($data['detailDettes'] as $detailDetteData) {
                $detailDette = new DetailDette();
                $article = $articleRepository->find($detailDetteData['articleId']);
                if (!$article) {
                    return new JsonResponse([
                        'message' => 'Article not found', 
                        'articleId' => $detailDetteData['articleId']
                    ], Response::HTTP_BAD_REQUEST);
                }
                
                $detailDette->setArticle($article);
                $detailDette->setQuantiteDette($detailDetteData['quantite']);
                $montant = $article->getPrix() * $detailDetteData['quantite'];
                $detailDette->setMontant($montant);
                $totalMontant += $montant;
                $dette->addDetailDette($detailDette);
            }

            // Verify that the calculated total matches the submitted total
            if (abs($totalMontant - $data['montant']) > 0.01) { // Using 0.01 to handle floating point comparison
                return new JsonResponse([
                    'message' => 'Total montant mismatch',
                    'calculated' => $totalMontant,
                    'submitted' => $data['montant']
                ], Response::HTTP_BAD_REQUEST);
            }

            $entityManager->persist($dette);
            $entityManager->flush();

            return new JsonResponse(['message' => 'Dette added successfully'], Response::HTTP_CREATED);
            
        } catch (\Exception $e) {
            return new JsonResponse([
                'message' => 'An error occurred while processing the dette',
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }


    #[Route('/api/dettes', name: 'api.dettes.index', methods: ['GET'])]
    public function index(DetteRepository $detteRepository): JsonResponse
    {
        $dettes = $detteRepository->findAll();

        $data = array_map(function (Dette $dette) {
            return [
                'id' => $dette->getId(),
                'montant' => $dette->getMontant(),
                'montantVerser' => $dette->getMontantVerser(),
                'client' => [
                    'id' => $dette->getClient()->getId(),
                    'nom' => $dette->getClient()->getNom(),
                ],
                'detailDettes' => array_map(function (DetailDette $detailDette) {
                    return [
                        'id' => $detailDette->getId(),
                        'article' => [
                            'id' => $detailDette->getArticle()->getId(),
                            'libelle' => $detailDette->getArticle()->getLibelle(),
                        ],
                        'quantiteDette' => $detailDette->getQuantiteDette(),
                        'montant' => $detailDette->getMontant(),
                    ];
                }, $dette->getDetailDettes()->toArray()),
            ];
        }, $dettes);

        return $this->json(['dettes' => $data]);
    }
    
    #[Route('/dettes', name: 'dettes.index', methods: ['GET'])]
    public function dettesIndex(DetteRepository $detteRepository): Response
    {
        return $this->render('views/dette/index.html');
    }

    #[Route('/api/articles', name: 'api.articles.index', methods: ['GET'])]
    public function getArticles(ArticleRepository $articleRepository): JsonResponse
    {
        $articles = $articleRepository->findAll();

        $data = array_map(function (Article $article) {
            return [
                'id' => $article->getId(),
                'libelle' => $article->getLibelle(),
                'prix' => $article->getPrix(),
                'qteStock' => $article->getQteStock(),
            ];
        }, $articles);

        return $this->json(['articles' => $data]);
    }
}
