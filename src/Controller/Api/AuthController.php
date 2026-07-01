<?php

namespace App\Controller\Api;

use App\Entity\User;
use App\Entity\Utilisateur;
use App\Entity\Administrateur;
use App\Entity\Medecin;
use App\Entity\Patient;
use App\Entity\Infirmier;
use App\Entity\SecretaireMedicale;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

#[Route('/api/auth', name: 'api_auth_')]
class AuthController extends AbstractController
{
    #[Route('/register', name: 'register', methods: ['POST'])]
    public function register(
        Request $request,
        EntityManagerInterface $em,
        UserPasswordHasherInterface $passwordHasher,
        ValidatorInterface $validator,
        SerializerInterface $serializer
    ): JsonResponse {
        $data = json_decode($request->getContent(), true);

        if (empty($data['email']) || empty($data['password']) || empty($data['firstName']) || empty($data['lastName'])) {
            return $this->json(['message' => 'Champs requis manquants'], Response::HTTP_BAD_REQUEST);
        }

        $existingUser = $em->getRepository(User::class)->findOneBy(['email' => $data['email']]);
        if ($existingUser) {
            return $this->json(['message' => 'Cet email est déjà utilisé'], Response::HTTP_CONFLICT);
        }

        $allowedRoles = ['ROLE_PATIENT', 'ROLE_MEDECIN', 'ROLE_ADMIN'];
        $requestedRole = $data['role'] ?? 'ROLE_USER';

        if (!in_array($requestedRole, $allowedRoles, true)) {
            $requestedRole = 'ROLE_USER';
        }

        $user = new User();
        $user->setEmail($data['email']);
        $user->setFirstName($data['firstName']);
        $user->setLastName($data['lastName']);
        $user->setPassword($passwordHasher->hashPassword($user, $data['password']));
        $user->setRoles([$requestedRole]);

        $errors = $validator->validate($user);
        if (count($errors) > 0) {
            return $this->json(['message' => (string) $errors], Response::HTTP_BAD_REQUEST);
        }

        $em->persist($user);

        $utilisateur = new Utilisateur();
        $utilisateur->setEmail($data['email']);
        $utilisateur->setNom($data['lastName']);
        $utilisateur->setPrenom($data['firstName']);
        $utilisateur->setPassword($passwordHasher->hashPassword($utilisateur, $data['password']));
        $utilisateur->setRoles([$requestedRole]);
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
            'user' => [
                'id' => $user->getId(),
                'email' => $user->getEmail(),
                'firstName' => $user->getFirstName(),
                'lastName' => $user->getLastName(),
                'roles' => $user->getRoles(),
            ],
        ], Response::HTTP_CREATED);
    }

    #[Route('/me', name: 'me', methods: ['GET'])]
    public function me(SerializerInterface $serializer): JsonResponse
    {
        $user = $this->getUser();
        if (!$user) {
            return $this->json(['message' => 'Non authentifié'], Response::HTTP_UNAUTHORIZED);
        }

        $data = [
            'id' => $user->getId(),
            'email' => $user->getEmail(),
            'firstName' => $user->getFirstName(),
            'lastName' => $user->getLastName(),
            'roles' => $user->getRoles(),
        ];

        return $this->json($data);
    }

    #[Route('/profile', name: 'profile_update', methods: ['PUT'])]
    public function updateProfile(
        Request $request,
        EntityManagerInterface $em,
        SerializerInterface $serializer
    ): JsonResponse {
        $user = $this->getUser();
        if (!$user) {
            return $this->json(['message' => 'Non authentifié'], Response::HTTP_UNAUTHORIZED);
        }

        $data = json_decode($request->getContent(), true);

        if (isset($data['firstName'])) {
            $user->setFirstName($data['firstName']);
        }
        if (isset($data['lastName'])) {
            $user->setLastName($data['lastName']);
        }
        if (isset($data['email'])) {
            $existing = $em->getRepository(User::class)->findOneBy(['email' => $data['email']]);
            if ($existing && $existing->getId() !== $user->getId()) {
                return $this->json(['message' => 'Cet email est déjà utilisé'], Response::HTTP_CONFLICT);
            }
            $user->setEmail($data['email']);
        }

        $em->flush();

        return $this->json([
            'id' => $user->getId(),
            'email' => $user->getEmail(),
            'firstName' => $user->getFirstName(),
            'lastName' => $user->getLastName(),
            'roles' => $user->getRoles(),
        ]);
    }
}
