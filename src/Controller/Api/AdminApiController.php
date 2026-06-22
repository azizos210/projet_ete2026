<?php

namespace App\Controller\Api;

use App\Entity\Utilisateur;
use App\Entity\Medecin;
use App\Entity\Patient;
use App\Entity\Infirmier;
use App\Entity\RendezVous;
use App\Entity\Consultation;
use App\Entity\Facture;
use App\Entity\AuditLog;
use App\Entity\Notification;
use App\Enum\TypeNotificationEnum;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\SerializerInterface;

#[Route('/api/admin', name: 'api_admin_')]
class AdminApiController extends AbstractController
{
    public function __construct(
        private EntityManagerInterface $em,
        private SerializerInterface $serializer
    ) {}

    #[Route('/dashboard', name: 'dashboard', methods: ['GET'])]
    public function dashboard(): JsonResponse
    {
        $now = new \DateTime();
        $todayStart = (clone $now)->setTime(0, 0, 0);
        $todayEnd = (clone $now)->setTime(23, 59, 59);

        $totalPatients = $this->em->getRepository(Patient::class)->count([]);
        $totalMedecins = $this->em->getRepository(Medecin::class)->count([]);
        $totalInfirmiers = $this->em->getRepository(Infirmier::class)->count([]);
        $totalConsultations = $this->em->getRepository(Consultation::class)->count([]);

        $consultationsAujourdhui = $this->em->getRepository(Consultation::class)
            ->createQueryBuilder('c')
            ->select('COUNT(c.id)')
            ->where('c.dateConsultation BETWEEN :start AND :end')
            ->setParameter('start', $todayStart)
            ->setParameter('end', $todayEnd)
            ->getQuery()
            ->getSingleScalarResult();

        $rdvsAujourdhui = $this->em->getRepository(RendezVous::class)
            ->createQueryBuilder('r')
            ->select('COUNT(r.id)')
            ->where('r.dateHeure BETWEEN :start AND :end')
            ->setParameter('start', $todayStart)
            ->setParameter('end', $todayEnd)
            ->getQuery()
            ->getSingleScalarResult();

        $prochainsRdvs = $this->em->getRepository(RendezVous::class)
            ->findBy(['statut' => 'CONFIRME'], ['dateHeure' => 'ASC'], 10);

        $facturesEnAttente = $this->em->getRepository(Facture::class)
            ->createQueryBuilder('f')
            ->select('COUNT(f.id)')
            ->where('f.statut = :statut')
            ->setParameter('statut', 'EN_ATTENTE')
            ->getQuery()
            ->getSingleScalarResult();

        $chiffreAffaireMois = $this->em->getRepository(Facture::class)
            ->createQueryBuilder('f')
            ->select('COALESCE(SUM(f.montant), 0)')
            ->where('f.dateEmission >= :start')
            ->setParameter('start', (clone $now)->modify('first day of this month'))
            ->getQuery()
            ->getSingleScalarResult();

        return $this->json([
            'stats' => [
                'totalPatients' => $totalPatients,
                'totalMedecins' => $totalMedecins,
                'totalInfirmiers' => $totalInfirmiers,
                'totalConsultations' => $totalConsultations,
                'consultationsAujourdhui' => (int) $consultationsAujourdhui,
                'rdvsAujourdhui' => (int) $rdvsAujourdhui,
                'facturesEnAttente' => (int) $facturesEnAttente,
                'chiffreAffaireMois' => (float) $chiffreAffaireMois,
            ],
            'prochainsRdvs' => array_map(fn($r) => [
                'id' => $r->getId(),
                'dateHeure' => $r->getDateHeure()?->format('Y-m-d H:i'),
                'patient' => $r->getPatient()?->getUtilisateur()?->getNomComplet(),
                'medecin' => $r->getMedecin()?->getUtilisateur()?->getNomComplet(),
                'motif' => $r->getMotif(),
                'statut' => $r->getStatut()?->value,
            ], $prochainsRdvs),
        ]);
    }

    #[Route('/utilisateurs', name: 'utilisateurs', methods: ['GET'])]
    public function utilisateurs(): JsonResponse
    {
        $utilisateurs = $this->em->getRepository(Utilisateur::class)->findAll();
        return $this->json(array_map(fn($u) => [
            'id' => $u->getId(),
            'email' => $u->getEmail(),
            'nom' => $u->getNom(),
            'prenom' => $u->getPrenom(),
            'telephone' => $u->getTelephone(),
            'roles' => $u->getRoles(),
            'actif' => $u->isActif(),
            'dateCreation' => $u->getDateCreation()?->format('Y-m-d H:i'),
            'derniereConnexion' => $u->getDerniereConnexion()?->format('Y-m-d H:i'),
            'profil' => $u->getMedecin() ? 'medecin' : ($u->getPatient() ? 'patient' : ($u->getInfirmier() ? 'infirmier' : ($u->getSecretaireMedicale() ? 'secretaire' : ($u->getAdministrateur() ? 'administrateur' : ($u->getDirecteurMedical() ? 'directeur_medical' : 'aucun'))))),
        ], $utilisateurs));
    }

    #[Route('/statistiques', name: 'statistiques', methods: ['GET'])]
    public function statistiques(): JsonResponse
    {
        $now = new \DateTime();

        $consultationsParMois = $this->em->getRepository(Consultation::class)
            ->createQueryBuilder('c')
            ->select("DATE_FORMAT(c.dateConsultation, '%Y-%m') as mois, COUNT(c.id) as total")
            ->where('c.dateConsultation >= :date')
            ->setParameter('date', (clone $now)->modify('-12 months'))
            ->groupBy('mois')
            ->orderBy('mois', 'ASC')
            ->getQuery()
            ->getResult();

        $rdvsParStatut = $this->em->getRepository(RendezVous::class)
            ->createQueryBuilder('r')
            ->select('r.statut, COUNT(r.id) as total')
            ->groupBy('r.statut')
            ->getQuery()
            ->getResult();

        $patientsParMois = $this->em->getRepository(Utilisateur::class)
            ->createQueryBuilder('u')
            ->select("DATE_FORMAT(u.dateCreation, '%Y-%m') as mois, COUNT(u.id) as total")
            ->where('u.dateCreation >= :date')
            ->setParameter('date', (clone $now)->modify('-12 months'))
            ->groupBy('mois')
            ->orderBy('mois', 'ASC')
            ->getQuery()
            ->getResult();

        return $this->json([
            'consultationsParMois' => $consultationsParMois,
            'rdvsParStatut' => $rdvsParStatut,
            'patientsParMois' => $patientsParMois,
        ]);
    }

    #[Route('/audit-logs', name: 'audit_logs', methods: ['GET'])]
    public function auditLogs(): JsonResponse
    {
        $logs = $this->em->getRepository(AuditLog::class)->findBy([], ['dateAction' => 'DESC'], 100);
        return $this->json(array_map(fn($l) => [
            'id' => $l->getId(),
            'utilisateur' => $l->getUtilisateur()?->getNomComplet(),
            'action' => $l->getAction(),
            'entite' => $l->getEntite(),
            'entiteId' => $l->getEntiteId(),
            'details' => $l->getDetails(),
            'dateAction' => $l->getDateAction()?->format('Y-m-d H:i:s'),
            'adresseIp' => $l->getAdresseIp(),
        ], $logs));
    }

    #[Route('/notifications', name: 'notifications_envoyer', methods: ['POST'])]
    public function envoyerNotification(Request $request): JsonResponse
    {
        $data = json_decode($request->getContent(), true);
        if (empty($data['utilisateurId']) || empty($data['contenu'])) {
            return $this->json(['message' => 'Champs requis manquants'], Response::HTTP_BAD_REQUEST);
        }

        $utilisateur = $this->em->getRepository(Utilisateur::class)->find($data['utilisateurId']);
        if (!$utilisateur) {
            return $this->json(['message' => 'Utilisateur non trouvé'], Response::HTTP_NOT_FOUND);
        }

        $notification = new Notification();
        $notification->setDestinataire($utilisateur);
        $notification->setContenu($data['contenu']);
        $notification->setType(TypeNotificationEnum::SYSTEME);
        $notification->setLu(false);

        $this->em->persist($notification);
        $this->em->flush();

        return $this->json(['message' => 'Notification envoyée', 'id' => $notification->getId()], Response::HTTP_CREATED);
    }

    #[Route('/medecins', name: 'medecins', methods: ['GET'])]
    public function medecins(): JsonResponse
    {
        $medecins = $this->em->getRepository(Medecin::class)->findAll();
        return $this->json(array_map(fn($m) => [
            'id' => $m->getId(),
            'nomComplet' => $m->getUtilisateur()->getNomComplet(),
            'email' => $m->getUtilisateur()->getEmail(),
            'specialite' => $m->getSpecialite(),
            'numeroOrdre' => $m->getNumeroOrdre(),
            'telephone' => $m->getUtilisateur()->getTelephone(),
            'actif' => $m->isActif(),
        ], $medecins));
    }

    #[Route('/patients', name: 'patients_list', methods: ['GET'])]
    public function patients(): JsonResponse
    {
        $patients = $this->em->getRepository(Patient::class)->findAll();
        return $this->json(array_map(fn($p) => [
            'id' => $p->getId(),
            'nom' => $p->getUtilisateur()->getNom(),
            'prenom' => $p->getUtilisateur()->getPrenom(),
            'email' => $p->getUtilisateur()->getEmail(),
            'telephone' => $p->getUtilisateur()->getTelephone(),
            'dateNaissance' => $p->getDateNaissance()?->format('Y-m-d'),
            'genre' => $p->getGenre()?->value,
            'groupeSanguin' => $p->getGroupeSanguin(),
        ], $patients));
    }
}
