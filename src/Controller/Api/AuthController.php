<?php

namespace App\Controller\Api;

use App\Entity\Administrateur;
use App\Entity\Medecin;
use App\Entity\Patient;
use App\Entity\User;
use App\Entity\Utilisateur;
use App\Service\UserSyncService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Validator\Validator\ValidatorInterface;

#[Route('/api/auth', name: 'api_auth_')]
class AuthController extends AbstractController
{
    #[Route('/register', name: 'register', methods: ['POST'])]
    public function register(
        Request $request,
        EntityManagerInterface $em,
        UserSyncService $userSyncService,
        ValidatorInterface $validator,
    ): JsonResponse {
        $data = json_decode($request->getContent(), true);

        if (empty($data['email']) || empty($data['password']) || empty($data['firstName']) || empty($data['lastName'])) {
            return $this->json(['message' => 'Champs requis manquants'], Response::HTTP_BAD_REQUEST);
        }

        $email = $userSyncService->normalizeEmail($data['email']);

        if ($em->getRepository(Utilisateur::class)->findOneBy(['email' => $email])) {
            return $this->json(['message' => 'Cet email est déjà utilisé'], Response::HTTP_CONFLICT);
        }

        $allowedRoles = ['ROLE_PATIENT', 'ROLE_MEDECIN', 'ROLE_ADMIN'];
        $requestedRole = $data['role'] ?? 'ROLE_USER';

        if (!in_array($requestedRole, $allowedRoles, true)) {
            $requestedRole = 'ROLE_USER';
        }

        $user = new User();
        $user->setEmail($email);
        $user->setFirstName(trim($data['firstName']));
        $user->setLastName(trim($data['lastName']));
        $user->setRoles([$requestedRole]);

        $errors = $validator->validate($user);
        if (count($errors) > 0) {
            return $this->json(['message' => (string) $errors], Response::HTTP_BAD_REQUEST);
        }

        $utilisateur = new Utilisateur();
        $utilisateur->setEmail($email);
        $utilisateur->setNom(trim($data['lastName']));
        $utilisateur->setPrenom(trim($data['firstName']));
        $utilisateur->setRoles([$requestedRole]);

        $userSyncService->hashAndSetPassword($user, $utilisateur, $data['password']);

        $em->persist($user);
        $em->persist($utilisateur);

        $roleEntity = match ($requestedRole) {
            'ROLE_PATIENT' => new Patient(),
            'ROLE_MEDECIN' => new Medecin(),
            'ROLE_ADMIN' => new Administrateur(),
            default => null,
        };

        if ($roleEntity !== null) {
            $roleEntity->setUtilisateur($utilisateur);
            if ($roleEntity instanceof Medecin) {
                $roleEntity->setSpecialite('Généraliste');
                $roleEntity->setNumeroOrdre('TEMP-' . uniqid());
            }
            $em->persist($roleEntity);
        }

        $em->flush();

        return $this->json([
            'message' => 'Utilisateur créé avec succès',
            'user' => $this->serializeUtilisateur($utilisateur),
        ], Response::HTTP_CREATED);
    }

    #[Route('/me', name: 'me', methods: ['GET'])]
    public function me(): JsonResponse
    {
        $utilisateur = $this->getAuthenticatedUtilisateur();
        if (!$utilisateur) {
            return $this->json(['message' => 'Non authentifié'], Response::HTTP_UNAUTHORIZED);
        }

        return $this->json($this->serializeUtilisateur($utilisateur));
    }

    #[Route('/profile', name: 'profile_update', methods: ['PUT'])]
    public function updateProfile(
        Request $request,
        EntityManagerInterface $em,
        UserSyncService $userSyncService,
    ): JsonResponse {
        $utilisateur = $this->getAuthenticatedUtilisateur();
        if (!$utilisateur) {
            return $this->json(['message' => 'Non authentifié'], Response::HTTP_UNAUTHORIZED);
        }

        $data = json_decode($request->getContent(), true);

        if (isset($data['firstName'])) {
            $utilisateur->setPrenom(trim($data['firstName']));
        }
        if (isset($data['lastName'])) {
            $utilisateur->setNom(trim($data['lastName']));
        }
        if (isset($data['email'])) {
            $email = $userSyncService->normalizeEmail($data['email']);
            $existing = $em->getRepository(Utilisateur::class)->findOneBy(['email' => $email]);
            if ($existing && $existing->getId() !== $utilisateur->getId()) {
                return $this->json(['message' => 'Cet email est déjà utilisé'], Response::HTTP_CONFLICT);
            }
            $utilisateur->setEmail($email);
        }

        $userSyncService->syncUserFromUtilisateur($utilisateur);
        $em->flush();

        return $this->json($this->serializeUtilisateur($utilisateur));
    }

    private function getAuthenticatedUtilisateur(): ?Utilisateur
    {
        $user = $this->getUser();

        return $user instanceof Utilisateur ? $user : null;
    }

    private function serializeUtilisateur(Utilisateur $utilisateur): array
    {
        return [
            'id' => $utilisateur->getId(),
            'email' => $utilisateur->getEmail(),
            'firstName' => $utilisateur->getPrenom(),
            'lastName' => $utilisateur->getNom(),
            'roles' => $utilisateur->getRoles(),
        ];
    }
}
