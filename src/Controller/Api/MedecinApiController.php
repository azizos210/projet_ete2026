<?php

namespace App\Controller\Api;

use App\Entity\User;
use App\Entity\Medecin;
use App\Entity\Consultation;
use App\Entity\RendezVous;
use App\Entity\Prescription;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/api/medecin', name: 'api_medecin_')]
class MedecinApiController extends AbstractController
{
    public function __construct(
        private EntityManagerInterface $em,
    ) {}

    private function getMedecinFromUser(): ?Medecin
    {
        $user = $this->getUser();
        if (!$user) return null;
        $utilisateur = $this->em->getRepository(User::class)->findOneBy(['email' => $user->getEmail()]);
        return $this->em->getRepository(Medecin::class)->findOneBy(['utilisateur' => $utilisateur]);
    }

    #[Route('/dashboard', name: 'dashboard', methods: ['GET'])]
    public function dashboard(): JsonResponse
    {
        $medecin = $this->getMedecinFromUser();
        if (!$medecin) {
            return $this->json(['message' => 'Profil médecin non trouvé'], Response::HTTP_NOT_FOUND);
        }

        $now = new \DateTime();
        $todayStart = (clone $now)->setTime(0, 0, 0);
        $todayEnd = (clone $now)->setTime(23, 59, 59);

        $consultationsAujourdhui = $this->em->getRepository(Consultation::class)
            ->createQueryBuilder('c')
            ->select('COUNT(c.id)')
            ->where('c.medecin = :medecin')
            ->andWhere('c.dateConsultation BETWEEN :start AND :end')
            ->setParameter('medecin', $medecin)
            ->setParameter('start', $todayStart)
            ->setParameter('end', $todayEnd)
            ->getQuery()
            ->getSingleScalarResult();

        $prochainsRdvs = $this->em->getRepository(RendezVous::class)
            ->findBy(['medecin' => $medecin, 'statut' => 'CONFIRME'], ['dateHeure' => 'ASC'], 10);

        $patientsRecents = $this->em->getRepository(Consultation::class)
            ->createQueryBuilder('c')
            ->select('p')
            ->join('c.dossierMedical', 'dm')
            ->join('dm.patient', 'p')
            ->where('c.medecin = :medecin')
            ->setParameter('medecin', $medecin)
            ->orderBy('c.dateConsultation', 'DESC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult();

        return $this->json([
            'medecin' => [
                'id' => $medecin->getId(),
                'nomComplet' => $medecin->getUtilisateur()->getNomComplet(),
                'specialite' => $medecin->getSpecialite(),
                'email' => $medecin->getUtilisateur()->getEmail(),
                'telephone' => $medecin->getUtilisateur()->getTelephone(),
                'numeroOrdre' => $medecin->getNumeroOrdre(),
            ],
            'consultationsAujourdhui' => $consultationsAujourdhui,
            'prochainsRdvs' => array_map(fn($r) => [
                'id' => $r->getId(),
                'dateHeure' => $r->getDateHeure()?->format('Y-m-d H:i'),
                'patient' => $r->getPatient()?->getUtilisateur()?->getNomComplet(),
                'motif' => $r->getMotif(),
                'statut' => $r->getStatut()?->value,
            ], $prochainsRdvs),
            'patientsRecents' => array_map(fn($p) => [
                'id' => $p->getId(),
                'nom' => $p->getUtilisateur()->getNom(),
                'prenom' => $p->getUtilisateur()->getPrenom(),
                'email' => $p->getUtilisateur()->getEmail(),
                'telephone' => $p->getUtilisateur()->getTelephone(),
                'dateNaissance' => $p->getDateNaissance()?->format('Y-m-d'),
            ], $patientsRecents),
        ]);
    }

    #[Route('/consultations', name: 'consultations', methods: ['GET'])]
    public function consultations(): JsonResponse
    {
        $medecin = $this->getMedecinFromUser();
        if (!$medecin) {
            return $this->json(['message' => 'Médecin non trouvé'], Response::HTTP_NOT_FOUND);
        }

        $consultations = $this->em->getRepository(Consultation::class)
            ->findBy(['medecin' => $medecin], ['dateConsultation' => 'DESC']);

        return $this->json(array_map(fn($c) => [
            'id' => $c->getId(),
            'dateConsultation' => $c->getDateConsultation()?->format('Y-m-d H:i'),
            'patient' => $c->getDossierMedical()?->getPatient()?->getUtilisateur()?->getNomComplet(),
            'motif' => $c->getMotif(),
            'diagnostic' => $c->getDiagnostic(),
            'statut' => $c->getStatut()?->value,
        ], $consultations));
    }

    #[Route('/patients', name: 'patients', methods: ['GET'])]
    public function patients(): JsonResponse
    {
        $medecin = $this->getMedecinFromUser();
        if (!$medecin) {
            return $this->json(['message' => 'Médecin non trouvé'], Response::HTTP_NOT_FOUND);
        }

        $patients = $this->em->getRepository(Consultation::class)
            ->createQueryBuilder('c')
            ->select('p')
            ->join('c.dossierMedical', 'dm')
            ->join('dm.patient', 'p')
            ->where('c.medecin = :medecin')
            ->setParameter('medecin', $medecin)
            ->orderBy('p.id', 'DESC')
            ->getQuery()
            ->getResult();

        return $this->json(array_map(fn($p) => [
            'id' => $p->getId(),
            'nom' => $p->getUtilisateur()->getNom(),
            'prenom' => $p->getUtilisateur()->getPrenom(),
            'email' => $p->getUtilisateur()->getEmail(),
            'telephone' => $p->getUtilisateur()->getTelephone(),
            'dateNaissance' => $p->getDateNaissance()?->format('Y-m-d'),
            'genre' => $p->getGenre()?->value,
        ], $patients));
    }

    #[Route('/rendez-vous', name: 'rdv', methods: ['GET'])]
    public function rendezVous(): JsonResponse
    {
        $medecin = $this->getMedecinFromUser();
        if (!$medecin) {
            return $this->json(['message' => 'Médecin non trouvé'], Response::HTTP_NOT_FOUND);
        }

        $rdvs = $this->em->getRepository(RendezVous::class)
            ->findBy(['medecin' => $medecin], ['dateHeure' => 'DESC']);

        return $this->json(array_map(fn($r) => [
            'id' => $r->getId(),
            'dateHeure' => $r->getDateHeure()?->format('Y-m-d H:i'),
            'patient' => $r->getPatient()?->getUtilisateur()?->getNomComplet(),
            'motif' => $r->getMotif(),
            'statut' => $r->getStatut()?->value,
            'notes' => $r->getNotes(),
        ], $rdvs));
    }

    #[Route('/prescriptions', name: 'prescriptions', methods: ['GET'])]
    public function prescriptions(): JsonResponse
    {
        $medecin = $this->getMedecinFromUser();
        if (!$medecin) {
            return $this->json(['message' => 'Médecin non trouvé'], Response::HTTP_NOT_FOUND);
        }

        $prescriptions = $this->em->getRepository(Prescription::class)
            ->findBy(['medecin' => $medecin], ['datePrescription' => 'DESC']);

        return $this->json(array_map(fn($p) => [
            'id' => $p->getId(),
            'datePrescription' => $p->getDatePrescription()?->format('Y-m-d'),
            'patient' => $p->getConsultation()?->getDossierMedical()?->getPatient()?->getUtilisateur()?->getNomComplet(),
            'statut' => $p->getStatut()?->value,
            'lignes' => array_map(fn($l) => [
                'id' => $l->getId(),
                'medicament' => $l->getMedicament()?->getNom(),
                'dosage' => $l->getDosage(),
                'frequence' => $l->getFrequence(),
                'duree' => $l->getDuree(),
            ], $p->getLignes()->toArray()),
        ], $prescriptions));
    }
}
