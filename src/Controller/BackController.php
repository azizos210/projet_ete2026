<?php

namespace App\Controller;

use App\Entity\Consultation;
use App\Entity\Facture;
use App\Entity\Infirmier;
use App\Entity\Medecin;
use App\Entity\Patient;
use App\Entity\Prescription;
use App\Entity\RendezVous;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/back', name: 'back_')]
class BackController extends AbstractController
{
    private EntityManagerInterface $em;

    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }

    #[Route('', name: 'dashboard')]
    public function index(): Response
    {
        $stats = [
            'utilisateurs'  => $this->em->getRepository(User::class)->count([]),
            'patients'      => $this->em->getRepository(Patient::class)->count([]),
            'medecins'      => $this->em->getRepository(Medecin::class)->count([]),
            'infirmiers'    => $this->em->getRepository(Infirmier::class)->count([]),
            'rendez_vous'   => $this->em->getRepository(RendezVous::class)->count([]),
            'consultations' => $this->em->getRepository(Consultation::class)->count([]),
            'prescriptions' => $this->em->getRepository(Prescription::class)->count([]),
            'factures'      => $this->em->getRepository(Facture::class)->count([]),
        ];

        $derniersMedecins = $this->em->getRepository(Medecin::class)->findBy([], ['id' => 'DESC'], 5);
        $prochainsRdv     = $this->em->getRepository(RendezVous::class)->findBy([], ['dateHeure' => 'ASC'], 5);

        return $this->render('back/dashboard.html.twig', [
            'stats'             => $stats,
            'derniers_medecins' => $derniersMedecins,
            'prochains_rdv'     => $prochainsRdv,
        ]);
    }

    #[Route('/utilisateurs', name: 'utilisateurs')]
    public function utilisateurs(): Response
    {
        $utilisateurs = $this->em->getRepository(User::class)->findAll();
        return $this->render('back/crud/liste.html.twig', [
            'entite' => 'Utilisateurs',
            'colonnes' => ['ID', 'Email', 'Rôles', 'Prénom', 'Nom', 'Créé le'],
            'lignes' => $utilisateurs,
            'champs' => ['id', 'email', 'roles', 'firstName', 'lastName', 'createdAt'],
            'route_prefix' => 'back_utilisateurs',
        ]);
    }

    #[Route('/medecins', name: 'medecins')]
    public function medecins(): Response
    {
        $medecins = $this->em->getRepository(Medecin::class)->findAll();
        return $this->render('back/crud/liste.html.twig', [
            'entite' => 'Médecins',
            'colonnes' => ['ID', 'Prénom', 'Nom', 'Email', 'Spécialité', 'Téléphone'],
            'lignes' => $medecins,
            'champs' => ['id', 'utilisateur.prenom', 'utilisateur.nom', 'utilisateur.email', 'specialite', 'utilisateur.telephone'],
            'route_prefix' => 'back_medecins',
        ]);
    }

    #[Route('/patients', name: 'patients')]
    public function patients(): Response
    {
        $patients = $this->em->getRepository(Patient::class)->findAll();
        return $this->render('back/crud/liste.html.twig', [
            'entite' => 'Patients',
            'colonnes' => ['ID', 'Prénom', 'Nom', 'Email', 'Téléphone', 'Date naissance'],
            'lignes' => $patients,
            'champs' => ['id', 'utilisateur.prenom', 'utilisateur.nom', 'utilisateur.email', 'utilisateur.telephone', 'dateNaissance'],
            'route_prefix' => 'back_patients',
        ]);
    }

    #[Route('/infirmiers', name: 'infirmiers')]
    public function infirmiers(): Response
    {
        $infirmiers = $this->em->getRepository(Infirmier::class)->findAll();
        return $this->render('back/crud/liste.html.twig', [
            'entite' => 'Infirmiers',
            'colonnes' => ['ID', 'Prénom', 'Nom', 'Email', 'Service', 'Téléphone'],
            'lignes' => $infirmiers,
            'champs' => ['id', 'utilisateur.prenom', 'utilisateur.nom', 'utilisateur.email', 'service', 'utilisateur.telephone'],
            'route_prefix' => 'back_infirmiers',
        ]);
    }

    #[Route('/secretaires', name: 'secretaires')]
    public function secretaires(): Response
    {
        $secretaires = $this->em->getRepository(\App\Entity\SecretaireMedicale::class)->findAll();
        return $this->render('back/crud/liste.html.twig', [
            'entite' => 'Secrétaires',
            'colonnes' => ['ID', 'Prénom', 'Nom', 'Email', 'Téléphone'],
            'lignes' => $secretaires,
            'champs' => ['id', 'utilisateur.prenom', 'utilisateur.nom', 'utilisateur.email', 'utilisateur.telephone'],
            'route_prefix' => 'back_secretaires',
        ]);
    }

    #[Route('/consultations', name: 'consultations')]
    public function consultations(): Response
    {
        $consultations = $this->em->getRepository(Consultation::class)->findAll();
        return $this->render('back/crud/liste.html.twig', [
            'entite' => 'Consultations',
            'colonnes' => ['ID', 'Patient', 'Médecin', 'Date', 'Diagnostic', 'Statut'],
            'lignes' => $consultations,
            'champs' => ['id', 'dossierMedical.patient', 'medecin', 'date', 'diagnostic', 'statut'],
            'route_prefix' => 'back_consultations',
        ]);
    }

    #[Route('/rendez-vous', name: 'rendez_vous')]
    public function rendezVous(): Response
    {
        $rdvs = $this->em->getRepository(RendezVous::class)->findBy([], ['dateHeure' => 'DESC']);
        return $this->render('back/crud/liste.html.twig', [
            'entite' => 'Rendez-vous',
            'colonnes' => ['ID', 'Patient', 'Médecin', 'Date', 'Motif', 'Statut'],
            'lignes' => $rdvs,
            'champs' => ['id', 'patient', 'medecin', 'dateHeure', 'motif', 'statut'],
            'route_prefix' => 'back_rendez_vous',
        ]);
    }

    #[Route('/dossiers-medicaux', name: 'dossiers_medicaux')]
    public function dossiersMedicaux(): Response
    {
        $dossiers = $this->em->getRepository(\App\Entity\DossierMedical::class)->findAll();
        return $this->render('back/crud/liste.html.twig', [
            'entite' => 'Dossiers médicaux',
            'colonnes' => ['ID', 'Patient', 'Date création', 'Antécédents médicaux'],
            'lignes' => $dossiers,
            'champs' => ['id', 'patient', 'dateCreation', 'antecedentsMedicaux'],
            'route_prefix' => 'back_dossiers_medicaux',
        ]);
    }

    #[Route('/prescriptions', name: 'prescriptions')]
    public function prescriptions(): Response
    {
        $prescriptions = $this->em->getRepository(Prescription::class)->findAll();
        return $this->render('back/crud/liste.html.twig', [
            'entite' => 'Prescriptions',
            'colonnes' => ['ID', 'Consultation', 'Date', 'Instructions', 'Statut'],
            'lignes' => $prescriptions,
            'champs' => ['id', 'consultation', 'dateEmission', 'instructions', 'statut'],
            'route_prefix' => 'back_prescriptions',
        ]);
    }

    #[Route('/medicaments', name: 'medicaments')]
    public function medicaments(): Response
    {
        $medicaments = $this->em->getRepository(\App\Entity\Medicament::class)->findAll();
        return $this->render('back/crud/liste.html.twig', [
            'entite' => 'Médicaments',
            'colonnes' => ['ID', 'Nom', 'Dosage', 'Forme', 'Fabricant'],
            'lignes' => $medicaments,
            'champs' => ['id', 'nom', 'dosage', 'forme', 'fabricant'],
            'route_prefix' => 'back_medicaments',
        ]);
    }

    #[Route('/paiements', name: 'paiements')]
    public function paiements(): Response
    {
        $paiements = $this->em->getRepository(\App\Entity\Paiement::class)->findAll();
        return $this->render('back/crud/liste.html.twig', [
            'entite' => 'Paiements',
            'colonnes' => ['ID', 'Facture', 'Montant', 'Date', 'Méthode'],
            'lignes' => $paiements,
            'champs' => ['id', 'facture', 'montant', 'dateTransaction', 'methode'],
            'route_prefix' => 'back_paiements',
        ]);
    }

    #[Route('/factures', name: 'factures')]
    public function factures(): Response
    {
        $factures = $this->em->getRepository(Facture::class)->findAll();
        return $this->render('back/crud/liste.html.twig', [
            'entite' => 'Factures',
            'colonnes' => ['ID', 'Patient', 'Montant', 'Date', 'Statut'],
            'lignes' => $factures,
            'champs' => ['id', 'patient', 'montant', 'dateEmission', 'statutPaiement'],
            'route_prefix' => 'back_factures',
        ]);
    }

    #[Route('/assurance', name: 'assurance')]
    public function assurance(): Response
    {
        $assurances = $this->em->getRepository(\App\Entity\InformationAssurance::class)->findAll();
        return $this->render('back/crud/liste.html.twig', [
            'entite' => 'Assurances',
            'colonnes' => ['ID', 'Patient', 'Compagnie', 'Police', 'Expiration'],
            'lignes' => $assurances,
            'champs' => ['id', 'patient', 'compagnie', 'numeroPolice', 'dateExpiration'],
            'route_prefix' => 'back_assurance',
        ]);
    }

    #[Route('/messages', name: 'messages')]
    public function messages(): Response
    {
        $messages = $this->em->getRepository(\App\Entity\Message::class)->findAll();
        return $this->render('back/crud/liste.html.twig', [
            'entite' => 'Messages',
            'colonnes' => ['ID', 'Expéditeur', 'Destinataire', 'Contenu', 'Date', 'Lu'],
            'lignes' => $messages,
            'champs' => ['id', 'expediteur', 'destinataire', 'contenu', 'dateEnvoi', 'lu'],
            'route_prefix' => 'back_messages',
        ]);
    }

    #[Route('/notifications', name: 'notifications')]
    public function notifications(): Response
    {
        $notifications = $this->em->getRepository(\App\Entity\Notification::class)->findAll();
        return $this->render('back/crud/liste.html.twig', [
            'entite' => 'Notifications',
            'colonnes' => ['ID', 'Destinataire', 'Type', 'Date', 'Lu'],
            'lignes' => $notifications,
            'champs' => ['id', 'destinataire', 'type', 'dateEnvoi', 'lu'],
            'route_prefix' => 'back_notifications',
        ]);
    }

    #[Route('/audit-logs', name: 'audit_logs')]
    public function auditLogs(): Response
    {
        $logs = $this->em->getRepository(\App\Entity\AuditLog::class)->findBy([], ['dateAction' => 'DESC']);
        return $this->render('back/crud/liste.html.twig', [
            'entite' => 'Audit logs',
            'colonnes' => ['ID', 'Utilisateur', 'Action', 'Entité', 'Date', 'IP'],
            'lignes' => $logs,
            'champs' => ['id', 'utilisateur', 'action', 'entiteCible', 'dateAction', 'adresseIp'],
            'route_prefix' => 'back_audit_logs',
        ]);
    }

    #[Route('/signes-vitaux', name: 'signes_vitaux')]
    public function signesVitaux(): Response
    {
        $signes = $this->em->getRepository(\App\Entity\SignesVitaux::class)->findAll();
        return $this->render('back/crud/liste.html.twig', [
            'entite' => 'Signes vitaux',
            'colonnes' => ['ID', 'Consultation', 'Date', 'Tension', 'Pouls', 'Température'],
            'lignes' => $signes,
            'champs' => ['id', 'consultation', 'dateMesure', 'tensionArterielle', 'frequenceCardiaque', 'temperature'],
            'route_prefix' => 'back_signes_vitaux',
        ]);
    }

    #[Route('/evaluations', name: 'evaluations')]
    public function evaluations(): Response
    {
        $evaluations = $this->em->getRepository(\App\Entity\Evaluation::class)->findAll();
        return $this->render('back/crud/liste.html.twig', [
            'entite' => 'Évaluations',
            'colonnes' => ['ID', 'Patient', 'Consultation', 'Date', 'Note'],
            'lignes' => $evaluations,
            'champs' => ['id', 'patient', 'consultation', 'dateEvaluation', 'note'],
            'route_prefix' => 'back_evaluations',
        ]);
    }
}
