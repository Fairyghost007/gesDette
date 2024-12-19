<?php

namespace App\Controller;

use App\Entity\Client;
use App\Entity\Users;
use App\Entity\Dette;
use App\Form\ClientType;
use App\Form\UserType;
use App\Form\ClientTelephoneType;
use App\Form\DetteType;
use Doctrine\ORM\EntityManagerInterface;
use App\Repository\ClientRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;
use Symfony\Component\Mailer\Mailer;
use Symfony\Component\Mailer\Transport;
use Symfony\Component\HttpFoundation\JsonResponse;


use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasher;

class ClientController extends AbstractController
{

    #[Route('/clients', name: 'clients.index', methods: ['GET'])]
    public function clientIndex(): Response
    {
        return $this->render('views/client/index.html');
    }

    #[Route('/api/clients', name: 'api.clients.index', methods: ['GET'])]
    public function index(Request $request, ClientRepository $clientRepository): JsonResponse
    {
        $limit = $request->query->getInt('limit', 2); // Default limit
        $page = $request->query->getInt('page', 1); // Default page
        $offset = ($page - 1) * $limit;

        $telephone = $request->query->get('telephone', null);

        // Fetch filtered or unfiltered clients
        if ($telephone) {
            $clients = $clientRepository->findBy(['telephone' => $telephone], null, $limit, $offset);
            $totalClients = count($clientRepository->findBy(['telephone' => $telephone]));
        } else {
            $clients = $clientRepository->findBy([], null, $limit, $offset);
            $totalClients = $clientRepository->count([]);
        }

        $totalPages = ceil($totalClients / $limit);

        // Use the repository helper to convert clients to an array
        $data = [
            'clients' => array_map(fn (Client $client) => [
                'id' => $client->getId(),
                'nom' => $client->getNom(),
                'telephone' => $client->getTelephone(),
                'addresse' => $client->getAddresse(),
                'email' => $client->getEmail(),
            ], $clients),
            'current_page' => $page,
            'total_pages' => $totalPages,
            'total_clients' => $totalClients,
        ];

        return $this->json($data);
    }

    #[Route('/client/store', name: 'clients.store', methods: ['GET', 'POST'])]
public function store(Request $request, EntityManagerInterface $entityManager, UserPasswordHasherInterface $encoder): Response
{
    if ($request->isMethod('GET')) {
        return $this->render('views/client/form.html');
    }

    $data = json_decode($request->getContent(), true);

    if ($data === null) {
        return new JsonResponse(['message' => 'Invalid JSON'], Response::HTTP_BAD_REQUEST);
    }

    if (!isset($data['nom'], $data['telephone'], $data['addresse'], $data['email'])) {
        return new JsonResponse(['message' => 'Invalid input fields'], Response::HTTP_BAD_REQUEST);
    }

    $client = new Client();
    $client->setNom($data['nom']);
    $client->setTelephone($data['telephone']);
    $client->setAddresse($data['addresse']);
    $client->setEmail($data['email']);

    if (isset($data['creerCompte']) && $data['creerCompte'] === 'on') {
        if (!isset($data['login'], $data['password'])) {
            return new JsonResponse(['message' => 'Invalid user fields'], Response::HTTP_BAD_REQUEST);
        }

        $user = new Users();
        $user->setLogin($data['login']);
        $user->setPassword($encoder->hashPassword($user, $data['password']));
        $user->setRoles(['CLIENT']);
        $client->setUsers($user);

        $entityManager->persist($user);
    }

    $entityManager->persist($client);
    $entityManager->flush();

    return new JsonResponse(['message' => 'Client added successfully'], Response::HTTP_CREATED);
}

    





}
