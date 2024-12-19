<?php

namespace App\Controller;

use App\Entity\Users;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

class SecurityController extends AbstractController
{
    #[Route(['/login', '/'], name: 'app_login', methods: ['GET', 'POST'])]
    public function login(Request $request, AuthenticationUtils $authenticationUtils, EntityManagerInterface $entityManager): Response
    {
        if ($request->isMethod('GET')) {
            return $this->render('views/security/login.html');
        }

        $data = json_decode($request->getContent(), true);
        
        if (!$data || !isset($data['login'], $data['password'])) {
            return new JsonResponse(['message' => 'Missing credentials'], Response::HTTP_BAD_REQUEST);
        }

        $user = $entityManager->getRepository(Users::class)->findOneBy(['login' => $data['login']]);

        if (!$user || !password_verify($data['password'], $user->getPassword())) {
            return new JsonResponse(['message' => 'Invalid credentials'], Response::HTTP_UNAUTHORIZED);
        }

        // Create session for user
        $request->getSession()->set('user_id', $user->getId());

        return new JsonResponse(['message' => 'Login successful']);
    }

    #[Route('/logout', name: 'app_logout')]
    public function logout(Request $request): JsonResponse
    {
        $request->getSession()->invalidate();
        return new JsonResponse(['message' => 'Logged out successfully']);
    }

    #[Route('/register', name: 'app_register', methods: ['GET', 'POST'])]
    public function register(
        Request $request, 
        UserPasswordHasherInterface $passwordHasher,
        EntityManagerInterface $entityManager
    ): Response {
        if ($request->isMethod('GET')) {
            return $this->render('views/security/register.html');
        }

        $data = json_decode($request->getContent(), true);
        
        if (!$data || !isset($data['email'], $data['password'], $data['fullName'])) {
            return new JsonResponse(['message' => 'Missing required fields'], Response::HTTP_BAD_REQUEST);
        }

        // Check if user already exists
        $existingUser = $entityManager->getRepository(Users::class)->findOneBy(['email' => $data['email']]);
        if ($existingUser) {
            return new JsonResponse(['message' => 'Email already registered'], Response::HTTP_BAD_REQUEST);
        }

        $user = new Users();
        $user->setLogin($data['login']);
        
        // Hash the password
        $hashedPassword = $passwordHasher->hashPassword($user, $data['password']);
        $user->setPassword($hashedPassword);
        
        // Set default role
        $user->setRoles(['ROLE_USER']);
        
        $entityManager->persist($user);
        $entityManager->flush();
        
        return new JsonResponse(['message' => 'Registration successful'], Response::HTTP_CREATED);
    }
    #[Route('/404', name: 'app_404')]
    public function show404(): Response
    {
        return $this->render('views/security/404.html', [], new Response(null, Response::HTTP_NOT_FOUND));
    }
}