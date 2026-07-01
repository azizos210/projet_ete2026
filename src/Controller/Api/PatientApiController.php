<?php

namespace App\Controller\Api;

use App\Entity\Consultation;
use App\Entity\DossierMedical;
use App\Entity\Patient;
use App\Entity\RendezVous;
use App\Entity\Utilisateur;
use App\Entity\DocumentMedical;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/api/patient', name: 'api_patient_')]
class PatientApiController extends AbstractController
{
    public function __construct(
        private EntityManagerInterface $em,
    ) {}

    private function getPatientFromUser(): ?Patient
    {
        $utilisateur = $this->getUser();
        if (!$utilisateur instanceof Utilisateur) {
            return null;
        }

        return $this->em->getRepository(Patient::class)->findOneBy(['utilisateur' => $utilisateur]);
    }

    #[Route('/dashboard', name: 'dashboard', methods: ['GET'])]
    public function dashboard(): JsonResponse
    {
        $patient = $this->getPatientFromUser();
        if (!$patient) {
            return $this->json(['message' => 'Profil patient non trouvé'], Response::HTTP_NOT_FOUND);
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

    #[Route('/rdv', name: 'rdv_list', methods: ['GET'])]
    public function rdvList(): JsonResponse
    {
        $patient = $this->getPatientFromUser();
        if (!$patient) {
            return $this->json(['message' => 'Patient non trouvé'], Response::HTTP_NOT_FOUND);
        }

        $rdvs = $this->em->getRepository(RendezVous::class)->findBy(
            ['patient' => $patient],
            ['dateHeure' => 'DESC']
        );

        return $this->json(array_map(fn($r) => [
            'id' => $r->getId(),
            'dateHeure' => $r->getDateHeure()?->format('Y-m-d H:i'),
            'medecin' => $r->getMedecin()?->getUtilisateur()?->getNomComplet(),
            'motif' => $r->getMotif(),
            'statut' => $r->getStatut()?->value,
            'notes' => $r->getNotes(),
        ], $rdvs));
    }

    #[Route('/dossier-medical', name: 'dossier_medical', methods: ['GET'])]
    public function dossierMedical(): JsonResponse
    {
        $patient = $this->getPatientFromUser();
        if (!$patient || !$patient->getDossierMedical()) {
            return $this->json(['message' => 'Dossier médical non trouvé'], Response::HTTP_NOT_FOUND);
        }

        $dossier = $patient->getDossierMedical();
        return $this->json([
            'id' => $dossier->getId(),
            'dateCreation' => $dossier->getDateCreation()?->format('Y-m-d'),
            'antecedents' => $dossier->getAntecedents(),
            'allergies' => $dossier->getAllergies(),
            'traitementsEnCours' => $dossier->getTraitementsEnCours(),
            'consultations' => array_map(fn($c) => [
                'id' => $c->getId(),
                'date' => $c->getDateConsultation()?->format('Y-m-d'),
                'medecin' => $c->getMedecin()?->getUtilisateur()?->getNomComplet(),
                'motif' => $c->getMotif(),
                'diagnostic' => $c->getDiagnostic(),
                'statut' => $c->getStatut()?->value,
            ], $dossier->getConsultations()->toArray()),
            'documents' => array_map(fn($d) => [
                'id' => $d->getId(),
                'titre' => $d->getTitre(),
                'type' => $d->getTypeDocument()?->value,
                'description' => $d->getDescription(),
                'dateAjout' => $d->getDateAjout()?->format('Y-m-d'),
            ], $dossier->getDocumentsMedicaux()->toArray()),
        ]);
    }
}
