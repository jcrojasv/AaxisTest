<?php

namespace App\Controller;

use App\Entity\User;
use App\Security\AccessTokenHandler;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\CurrentUser;

class AuthController extends AbstractController
{
    #[Route('/auth', name: 'app_auth_login')]
    public function index(#[CurrentUser] User $user, AccessTokenHandler $accessTokenHandler): JsonResponse
    {
        if (!$user) {
            return $this->json('Invalid credentials', Response::HTTP_UNAUTHORIZED);
        }

        return $this->json([
            'token' => $accessTokenHandler->createForUser($user)
        ]);
    }
}
