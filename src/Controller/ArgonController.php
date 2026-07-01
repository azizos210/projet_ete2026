<?php

namespace App\Controller;

use App\Entity\Consultation;
use App\Entity\DocumentMedical;
use App\Entity\Patient;
use App\Entity\RendezVous;
use App\Entity\Utilisateur;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/api/argon')]
class ArgonController extends AbstractController
{
    public function __construct(
        private EntityManagerInterface $em,
    ) {}

    #[Route('/data', name: 'argon_data', methods: ['GET'])]
    public function data(): JsonResponse
    {
        $patient = $this->getPatientFromUser();

        if (!$patient) {
            $user = $this->getUser();
            if (!$user) {
                return $this->json([
                    'patient' => [
                        'nom' => 'Visiteur',
                        'prenom' => '',
                        'email' => '',
                        'telephone' => '--',
                        'dateNaissance' => null,
                        'genre' => null,
                        'groupeSanguin' => '--',
                        'allergies' => null,
                        'numeroSecuriteSociale' => null,
                    ],
                    'prochainsRdvs' => [],
                    'dernieresConsultations' => [],
                    'documentsRecents' => [],
                ]);
            }
            return $this->json([
                'patient' => [
                    'nom' => $user->getLastName() ?? '--',
                    'prenom' => $user->getFirstName() ?? '--',
                    'email' => $user->getEmail() ?? '--',
                    'telephone' => '--',
                    'dateNaissance' => null,
                    'genre' => null,
                    'groupeSanguin' => '--',
                    'allergies' => null,
                    'numeroSecuriteSociale' => null,
                ],
                'prochainsRdvs' => [],
                'dernieresConsultations' => [],
                'documentsRecents' => [],
            ]);
        }

        $utilisateur = $patient->getUtilisateur();
        $dossierMedical = $patient->getDossierMedical();

        $prochainsRdvs = $this->em->getRepository(RendezVous::class)
            ->findBy(['patient' => $patient, 'statut' => 'CONFIRME'], ['dateHeure' => 'ASC'], 5);
        $consultations = $dossierMedical ? $this->em->getRepository(Consultation::class)
            ->findBy(['dossierMedical' => $dossierMedical], ['dateConsultation' => 'DESC'], 5) : [];
        $documents = $dossierMedical ? $this->em->getRepository(DocumentMedical::class)
            ->findBy(['dossierMedical' => $dossierMedical], ['dateAjout' => 'DESC'], 5) : [];

        return $this->json([
            'patient' => [
                'id' => $patient->getId(),
                'nom' => $utilisateur->getNom(),
                'prenom' => $utilisateur->getPrenom(),
                'email' => $utilisateur->getEmail(),
                'telephone' => $utilisateur->getTelephone(),
                'dateNaissance' => $patient->getDateNaissance()?->format('Y-m-d'),
                'genre' => $patient->getGenre()?->value,
                'groupeSanguin' => $patient->getGroupeSanguin(),
                'allergies' => $patient->getAllergies(),
                'numeroSecuriteSociale' => $patient->getNumeroSecuriteSociale(),
            ],
            'prochainsRdvs' => array_map(fn($r) => [
                'id' => $r->getId(),
                'dateHeure' => $r->getDateHeure()?->format('Y-m-d H:i'),
                'medecin' => $r->getMedecin()?->getUtilisateur()?->getNomComplet(),
                'motif' => $r->getMotif(),
                'statut' => $r->getStatut()?->value,
            ], $prochainsRdvs),
            'dernieresConsultations' => array_map(fn($c) => [
                'id' => $c->getId(),
                'date' => $c->getDateConsultation()?->format('Y-m-d'),
                'medecin' => $c->getMedecin()?->getUtilisateur()?->getNomComplet(),
                'motif' => $c->getMotif(),
                'diagnostic' => $c->getDiagnostic(),
                'statut' => $c->getStatut()?->value,
            ], $consultations),
            'documentsRecents' => array_map(fn($d) => [
                'id' => $d->getId(),
                'titre' => $d->getTitre(),
                'type' => $d->getTypeDocument()?->value,
                'dateAjout' => $d->getDateAjout()?->format('Y-m-d'),
            ], $documents),
        ]);
    }

    private function getPatientFromUser(): ?Patient
    {
        $user = $this->getUser();
        if (!$user) return null;
        $utilisateur = $this->em->getRepository(Utilisateur::class)->findOneBy(['email' => $user->getEmail()]);
        if (!$utilisateur) return null;
        return $this->em->getRepository(Patient::class)->findOneBy(['utilisateur' => $utilisateur]);
    }
}
