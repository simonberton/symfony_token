<?php

namespace App\Controller;

use App\Entity\AccessToken;
use App\Repository\ApiUserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class APILoginController extends AbstractController
{
    #[Route('/api-login', name: 'api_login', methods: ['POST'])]
    public function login(
        Request $request,
        UserPasswordHasherInterface $hasher,
        ApiUserRepository $userRepo,
        EntityManagerInterface $em
    ): JsonResponse {
        $data = json_decode($request->getContent(), true);
        $user = $userRepo->findOneBy(['name' => $data['username']]);

        if (!$user || !$hasher->isPasswordValid($user, $data['password'])) {
            return $this->json(['error' => 'Invalid credentials'], 401);
        }

        $token = new AccessToken();
        $token->setUser($user);
        $token->setValue(bin2hex(random_bytes(32)));

        $token->setExpiresAt(new \DateTimeImmutable('+1 hour'));

        $em->persist($token);
        $em->flush();

        return $this->json(['token' => $token->getValue()]);
    }
}
