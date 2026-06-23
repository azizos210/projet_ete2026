<?php

namespace App\Controller;

use App\Entity\Consultation;
use App\Entity\Facture;
use App\Entity\Infirmier;
use App\Entity\Medecin;
use App\Entity\Patient;
use App\Entity\Prescription;
use App\Entity\RendezVous;
use App\Entity\SecretaireMedicale;
use App\Entity\User;
use App\Entity\Utilisateur;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/back', name: 'back_')]
class BackController extends AbstractController
{
    public function __construct(
        private EntityManagerInterface $em,
        private UserPasswordHasherInterface $passwordHasher,
    ) {}

    // ========================================================================
    // DASHBOARD
    // ========================================================================

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

    // ========================================================================
    // UTILISATEURS (User)
    // ========================================================================

    #[Route('/utilisateurs', name: 'utilisateurs')]
    public function utilisateurs(Request $req): Response
    {
        $roleOptions = [
            ['value' => 'ROLE_ADMIN', 'label' => 'Admin'],
            ['value' => 'ROLE_MEDECIN', 'label' => 'Médecin'],
            ['value' => 'ROLE_PATIENT', 'label' => 'Patient'],
            ['value' => 'ROLE_INFIRMIER', 'label' => 'Infirmier'],
            ['value' => 'ROLE_SECRETAIRE', 'label' => 'Secrétaire'],
        ];
        $filterConfig = [['name' => 'role', 'label' => 'Rôle', 'field' => 'roles', 'type' => 'like', 'options' => $roleOptions]];
        $q = $this->buildListQuery(User::class, $req, [
            'defaultSort' => 'e.id',
            'searchFields' => ['email', 'firstName', 'lastName'],
            'filters' => $filterConfig,
        ]);
        return $this->render('back/crud/liste.html.twig', array_merge($q, [
            'entite'       => 'Utilisateurs',
            'entite_slug'  => 'utilisateur',
            'colonnes'     => ['ID', 'Email', 'Rôles', 'Prénom', 'Nom', 'Créé le'],
            'lignes'       => $q['results'],
            'champs'       => ['id', 'email', 'roles', 'firstName', 'lastName', 'createdAt'],
            'route_prefix' => 'back_utilisateurs',
            'sortable_fields' => [0 => 'e.id', 1 => 'email', 3 => 'firstName', 4 => 'lastName', 5 => 'createdAt'],
            'filters' => array_map(fn($f) => $f + ['selected' => $q['filter_values'][$f['name']] ?? ''], $filterConfig),
            'total' => count($q['results']),
        ]));
    }

    #[Route('/utilisateur/nouveau', name: 'utilisateur_new', methods: ['GET', 'POST'])]
    public function utilisateurNew(Request $req): Response
    {
        if ($req->isMethod('POST')) {
            $data = $req->request;
            if (!$this->isCsrfTokenValid('utilisateur_new', $data->get('_token'))) {
                $this->addFlash('error', 'Token CSRF invalide.');
                return $this->redirectToRoute('back_utilisateurs');
            }

            $email = trim($data->get('email', ''));
            $password = $data->get('password', '');
            $firstName = trim($data->get('firstName', ''));
            $lastName = trim($data->get('lastName', ''));
            $roles = $data->all('roles') ?? [];

            if ($email === '' || $password === '' || $firstName === '' || $lastName === '') {
                $this->addFlash('error', 'Tous les champs obligatoires doivent être remplis.');
                return $this->render('back/crud/form_utilisateur.html.twig', [
                    'user' => null,
                    'selected_roles' => $roles,
                    'form_action' => $this->generateUrl('back_utilisateur_new'),
                    'form_title' => 'Nouvel utilisateur',
                    'submit_label' => 'Créer',
                ]);
            }

            if ($this->em->getRepository(User::class)->findOneBy(['email' => $email])) {
                $this->addFlash('error', 'Cet email est déjà utilisé.');
                return $this->redirectToRoute('back_utilisateurs');
            }

            $user = new User();
            $user->setEmail($email);
            $user->setFirstName($firstName);
            $user->setLastName($lastName);
            $user->setPassword($this->passwordHasher->hashPassword($user, $password));
            $user->setRoles(array_filter($roles));

            $this->em->persist($user);
            $this->em->flush();

            $this->addFlash('success', 'Utilisateur créé avec succès.');
            return $this->redirectToRoute('back_utilisateurs');
        }

        return $this->render('back/crud/form_utilisateur.html.twig', [
            'user' => null,
            'selected_roles' => [],
            'form_action' => $this->generateUrl('back_utilisateur_new'),
            'form_title' => 'Nouvel utilisateur',
            'submit_label' => 'Créer',
        ]);
    }

    #[Route('/utilisateur/{id}', name: 'utilisateur_show', methods: ['GET'])]
    public function utilisateurShow(User $user): Response
    {
        return $this->render('back/crud/show.html.twig', [
            'entite'      => 'Utilisateur',
            'entite_slug' => 'utilisateur',
            'entity'      => $user,
            'fields'      => [
                ['label' => 'ID',             'value' => $user->getId()],
                ['label' => 'Email',          'value' => $user->getEmail()],
                ['label' => 'Prénom',         'value' => $user->getFirstName()],
                ['label' => 'Nom',            'value' => $user->getLastName()],
                ['label' => 'Rôles',          'value' => $user->getRoles(), 'type' => 'roles'],
                ['label' => 'Créé le',        'value' => $user->getCreatedAt(), 'type' => 'datetime'],
                ['label' => 'Mis à jour le',  'value' => $user->getUpdatedAt(), 'type' => 'datetime'],
            ],
            'delete_route' => 'back_utilisateur_delete',
            'edit_route'   => 'back_utilisateur_edit',
            'list_route'   => 'back_utilisateurs',
        ]);
    }

    #[Route('/utilisateur/{id}/modifier', name: 'utilisateur_edit', methods: ['GET', 'POST'])]
    public function utilisateurEdit(Request $req, User $user): Response
    {
        if ($req->isMethod('POST')) {
            $data = $req->request;
            if (!$this->isCsrfTokenValid('utilisateur_edit_' . $user->getId(), $data->get('_token'))) {
                $this->addFlash('error', 'Token CSRF invalide.');
                return $this->redirectToRoute('back_utilisateurs');
            }

            $email = trim($data->get('email', ''));
            $firstName = trim($data->get('firstName', ''));
            $lastName = trim($data->get('lastName', ''));
            $password = $data->get('password', '');
            $roles = $data->all('roles') ?? [];

            if ($email === '' || $firstName === '' || $lastName === '') {
                $this->addFlash('error', 'Les champs email, prénom et nom sont obligatoires.');
                return $this->render('back/crud/form_utilisateur.html.twig', [
                    'user' => $user,
                    'selected_roles' => $roles,
                    'form_action' => $this->generateUrl('back_utilisateur_edit', ['id' => $user->getId()]),
                    'form_title' => 'Modifier l\'utilisateur',
                    'submit_label' => 'Enregistrer',
                ]);
            }

            $existing = $this->em->getRepository(User::class)->findOneBy(['email' => $email]);
            if ($existing && $existing->getId() !== $user->getId()) {
                $this->addFlash('error', 'Cet email est déjà utilisé par un autre compte.');
                return $this->redirectToRoute('back_utilisateurs');
            }

            $user->setEmail($email);
            $user->setFirstName($firstName);
            $user->setLastName($lastName);
            if ($password !== '') {
                $user->setPassword($this->passwordHasher->hashPassword($user, $password));
            }
            $user->setRoles(array_filter($roles));

            $this->em->flush();
            $this->addFlash('success', 'Utilisateur modifié avec succès.');
            return $this->redirectToRoute('back_utilisateurs');
        }

        return $this->render('back/crud/form_utilisateur.html.twig', [
            'user' => $user,
            'selected_roles' => $user->getRoles(),
            'form_action' => $this->generateUrl('back_utilisateur_edit', ['id' => $user->getId()]),
            'form_title' => 'Modifier l\'utilisateur',
            'submit_label' => 'Enregistrer',
        ]);
    }

    #[Route('/utilisateur/{id}/supprimer', name: 'utilisateur_delete', methods: ['POST'])]
    public function utilisateurDelete(Request $req, User $user): Response
    {
        if (!$this->isCsrfTokenValid('utilisateur_delete_' . $user->getId(), $req->request->get('_token'))) {
            $this->addFlash('error', 'Token CSRF invalide.');
            return $this->redirectToRoute('back_utilisateurs');
        }

        $this->em->remove($user);
        $this->em->flush();
        $this->addFlash('success', 'Utilisateur supprimé avec succès.');
        return $this->redirectToRoute('back_utilisateurs');
    }

    // ========================================================================
    // MEDECINS
    // ========================================================================

    #[Route('/medecins', name: 'medecins')]
    public function medecins(Request $req): Response
    {
        $specialites = $this->em->createQueryBuilder()
            ->select('DISTINCT m.specialite')->from(Medecin::class, 'm')
            ->orderBy('m.specialite')->getQuery()->getScalarResult();
        $specOptions = array_map(fn($r) => ['value' => $r['specialite'], 'label' => $r['specialite']], $specialites);
        $filterConfig = [
            ['name' => 'specialite', 'label' => 'Spécialité', 'field' => 'specialite', 'options' => $specOptions],
            ['name' => 'actif', 'label' => 'Statut', 'field' => 'actif', 'options' => [
                ['value' => '1', 'label' => 'Actif'],
                ['value' => '0', 'label' => 'Inactif'],
            ]],
        ];
        $q = $this->buildListQuery(Medecin::class, $req, [
            'defaultSort' => 'e.id',
            'searchFields' => ['utilisateur.prenom', 'utilisateur.nom', 'specialite', 'numeroOrdre'],
            'filters' => $filterConfig,
        ]);
        return $this->render('back/crud/liste.html.twig', array_merge($q, [
            'entite'       => 'Médecins',
            'entite_slug'  => 'medecin',
            'colonnes'     => ['ID', 'Prénom', 'Nom', 'Email', 'Spécialité', 'Téléphone'],
            'lignes'       => $q['results'],
            'champs'       => ['id', 'utilisateur.prenom', 'utilisateur.nom', 'utilisateur.email', 'specialite', 'utilisateur.telephone'],
            'route_prefix' => 'back_medecins',
            'sortable_fields' => [0 => 'e.id', 2 => 'utilisateur.nom', 4 => 'specialite'],
            'filters' => array_map(fn($f) => $f + ['selected' => $q['filter_values'][$f['name']] ?? ''], $filterConfig),
            'total' => count($q['results']),
        ]));
    }

    #[Route('/medecin/nouveau', name: 'medecin_new', methods: ['GET', 'POST'])]
    public function medecinNew(Request $req): Response
    {
        $utilisateursDisponibles = $this->em->getRepository(Utilisateur::class)->findAll();

        if ($req->isMethod('POST')) {
            $data = $req->request;
            if (!$this->isCsrfTokenValid('medecin_new', $data->get('_token'))) {
                $this->addFlash('error', 'Token CSRF invalide.');
                return $this->redirectToRoute('back_medecins');
            }

            $utilisateurId = $data->get('utilisateur');
            $specialite = trim($data->get('specialite', ''));
            $numeroOrdre = trim($data->get('numeroOrdre', ''));
            $actif = $data->get('actif') ? true : false;

            if (!$utilisateurId || $specialite === '' || $numeroOrdre === '') {
                $this->addFlash('error', 'Tous les champs obligatoires doivent être remplis.');
                return $this->render('back/crud/form_medecin.html.twig', [
                    'medecin' => null,
                    'utilisateurs' => $utilisateursDisponibles,
                    'selected_utilisateur' => $utilisateurId,
                    'form_action' => $this->generateUrl('back_medecin_new'),
                    'form_title' => 'Nouveau médecin',
                    'submit_label' => 'Créer',
                ]);
            }

            $utilisateur = $this->em->getRepository(Utilisateur::class)->find($utilisateurId);
            if (!$utilisateur) {
                $this->addFlash('error', 'Utilisateur introuvable.');
                return $this->redirectToRoute('back_medecins');
            }

            if ($this->em->getRepository(Medecin::class)->findOneBy(['numeroOrdre' => $numeroOrdre])) {
                $this->addFlash('error', 'Ce numéro d\'ordre est déjà utilisé.');
                return $this->redirectToRoute('back_medecins');
            }

            $medecin = new Medecin();
            $medecin->setUtilisateur($utilisateur);
            $medecin->setSpecialite($specialite);
            $medecin->setNumeroOrdre($numeroOrdre);
            $medecin->setActif($actif);

            $this->em->persist($medecin);
            $this->em->flush();

            $this->addFlash('success', 'Médecin créé avec succès.');
            return $this->redirectToRoute('back_medecins');
        }

        return $this->render('back/crud/form_medecin.html.twig', [
            'medecin' => null,
            'utilisateurs' => $utilisateursDisponibles,
            'selected_utilisateur' => null,
            'form_action' => $this->generateUrl('back_medecin_new'),
            'form_title' => 'Nouveau médecin',
            'submit_label' => 'Créer',
        ]);
    }

    #[Route('/medecin/{id}', name: 'medecin_show', methods: ['GET'])]
    public function medecinShow(Medecin $medecin): Response
    {
        $u = $medecin->getUtilisateur();
        return $this->render('back/crud/show.html.twig', [
            'entite'      => 'Médecin',
            'entite_slug' => 'medecin',
            'entity'      => $medecin,
            'fields'      => [
                ['label' => 'ID',             'value' => $medecin->getId()],
                ['label' => 'Prénom',         'value' => $u?->getPrenom()],
                ['label' => 'Nom',            'value' => $u?->getNom()],
                ['label' => 'Email',          'value' => $u?->getEmail()],
                ['label' => 'Téléphone',      'value' => $u?->getTelephone() ?? '--'],
                ['label' => 'Spécialité',     'value' => $medecin->getSpecialite()],
                ['label' => 'N° Ordre',       'value' => $medecin->getNumeroOrdre()],
                ['label' => 'Signature',      'value' => $medecin->getSignatureNumerique() ?? '--'],
                ['label' => 'Actif',          'value' => $medecin->isActif(), 'type' => 'boolean'],
                ['label' => 'Rendez-vous',    'value' => $medecin->getRendezVous()->count(), 'type' => 'count'],
                ['label' => 'Consultations',  'value' => $medecin->getConsultations()->count(), 'type' => 'count'],
                ['label' => 'Prescriptions',  'value' => $medecin->getPrescriptions()->count(), 'type' => 'count'],
            ],
            'delete_route' => 'back_medecin_delete',
            'edit_route'   => 'back_medecin_edit',
            'list_route'   => 'back_medecins',
        ]);
    }

    #[Route('/medecin/{id}/modifier', name: 'medecin_edit', methods: ['GET', 'POST'])]
    public function medecinEdit(Request $req, Medecin $medecin): Response
    {
        $utilisateursDisponibles = $this->em->getRepository(Utilisateur::class)->findAll();

        if ($req->isMethod('POST')) {
            $data = $req->request;
            if (!$this->isCsrfTokenValid('medecin_edit_' . $medecin->getId(), $data->get('_token'))) {
                $this->addFlash('error', 'Token CSRF invalide.');
                return $this->redirectToRoute('back_medecins');
            }

            $utilisateurId = $data->get('utilisateur');
            $specialite = trim($data->get('specialite', ''));
            $numeroOrdre = trim($data->get('numeroOrdre', ''));
            $actif = $data->get('actif') ? true : false;

            if (!$utilisateurId || $specialite === '' || $numeroOrdre === '') {
                $this->addFlash('error', 'Tous les champs obligatoires doivent être remplis.');
                return $this->render('back/crud/form_medecin.html.twig', [
                    'medecin' => $medecin,
                    'utilisateurs' => $utilisateursDisponibles,
                    'selected_utilisateur' => $utilisateurId,
                    'form_action' => $this->generateUrl('back_medecin_edit', ['id' => $medecin->getId()]),
                    'form_title' => 'Modifier le médecin',
                    'submit_label' => 'Enregistrer',
                ]);
            }

            $existing = $this->em->getRepository(Medecin::class)->findOneBy(['numeroOrdre' => $numeroOrdre]);
            if ($existing && $existing->getId() !== $medecin->getId()) {
                $this->addFlash('error', 'Ce numéro d\'ordre est déjà utilisé.');
                return $this->redirectToRoute('back_medecins');
            }

            $utilisateur = $this->em->getRepository(Utilisateur::class)->find($utilisateurId);
            if (!$utilisateur) {
                $this->addFlash('error', 'Utilisateur introuvable.');
                return $this->redirectToRoute('back_medecins');
            }

            $medecin->setUtilisateur($utilisateur);
            $medecin->setSpecialite($specialite);
            $medecin->setNumeroOrdre($numeroOrdre);
            $medecin->setActif($actif);

            $this->em->flush();
            $this->addFlash('success', 'Médecin modifié avec succès.');
            return $this->redirectToRoute('back_medecins');
        }

        return $this->render('back/crud/form_medecin.html.twig', [
            'medecin' => $medecin,
            'utilisateurs' => $utilisateursDisponibles,
            'selected_utilisateur' => $medecin->getUtilisateur()?->getId(),
            'form_action' => $this->generateUrl('back_medecin_edit', ['id' => $medecin->getId()]),
            'form_title' => 'Modifier le médecin',
            'submit_label' => 'Enregistrer',
        ]);
    }

    #[Route('/medecin/{id}/supprimer', name: 'medecin_delete', methods: ['POST'])]
    public function medecinDelete(Request $req, Medecin $medecin): Response
    {
        if (!$this->isCsrfTokenValid('medecin_delete_' . $medecin->getId(), $req->request->get('_token'))) {
            $this->addFlash('error', 'Token CSRF invalide.');
            return $this->redirectToRoute('back_medecins');
        }

        $this->em->remove($medecin);
        $this->em->flush();
        $this->addFlash('success', 'Médecin supprimé avec succès.');
        return $this->redirectToRoute('back_medecins');
    }

    // ========================================================================
    // PATIENTS
    // ========================================================================

    #[Route('/patients', name: 'patients')]
    public function patients(Request $req): Response
    {
        $filterConfig = [
            ['name' => 'genre', 'label' => 'Genre', 'field' => 'genre', 'options' => [
                ['value' => 'homme', 'label' => 'Homme'],
                ['value' => 'femme', 'label' => 'Femme'],
                ['value' => 'autre', 'label' => 'Autre'],
            ]],
            ['name' => 'groupeSanguin', 'label' => 'Groupe sanguin', 'field' => 'groupeSanguin', 'options' => array_map(
                fn($g) => ['value' => $g, 'label' => $g],
                ['A+', 'A-', 'B+', 'B-', 'AB+', 'AB-', 'O+', 'O-']
            )],
        ];
        $q = $this->buildListQuery(Patient::class, $req, [
            'defaultSort' => 'e.id',
            'searchFields' => ['utilisateur.prenom', 'utilisateur.nom', 'utilisateur.email', 'numeroSecuriteSociale'],
            'filters' => $filterConfig,
        ]);
        return $this->render('back/crud/liste.html.twig', array_merge($q, [
            'entite'       => 'Patients',
            'entite_slug'  => 'patient',
            'colonnes'     => ['ID', 'Prénom', 'Nom', 'Email', 'Téléphone', 'Date naissance'],
            'lignes'       => $q['results'],
            'champs'       => ['id', 'utilisateur.prenom', 'utilisateur.nom', 'utilisateur.email', 'utilisateur.telephone', 'dateNaissance'],
            'route_prefix' => 'back_patients',
            'sortable_fields' => [0 => 'e.id', 1 => 'utilisateur.prenom', 5 => 'dateNaissance'],
            'filters' => array_map(fn($f) => $f + ['selected' => $q['filter_values'][$f['name']] ?? ''], $filterConfig),
            'total' => count($q['results']),
        ]));
    }

    #[Route('/patient/nouveau', name: 'patient_new', methods: ['GET', 'POST'])]
    public function patientNew(Request $req): Response
    {
        $utilisateursDisponibles = $this->em->getRepository(Utilisateur::class)->findAll();

        if ($req->isMethod('POST')) {
            $data = $req->request;
            if (!$this->isCsrfTokenValid('patient_new', $data->get('_token'))) {
                $this->addFlash('error', 'Token CSRF invalide.');
                return $this->redirectToRoute('back_patients');
            }

            $utilisateurId = $data->get('utilisateur');
            $dateNaissance = $data->get('dateNaissance', '');
            $genre = $data->get('genre', '');
            $groupeSanguin = $data->get('groupeSanguin', '');
            $allergies = $data->get('allergies', '');
            $contactUrgence = $data->get('contactUrgence', '');
            $numeroSecuriteSociale = $data->get('numeroSecuriteSociale', '');

            if (!$utilisateurId || $dateNaissance === '') {
                $this->addFlash('error', 'L\'utilisateur et la date de naissance sont obligatoires.');
                return $this->render('back/crud/form_patient.html.twig', [
                    'patient' => null,
                    'utilisateurs' => $utilisateursDisponibles,
                    'selected_utilisateur' => $utilisateurId,
                    'form_action' => $this->generateUrl('back_patient_new'),
                    'form_title' => 'Nouveau patient',
                    'submit_label' => 'Créer',
                ]);
            }

            $utilisateur = $this->em->getRepository(Utilisateur::class)->find($utilisateurId);
            if (!$utilisateur) {
                $this->addFlash('error', 'Utilisateur introuvable.');
                return $this->redirectToRoute('back_patients');
            }

            $patient = new Patient();
            $patient->setUtilisateur($utilisateur);
            $patient->setDateNaissance(new \DateTime($dateNaissance));

            if ($genre !== '') {
                $enumClass = \App\Enum\GenreEnum::class;
                $patient->setGenre($enumClass::tryFrom($genre));
            }
            if ($groupeSanguin !== '') $patient->setGroupeSanguin($groupeSanguin);
            if ($allergies !== '') $patient->setAllergies($allergies);
            if ($contactUrgence !== '') $patient->setContactUrgence($contactUrgence);
            if ($numeroSecuriteSociale !== '') {
                if ($this->em->getRepository(Patient::class)->findOneBy(['numeroSecuriteSociale' => $numeroSecuriteSociale])) {
                    $this->addFlash('error', 'Ce numéro de sécurité sociale est déjà utilisé.');
                    return $this->redirectToRoute('back_patients');
                }
                $patient->setNumeroSecuriteSociale($numeroSecuriteSociale);
            }

            $this->em->persist($patient);
            $this->em->flush();

            $this->addFlash('success', 'Patient créé avec succès.');
            return $this->redirectToRoute('back_patients');
        }

        return $this->render('back/crud/form_patient.html.twig', [
            'patient' => null,
            'utilisateurs' => $utilisateursDisponibles,
            'selected_utilisateur' => null,
            'form_action' => $this->generateUrl('back_patient_new'),
            'form_title' => 'Nouveau patient',
            'submit_label' => 'Créer',
        ]);
    }

    #[Route('/patient/{id}', name: 'patient_show', methods: ['GET'])]
    public function patientShow(Patient $patient): Response
    {
        $u = $patient->getUtilisateur();
        $dm = $patient->getDossierMedical();
        return $this->render('back/crud/show.html.twig', [
            'entite'      => 'Patient',
            'entite_slug' => 'patient',
            'entity'      => $patient,
            'fields'      => [
                ['label' => 'ID',                     'value' => $patient->getId()],
                ['label' => 'Prénom',                 'value' => $u?->getPrenom()],
                ['label' => 'Nom',                    'value' => $u?->getNom()],
                ['label' => 'Email',                  'value' => $u?->getEmail()],
                ['label' => 'Téléphone',              'value' => $u?->getTelephone() ?? '--'],
                ['label' => 'Date naissance',         'value' => $patient->getDateNaissance(), 'type' => 'date'],
                ['label' => 'Genre',                  'value' => $patient->getGenre()?->value ?? '--'],
                ['label' => 'Groupe sanguin',         'value' => $patient->getGroupeSanguin() ?? '--'],
                ['label' => 'Allergies',              'value' => $patient->getAllergies() ?? 'Aucune'],
                ['label' => 'Contact urgence',        'value' => $patient->getContactUrgence() ?? '--'],
                ['label' => 'N° Sécurité sociale',    'value' => $patient->getNumeroSecuriteSociale() ?? '--'],
                ['label' => 'Dossier médical',        'value' => $dm ? '#' . $dm->getId() : 'Non créé'],
                ['label' => 'Rendez-vous',             'value' => $patient->getRendezVous()->count(), 'type' => 'count'],
                ['label' => 'Factures',               'value' => $patient->getFactures()->count(), 'type' => 'count'],
            ],
            'delete_route' => 'back_patient_delete',
            'edit_route'   => 'back_patient_edit',
            'list_route'   => 'back_patients',
        ]);
    }

    #[Route('/patient/{id}/modifier', name: 'patient_edit', methods: ['GET', 'POST'])]
    public function patientEdit(Request $req, Patient $patient): Response
    {
        $utilisateursDisponibles = $this->em->getRepository(Utilisateur::class)->findAll();
        $genreEnum = \App\Enum\GenreEnum::class;

        if ($req->isMethod('POST')) {
            $data = $req->request;
            if (!$this->isCsrfTokenValid('patient_edit_' . $patient->getId(), $data->get('_token'))) {
                $this->addFlash('error', 'Token CSRF invalide.');
                return $this->redirectToRoute('back_patients');
            }

            $utilisateurId = $data->get('utilisateur');
            $dateNaissance = $data->get('dateNaissance', '');
            $genre = $data->get('genre', '');
            $groupeSanguin = $data->get('groupeSanguin', '');
            $allergies = $data->get('allergies', '');
            $contactUrgence = $data->get('contactUrgence', '');
            $numeroSecuriteSociale = $data->get('numeroSecuriteSociale', '');

            if (!$utilisateurId || $dateNaissance === '') {
                $this->addFlash('error', 'L\'utilisateur et la date de naissance sont obligatoires.');
                return $this->render('back/crud/form_patient.html.twig', [
                    'patient' => $patient,
                    'utilisateurs' => $utilisateursDisponibles,
                    'selected_utilisateur' => $utilisateurId,
                    'form_action' => $this->generateUrl('back_patient_edit', ['id' => $patient->getId()]),
                    'form_title' => 'Modifier le patient',
                    'submit_label' => 'Enregistrer',
                ]);
            }

            $utilisateur = $this->em->getRepository(Utilisateur::class)->find($utilisateurId);
            if (!$utilisateur) {
                $this->addFlash('error', 'Utilisateur introuvable.');
                return $this->redirectToRoute('back_patients');
            }

            if ($numeroSecuriteSociale !== '') {
                $existing = $this->em->getRepository(Patient::class)->findOneBy(['numeroSecuriteSociale' => $numeroSecuriteSociale]);
                if ($existing && $existing->getId() !== $patient->getId()) {
                    $this->addFlash('error', 'Ce numéro de sécurité sociale est déjà utilisé.');
                    return $this->redirectToRoute('back_patients');
                }
            }

            $patient->setUtilisateur($utilisateur);
            $patient->setDateNaissance(new \DateTime($dateNaissance));
            $patient->setGenre($genre !== '' ? $genreEnum::tryFrom($genre) : null);
            $patient->setGroupeSanguin($groupeSanguin !== '' ? $groupeSanguin : null);
            $patient->setAllergies($allergies !== '' ? $allergies : null);
            $patient->setContactUrgence($contactUrgence !== '' ? $contactUrgence : null);
            $patient->setNumeroSecuriteSociale($numeroSecuriteSociale !== '' ? $numeroSecuriteSociale : null);

            $this->em->flush();
            $this->addFlash('success', 'Patient modifié avec succès.');
            return $this->redirectToRoute('back_patients');
        }

        return $this->render('back/crud/form_patient.html.twig', [
            'patient' => $patient,
            'utilisateurs' => $utilisateursDisponibles,
            'selected_utilisateur' => $patient->getUtilisateur()?->getId(),
            'form_action' => $this->generateUrl('back_patient_edit', ['id' => $patient->getId()]),
            'form_title' => 'Modifier le patient',
            'submit_label' => 'Enregistrer',
        ]);
    }

    #[Route('/patient/{id}/supprimer', name: 'patient_delete', methods: ['POST'])]
    public function patientDelete(Request $req, Patient $patient): Response
    {
        if (!$this->isCsrfTokenValid('patient_delete_' . $patient->getId(), $req->request->get('_token'))) {
            $this->addFlash('error', 'Token CSRF invalide.');
            return $this->redirectToRoute('back_patients');
        }

        $this->em->remove($patient);
        $this->em->flush();
        $this->addFlash('success', 'Patient supprimé avec succès.');
        return $this->redirectToRoute('back_patients');
    }

    // ========================================================================
    // INFIRMIERS
    // ========================================================================

    #[Route('/infirmiers', name: 'infirmiers')]
    public function infirmiers(Request $req): Response
    {
        $services = $this->em->createQueryBuilder()
            ->select('DISTINCT i.service')->from(Infirmier::class, 'i')
            ->orderBy('i.service')->getQuery()->getScalarResult();
        $serviceOptions = array_map(fn($r) => ['value' => $r['service'], 'label' => $r['service']], $services);
        $filterConfig = [['name' => 'service', 'label' => 'Service', 'field' => 'service', 'options' => $serviceOptions]];
        $q = $this->buildListQuery(Infirmier::class, $req, [
            'defaultSort' => 'e.id',
            'searchFields' => ['utilisateur.prenom', 'utilisateur.nom', 'matricule', 'service'],
            'filters' => $filterConfig,
        ]);
        return $this->render('back/crud/liste.html.twig', array_merge($q, [
            'entite'       => 'Infirmiers',
            'entite_slug'  => 'infirmier',
            'colonnes'     => ['ID', 'Prénom', 'Nom', 'Email', 'Service', 'Téléphone'],
            'lignes'       => $q['results'],
            'champs'       => ['id', 'utilisateur.prenom', 'utilisateur.nom', 'utilisateur.email', 'service', 'utilisateur.telephone'],
            'route_prefix' => 'back_infirmiers',
            'sortable_fields' => [0 => 'e.id', 2 => 'utilisateur.nom', 4 => 'service'],
            'filters' => array_map(fn($f) => $f + ['selected' => $q['filter_values'][$f['name']] ?? ''], $filterConfig),
            'total' => count($q['results']),
        ]));
    }

    #[Route('/infirmier/nouveau', name: 'infirmier_new', methods: ['GET', 'POST'])]
    public function infirmierNew(Request $req): Response
    {
        $utilisateursDisponibles = $this->em->getRepository(Utilisateur::class)->findAll();

        if ($req->isMethod('POST')) {
            $data = $req->request;
            if (!$this->isCsrfTokenValid('infirmier_new', $data->get('_token'))) {
                $this->addFlash('error', 'Token CSRF invalide.');
                return $this->redirectToRoute('back_infirmiers');
            }

            $utilisateurId = $data->get('utilisateur');
            $matricule = trim($data->get('matricule', ''));
            $service = trim($data->get('service', ''));

            if (!$utilisateurId || $matricule === '' || $service === '') {
                $this->addFlash('error', 'Tous les champs sont obligatoires.');
                return $this->render('back/crud/form_infirmier.html.twig', [
                    'infirmier' => null,
                    'utilisateurs' => $utilisateursDisponibles,
                    'selected_utilisateur' => $utilisateurId,
                    'form_action' => $this->generateUrl('back_infirmier_new'),
                    'form_title' => 'Nouvel infirmier',
                    'submit_label' => 'Créer',
                ]);
            }

            $utilisateur = $this->em->getRepository(Utilisateur::class)->find($utilisateurId);
            if (!$utilisateur) {
                $this->addFlash('error', 'Utilisateur introuvable.');
                return $this->redirectToRoute('back_infirmiers');
            }

            $infirmier = new Infirmier();
            $infirmier->setUtilisateur($utilisateur);
            $infirmier->setMatricule($matricule);
            $infirmier->setService($service);

            $this->em->persist($infirmier);
            $this->em->flush();

            $this->addFlash('success', 'Infirmier créé avec succès.');
            return $this->redirectToRoute('back_infirmiers');
        }

        return $this->render('back/crud/form_infirmier.html.twig', [
            'infirmier' => null,
            'utilisateurs' => $utilisateursDisponibles,
            'selected_utilisateur' => null,
            'form_action' => $this->generateUrl('back_infirmier_new'),
            'form_title' => 'Nouvel infirmier',
            'submit_label' => 'Créer',
        ]);
    }

    #[Route('/infirmier/{id}', name: 'infirmier_show', methods: ['GET'])]
    public function infirmierShow(Infirmier $infirmier): Response
    {
        $u = $infirmier->getUtilisateur();
        return $this->render('back/crud/show.html.twig', [
            'entite'      => 'Infirmier',
            'entite_slug' => 'infirmier',
            'entity'      => $infirmier,
            'fields'      => [
                ['label' => 'ID',                  'value' => $infirmier->getId()],
                ['label' => 'Prénom',              'value' => $u?->getPrenom()],
                ['label' => 'Nom',                 'value' => $u?->getNom()],
                ['label' => 'Email',               'value' => $u?->getEmail()],
                ['label' => 'Téléphone',           'value' => $u?->getTelephone() ?? '--'],
                ['label' => 'Matricule',           'value' => $infirmier->getMatricule()],
                ['label' => 'Service',             'value' => $infirmier->getService()],
                ['label' => 'Signes vitaux',       'value' => $infirmier->getSignesVitaux()->count(), 'type' => 'count'],
                ['label' => 'Administrations',     'value' => $infirmier->getAdministrations()->count(), 'type' => 'count'],
                ['label' => 'Consultations',       'value' => $infirmier->getConsultationsAssistees()->count(), 'type' => 'count'],
            ],
            'delete_route' => 'back_infirmier_delete',
            'edit_route'   => 'back_infirmier_edit',
            'list_route'   => 'back_infirmiers',
        ]);
    }

    #[Route('/infirmier/{id}/modifier', name: 'infirmier_edit', methods: ['GET', 'POST'])]
    public function infirmierEdit(Request $req, Infirmier $infirmier): Response
    {
        $utilisateursDisponibles = $this->em->getRepository(Utilisateur::class)->findAll();

        if ($req->isMethod('POST')) {
            $data = $req->request;
            if (!$this->isCsrfTokenValid('infirmier_edit_' . $infirmier->getId(), $data->get('_token'))) {
                $this->addFlash('error', 'Token CSRF invalide.');
                return $this->redirectToRoute('back_infirmiers');
            }

            $utilisateurId = $data->get('utilisateur');
            $matricule = trim($data->get('matricule', ''));
            $service = trim($data->get('service', ''));

            if (!$utilisateurId || $matricule === '' || $service === '') {
                $this->addFlash('error', 'Tous les champs sont obligatoires.');
                return $this->render('back/crud/form_infirmier.html.twig', [
                    'infirmier' => $infirmier,
                    'utilisateurs' => $utilisateursDisponibles,
                    'selected_utilisateur' => $utilisateurId,
                    'form_action' => $this->generateUrl('back_infirmier_edit', ['id' => $infirmier->getId()]),
                    'form_title' => "Modifier l'infirmier",
                    'submit_label' => 'Enregistrer',
                ]);
            }

            $utilisateur = $this->em->getRepository(Utilisateur::class)->find($utilisateurId);
            if (!$utilisateur) {
                $this->addFlash('error', 'Utilisateur introuvable.');
                return $this->redirectToRoute('back_infirmiers');
            }

            $infirmier->setUtilisateur($utilisateur);
            $infirmier->setMatricule($matricule);
            $infirmier->setService($service);

            $this->em->flush();
            $this->addFlash('success', 'Infirmier modifié avec succès.');
            return $this->redirectToRoute('back_infirmiers');
        }

        return $this->render('back/crud/form_infirmier.html.twig', [
            'infirmier' => $infirmier,
            'utilisateurs' => $utilisateursDisponibles,
            'selected_utilisateur' => $infirmier->getUtilisateur()?->getId(),
            'form_action' => $this->generateUrl('back_infirmier_edit', ['id' => $infirmier->getId()]),
            'form_title' => "Modifier l'infirmier",
            'submit_label' => 'Enregistrer',
        ]);
    }

    #[Route('/infirmier/{id}/supprimer', name: 'infirmier_delete', methods: ['POST'])]
    public function infirmierDelete(Request $req, Infirmier $infirmier): Response
    {
        if (!$this->isCsrfTokenValid('infirmier_delete_' . $infirmier->getId(), $req->request->get('_token'))) {
            $this->addFlash('error', 'Token CSRF invalide.');
            return $this->redirectToRoute('back_infirmiers');
        }

        $this->em->remove($infirmier);
        $this->em->flush();
        $this->addFlash('success', 'Infirmier supprimé avec succès.');
        return $this->redirectToRoute('back_infirmiers');
    }

    // ========================================================================
    // SECRETAIRES
    // ========================================================================

    #[Route('/secretaires', name: 'secretaires')]
    public function secretaires(Request $req): Response
    {
        $q = $this->buildListQuery(SecretaireMedicale::class, $req, [
            'defaultSort' => 'e.id',
            'searchFields' => ['utilisateur.prenom', 'utilisateur.nom', 'utilisateur.email'],
            'filters' => [],
        ]);
        return $this->render('back/crud/liste.html.twig', array_merge($q, [
            'entite'       => 'Secrétaires',
            'entite_slug'  => 'secretaire',
            'colonnes'     => ['ID', 'Prénom', 'Nom', 'Email', 'Téléphone'],
            'lignes'       => $q['results'],
            'champs'       => ['id', 'utilisateur.prenom', 'utilisateur.nom', 'utilisateur.email', 'utilisateur.telephone'],
            'route_prefix' => 'back_secretaires',
            'sortable_fields' => [0 => 'e.id', 2 => 'utilisateur.nom'],
            'filters' => [],
            'total' => count($q['results']),
        ]));
    }

    #[Route('/secretaire/nouveau', name: 'secretaire_new', methods: ['GET', 'POST'])]
    public function secretaireNew(Request $req): Response
    {
        $utilisateursDisponibles = $this->em->getRepository(Utilisateur::class)->findAll();

        if ($req->isMethod('POST')) {
            $data = $req->request;
            if (!$this->isCsrfTokenValid('secretaire_new', $data->get('_token'))) {
                $this->addFlash('error', 'Token CSRF invalide.');
                return $this->redirectToRoute('back_secretaires');
            }

            $utilisateurId = $data->get('utilisateur');
            $posteAccueil = trim($data->get('posteAccueil', ''));

            if (!$utilisateurId) {
                $this->addFlash('error', 'L\'utilisateur est obligatoire.');
                return $this->render('back/crud/form_secretaire.html.twig', [
                    'secretaire' => null,
                    'utilisateurs' => $utilisateursDisponibles,
                    'selected_utilisateur' => $utilisateurId,
                    'form_action' => $this->generateUrl('back_secretaire_new'),
                    'form_title' => 'Nouveau secrétaire',
                    'submit_label' => 'Créer',
                ]);
            }

            $utilisateur = $this->em->getRepository(Utilisateur::class)->find($utilisateurId);
            if (!$utilisateur) {
                $this->addFlash('error', 'Utilisateur introuvable.');
                return $this->redirectToRoute('back_secretaires');
            }

            $secretaire = new SecretaireMedicale();
            $secretaire->setUtilisateur($utilisateur);
            $secretaire->setPosteAccueil($posteAccueil !== '' ? $posteAccueil : null);

            $this->em->persist($secretaire);
            $this->em->flush();

            $this->addFlash('success', 'Secrétaire créé avec succès.');
            return $this->redirectToRoute('back_secretaires');
        }

        return $this->render('back/crud/form_secretaire.html.twig', [
            'secretaire' => null,
            'utilisateurs' => $utilisateursDisponibles,
            'selected_utilisateur' => null,
            'form_action' => $this->generateUrl('back_secretaire_new'),
            'form_title' => 'Nouveau secrétaire',
            'submit_label' => 'Créer',
        ]);
    }

    #[Route('/secretaire/{id}', name: 'secretaire_show', methods: ['GET'])]
    public function secretaireShow(SecretaireMedicale $secretaire): Response
    {
        $u = $secretaire->getUtilisateur();
        return $this->render('back/crud/show.html.twig', [
            'entite'      => 'Secrétaire',
            'entite_slug' => 'secretaire',
            'entity'      => $secretaire,
            'fields'      => [
                ['label' => 'ID',                  'value' => $secretaire->getId()],
                ['label' => 'Prénom',              'value' => $u?->getPrenom()],
                ['label' => 'Nom',                 'value' => $u?->getNom()],
                ['label' => 'Email',               'value' => $u?->getEmail()],
                ['label' => 'Téléphone',           'value' => $u?->getTelephone() ?? '--'],
                ['label' => 'Poste d\'accueil',    'value' => $secretaire->getPosteAccueil() ?? '--'],
                ['label' => 'Rendez-vous créés',   'value' => $secretaire->getRendezVousCrees()->count(), 'type' => 'count'],
                ['label' => 'Factures émises',     'value' => $secretaire->getFacturesEmises()->count(), 'type' => 'count'],
                ['label' => 'Tickets traités',     'value' => $secretaire->getTicketsTraites()->count(), 'type' => 'count'],
            ],
            'delete_route' => 'back_secretaire_delete',
            'edit_route'   => 'back_secretaire_edit',
            'list_route'   => 'back_secretaires',
        ]);
    }

    #[Route('/secretaire/{id}/modifier', name: 'secretaire_edit', methods: ['GET', 'POST'])]
    public function secretaireEdit(Request $req, SecretaireMedicale $secretaire): Response
    {
        $utilisateursDisponibles = $this->em->getRepository(Utilisateur::class)->findAll();

        if ($req->isMethod('POST')) {
            $data = $req->request;
            if (!$this->isCsrfTokenValid('secretaire_edit_' . $secretaire->getId(), $data->get('_token'))) {
                $this->addFlash('error', 'Token CSRF invalide.');
                return $this->redirectToRoute('back_secretaires');
            }

            $utilisateurId = $data->get('utilisateur');
            $posteAccueil = trim($data->get('posteAccueil', ''));

            if (!$utilisateurId) {
                $this->addFlash('error', 'L\'utilisateur est obligatoire.');
                return $this->render('back/crud/form_secretaire.html.twig', [
                    'secretaire' => $secretaire,
                    'utilisateurs' => $utilisateursDisponibles,
                    'selected_utilisateur' => $utilisateurId,
                    'form_action' => $this->generateUrl('back_secretaire_edit', ['id' => $secretaire->getId()]),
                    'form_title' => 'Modifier le secrétaire',
                    'submit_label' => 'Enregistrer',
                ]);
            }

            $utilisateur = $this->em->getRepository(Utilisateur::class)->find($utilisateurId);
            if (!$utilisateur) {
                $this->addFlash('error', 'Utilisateur introuvable.');
                return $this->redirectToRoute('back_secretaires');
            }

            $secretaire->setUtilisateur($utilisateur);
            $secretaire->setPosteAccueil($posteAccueil !== '' ? $posteAccueil : null);

            $this->em->flush();
            $this->addFlash('success', 'Secrétaire modifié avec succès.');
            return $this->redirectToRoute('back_secretaires');
        }

        return $this->render('back/crud/form_secretaire.html.twig', [
            'secretaire' => $secretaire,
            'utilisateurs' => $utilisateursDisponibles,
            'selected_utilisateur' => $secretaire->getUtilisateur()?->getId(),
            'form_action' => $this->generateUrl('back_secretaire_edit', ['id' => $secretaire->getId()]),
            'form_title' => 'Modifier le secrétaire',
            'submit_label' => 'Enregistrer',
        ]);
    }

    #[Route('/secretaire/{id}/supprimer', name: 'secretaire_delete', methods: ['POST'])]
    public function secretaireDelete(Request $req, SecretaireMedicale $secretaire): Response
    {
        if (!$this->isCsrfTokenValid('secretaire_delete_' . $secretaire->getId(), $req->request->get('_token'))) {
            $this->addFlash('error', 'Token CSRF invalide.');
            return $this->redirectToRoute('back_secretaires');
        }

        $this->em->remove($secretaire);
        $this->em->flush();
        $this->addFlash('success', 'Secrétaire supprimé avec succès.');
        return $this->redirectToRoute('back_secretaires');
    }

    // ========================================================================
    // EXISTING LIST METHODS (consultations, rendez-vous, etc.)
    // ========================================================================

    #[Route('/consultations', name: 'consultations')]
    public function consultations(Request $req): Response
    {
        $filterConfig = [
            ['name' => 'statut', 'label' => 'Statut', 'field' => 'statut', 'options' => [
                ['value' => 'en_cours', 'label' => 'En cours'],
                ['value' => 'terminee', 'label' => 'Terminée'],
                ['value' => 'annulee', 'label' => 'Annulée'],
            ]],
        ];
        $q = $this->buildListQuery(Consultation::class, $req, [
            'defaultSort' => 'e.date',
            'searchFields' => ['diagnostic', 'symptomes'],
            'filters' => $filterConfig,
        ]);
        return $this->render('back/crud/liste.html.twig', array_merge($q, [
            'entite'      => 'Consultations',
            'entite_slug' => 'consultation',
            'colonnes'    => ['ID', 'Patient', 'Médecin', 'Date', 'Diagnostic', 'Statut'],
            'lignes'      => $q['results'],
            'champs'      => ['id', 'dossierMedical.patient', 'medecin', 'date', 'diagnostic', 'statut'],
            'route_prefix' => 'back_consultations',
            'sortable_fields' => [0 => 'e.id', 3 => 'date', 5 => 'statut'],
            'filters' => array_map(fn($f) => $f + ['selected' => $q['filter_values'][$f['name']] ?? ''], $filterConfig),
            'total' => count($q['results']),
        ]));
    }

    // ========================================================================
    // CONSULTATIONS
    // ========================================================================

    #[Route('/consultation/{id}', name: 'consultation_show', methods: ['GET'])]
    public function consultationShow(Consultation $consultation): Response
    {
        $dm = $consultation->getDossierMedical();
        $patient = $dm?->getPatient();
        $u = $patient?->getUtilisateur();
        $m = $consultation->getMedecin();
        $mu = $m?->getUtilisateur();
        $i = $consultation->getInfirmier();
        $iu = $i?->getUtilisateur();
        $v = $consultation->getValidateur();
        $vu = $v?->getUtilisateur();
        return $this->render('back/crud/show.html.twig', [
            'entite'      => 'Consultation',
            'entite_slug' => 'consultation',
            'entity'      => $consultation,
            'fields'      => [
                ['label' => 'ID',                     'value' => $consultation->getId()],
                ['label' => 'Patient',                'value' => $patient ? '#' . $patient->getId() . ' ' . ($u?->getNomComplet() ?? '') : '--'],
                ['label' => 'Médecin',                'value' => $m ? ($mu?->getNomComplet() ?? '#'.$m->getId()) : '--'],
                ['label' => 'Infirmier',              'value' => $i ? ($iu?->getNomComplet() ?? '#'.$i->getId()) : '--'],
                ['label' => 'Date',                   'value' => $consultation->getDate(), 'type' => 'datetime'],
                ['label' => 'Symptômes',              'value' => $consultation->getSymptomes() ?? '--'],
                ['label' => 'Examen clinique',        'value' => $consultation->getExamenClinique() ?? '--'],
                ['label' => 'Diagnostic',             'value' => $consultation->getDiagnostic() ?? '--'],
                ['label' => 'Recommandations',        'value' => $consultation->getRecommandations() ?? '--'],
                ['label' => 'Statut',                 'value' => $consultation->getStatut()],
                ['label' => 'Rendez-vous',            'value' => $consultation->getRendezVous() ? '#' . $consultation->getRendezVous()->getId() : '--'],
                ['label' => 'Validateur',             'value' => $v ? ($vu?->getNomComplet() ?? '#'.$v->getId()) : '--'],
                ['label' => 'Signes vitaux',          'value' => $consultation->getSignesVitaux()->count(), 'type' => 'count'],
                ['label' => 'Prescriptions',          'value' => $consultation->getPrescriptions()->count(), 'type' => 'count'],
                ['label' => 'Documents',              'value' => $consultation->getDocuments()->count(), 'type' => 'count'],
                ['label' => 'Facture',                'value' => $consultation->getFacture() ? '#' . $consultation->getFacture()->getId() : 'Non émise'],
            ],
            'delete_route' => 'back_consultation_delete',
            'edit_route'   => 'back_consultation_edit',
            'list_route'   => 'back_consultations',
        ]);
    }

    #[Route('/consultation/nouveau', name: 'consultation_new', methods: ['GET', 'POST'])]
    public function consultationNew(Request $req): Response
    {
        $dossiers = $this->em->getRepository(\App\Entity\DossierMedical::class)->findAll();
        $medecins = $this->em->getRepository(Medecin::class)->findAll();
        $infirmiers = $this->em->getRepository(Infirmier::class)->findAll();
        $rdvs = $this->em->getRepository(RendezVous::class)->findAll();
        $validateurs = $this->em->getRepository(\App\Entity\DirecteurMedical::class)->findAll();
        $statuts = \App\Enum\StatutConsultationEnum::cases();

        if ($req->isMethod('POST')) {
            $data = $req->request;
            if (!$this->isCsrfTokenValid('consultation_new', $data->get('_token'))) {
                $this->addFlash('error', 'Token CSRF invalide.');
                return $this->redirectToRoute('back_consultations');
            }

            $dossierId = $data->get('dossierMedical');
            $medecinId = $data->get('medecin');
            $dateStr = $data->get('date', '');

            if (!$dossierId || !$medecinId || $dateStr === '') {
                $this->addFlash('error', 'Le dossier médical, le médecin et la date sont obligatoires.');
                return $this->render('back/crud/form_consultation.html.twig', array_merge(
                    $this->getFormVars(null, $dossiers, $medecins, $infirmiers, $rdvs, $validateurs, $statuts),
                    [
                        'form_action' => $this->generateUrl('back_consultation_new'),
                        'form_title' => 'Nouvelle consultation',
                        'submit_label' => 'Créer',
                    ]
                ));
            }

            $dossier = $this->em->getRepository(\App\Entity\DossierMedical::class)->find($dossierId);
            $medecin = $this->em->getRepository(Medecin::class)->find($medecinId);
            if (!$dossier || !$medecin) {
                $this->addFlash('error', 'Dossier médical ou médecin introuvable.');
                return $this->redirectToRoute('back_consultations');
            }

            $consultation = new Consultation();
            $consultation->setDossierMedical($dossier);
            $consultation->setMedecin($medecin);
            $consultation->setDate(new \DateTime($dateStr));
            $consultation->setStatut(\App\Enum\StatutConsultationEnum::tryFrom($data->get('statut', 'en_cours')) ?? \App\Enum\StatutConsultationEnum::EN_COURS);

            $infirmierId = $data->get('infirmier');
            if ($infirmierId) {
                $inf = $this->em->getRepository(Infirmier::class)->find($infirmierId);
                if ($inf) $consultation->setInfirmier($inf);
            }

            $rdvId = $data->get('rendezVous');
            if ($rdvId) {
                $rdv = $this->em->getRepository(RendezVous::class)->find($rdvId);
                if ($rdv) $consultation->setRendezVous($rdv);
            }

            $validateurId = $data->get('validateur');
            if ($validateurId) {
                $val = $this->em->getRepository(\App\Entity\DirecteurMedical::class)->find($validateurId);
                if ($val) $consultation->setValidateur($val);
            }

            $consultation->setSymptomes(trim($data->get('symptomes', '')) ?: null);
            $consultation->setExamenClinique(trim($data->get('examenClinique', '')) ?: null);
            $consultation->setDiagnostic(trim($data->get('diagnostic', '')) ?: null);
            $consultation->setRecommandations(trim($data->get('recommandations', '')) ?: null);

            $this->em->persist($consultation);
            $this->em->flush();
            $this->addFlash('success', 'Consultation créée avec succès.');
            return $this->redirectToRoute('back_consultations');
        }

        return $this->render('back/crud/form_consultation.html.twig', array_merge(
            $this->getFormVars(null, $dossiers, $medecins, $infirmiers, $rdvs, $validateurs, $statuts),
            [
                'form_action' => $this->generateUrl('back_consultation_new'),
                'form_title' => 'Nouvelle consultation',
                'submit_label' => 'Créer',
            ]
        ));
    }

    #[Route('/consultation/{id}/modifier', name: 'consultation_edit', methods: ['GET', 'POST'])]
    public function consultationEdit(Request $req, Consultation $consultation): Response
    {
        $dossiers = $this->em->getRepository(\App\Entity\DossierMedical::class)->findAll();
        $medecins = $this->em->getRepository(Medecin::class)->findAll();
        $infirmiers = $this->em->getRepository(Infirmier::class)->findAll();
        $rdvs = $this->em->getRepository(RendezVous::class)->findAll();
        $validateurs = $this->em->getRepository(\App\Entity\DirecteurMedical::class)->findAll();
        $statuts = \App\Enum\StatutConsultationEnum::cases();

        if ($req->isMethod('POST')) {
            $data = $req->request;
            if (!$this->isCsrfTokenValid('consultation_edit_' . $consultation->getId(), $data->get('_token'))) {
                $this->addFlash('error', 'Token CSRF invalide.');
                return $this->redirectToRoute('back_consultations');
            }

            $dossierId = $data->get('dossierMedical');
            $medecinId = $data->get('medecin');
            $dateStr = $data->get('date', '');

            if (!$dossierId || !$medecinId || $dateStr === '') {
                $this->addFlash('error', 'Le dossier médical, le médecin et la date sont obligatoires.');
                return $this->render('back/crud/form_consultation.html.twig', array_merge(
                    $this->getFormVars($consultation, $dossiers, $medecins, $infirmiers, $rdvs, $validateurs, $statuts),
                    [
                        'form_action' => $this->generateUrl('back_consultation_edit', ['id' => $consultation->getId()]),
                        'form_title' => 'Modifier la consultation',
                        'submit_label' => 'Enregistrer',
                    ]
                ));
            }

            $dossier = $this->em->getRepository(\App\Entity\DossierMedical::class)->find($dossierId);
            $medecin = $this->em->getRepository(Medecin::class)->find($medecinId);
            if (!$dossier || !$medecin) {
                $this->addFlash('error', 'Dossier médical ou médecin introuvable.');
                return $this->redirectToRoute('back_consultations');
            }

            $consultation->setDossierMedical($dossier);
            $consultation->setMedecin($medecin);
            $consultation->setDate(new \DateTime($dateStr));
            $consultation->setStatut(\App\Enum\StatutConsultationEnum::tryFrom($data->get('statut', 'en_cours')) ?? \App\Enum\StatutConsultationEnum::EN_COURS);

            $infirmierId = $data->get('infirmier');
            $consultation->setInfirmier(null);
            if ($infirmierId) {
                $inf = $this->em->getRepository(Infirmier::class)->find($infirmierId);
                if ($inf) $consultation->setInfirmier($inf);
            }

            $rdvId = $data->get('rendezVous');
            $consultation->setRendezVous(null);
            if ($rdvId) {
                $rdv = $this->em->getRepository(RendezVous::class)->find($rdvId);
                if ($rdv) $consultation->setRendezVous($rdv);
            }

            $validateurId = $data->get('validateur');
            $consultation->setValidateur(null);
            if ($validateurId) {
                $val = $this->em->getRepository(\App\Entity\DirecteurMedical::class)->find($validateurId);
                if ($val) $consultation->setValidateur($val);
            }

            $consultation->setSymptomes(trim($data->get('symptomes', '')) ?: null);
            $consultation->setExamenClinique(trim($data->get('examenClinique', '')) ?: null);
            $consultation->setDiagnostic(trim($data->get('diagnostic', '')) ?: null);
            $consultation->setRecommandations(trim($data->get('recommandations', '')) ?: null);

            $this->em->flush();
            $this->addFlash('success', 'Consultation modifiée avec succès.');
            return $this->redirectToRoute('back_consultations');
        }

        return $this->render('back/crud/form_consultation.html.twig', array_merge(
            $this->getFormVars($consultation, $dossiers, $medecins, $infirmiers, $rdvs, $validateurs, $statuts),
            [
                'form_action' => $this->generateUrl('back_consultation_edit', ['id' => $consultation->getId()]),
                'form_title' => 'Modifier la consultation',
                'submit_label' => 'Enregistrer',
            ]
        ));
    }

    #[Route('/consultation/{id}/supprimer', name: 'consultation_delete', methods: ['POST'])]
    public function consultationDelete(Request $req, Consultation $consultation): Response
    {
        if (!$this->isCsrfTokenValid('consultation_delete_' . $consultation->getId(), $req->request->get('_token'))) {
            $this->addFlash('error', 'Token CSRF invalide.');
            return $this->redirectToRoute('back_consultations');
        }

        $this->em->remove($consultation);
        $this->em->flush();
        $this->addFlash('success', 'Consultation supprimée avec succès.');
        return $this->redirectToRoute('back_consultations');
    }

    private function buildListQuery(string $entityClass, Request $req, array $config): array
    {
        $qb = $this->em->createQueryBuilder()->select('e')->from($entityClass, 'e');

        $search = trim($req->query->get('q', ''));
        $sort = $req->query->get('sort', $config['defaultSort'] ?? 'e.id');
        $order = strtoupper($req->query->get('order', 'DESC'));
        if (!in_array($order, ['ASC', 'DESC'])) $order = 'DESC';

        $joined = [];

        if ($search !== '') {
            $orX = $qb->expr()->orX();
            foreach ($config['searchFields'] ?? [] as $i => $field) {
                $alias = $this->resolveAlias($qb, $field, 'e', $joined);
                $p = 'srch' . $i;
                $orX->add($qb->expr()->like($alias, ':' . $p));
                $qb->setParameter($p, '%' . $search . '%');
            }
            if ($orX->count() > 0) $qb->andWhere($orX);
        }

        $filterValues = [];
        foreach ($config['filters'] ?? [] as $f) {
            $name = $f['name'];
            $val = $req->query->get($name);
            if ($val !== null && $val !== '') {
                $filterValues[$name] = $val;
                $alias = $this->resolveAlias($qb, $f['field'], 'e', $joined);
                $ft = $f['type'] ?? 'eq';
                if ($ft === 'like') {
                    $qb->andWhere($qb->expr()->like($alias, ':f_' . $name))
                       ->setParameter('f_' . $name, '%' . $val . '%');
                } else {
                    $qb->andWhere($alias . ' = :f_' . $name)
                       ->setParameter('f_' . $name, $val);
                }
            } else {
                $filterValues[$name] = '';
            }
        }

        if ($sort) {
            $sf = preg_replace('/^e\./', '', $sort);
            if ($sf !== '') {
                $sortAlias = $this->resolveAlias($qb, $sf, 'e', $joined);
                $qb->orderBy($sortAlias, $order);
            }
        }

        return [
            'results' => $qb->getQuery()->getResult(),
            'search_query' => $search,
            'sort_field' => $sort,
            'sort_order' => $order,
            'filter_values' => $filterValues,
        ];
    }

    private function resolveAlias($qb, string $field, string $rootAlias, array &$joined): string
    {
        $parts = explode('.', $field);
        if (count($parts) === 1) return $rootAlias . '.' . $parts[0];

        $current = $rootAlias;
        for ($i = 0; $i < count($parts) - 1; $i++) {
            $relation = $parts[$i];
            $alias = '_j' . $relation;
            if (!isset($joined[$alias])) {
                $qb->leftJoin($current . '.' . $relation, $alias);
                $joined[$alias] = true;
            }
            $current = $alias;
        }
        return $current . '.' . end($parts);
    }

    private function getFormVars(?Consultation $c, array $dossiers, array $medecins, array $infirmiers, array $rdvs, array $validateurs, array $statuts): array
    {
        return [
            'consultation' => $c,
            'dossiers_medicaux' => $dossiers,
            'medecins' => $medecins,
            'infirmiers' => $infirmiers,
            'rendez_vous' => $rdvs,
            'validateurs' => $validateurs,
            'statuts' => $statuts,
            'selected_dossier' => $c?->getDossierMedical()?->getId(),
            'selected_medecin' => $c?->getMedecin()?->getId(),
            'selected_infirmier' => $c?->getInfirmier()?->getId(),
            'selected_rdv' => $c?->getRendezVous()?->getId(),
            'selected_validateur' => $c?->getValidateur()?->getId(),
            'selected_statut' => $c?->getStatut()?->value ?? 'en_cours',
        ];
    }

    #[Route('/rendez-vous', name: 'rendez_vous')]
    public function rendezVous(): Response
    {
        $rdvs = $this->em->getRepository(RendezVous::class)->findBy([], ['dateHeure' => 'DESC']);
        return $this->render('back/crud/liste.html.twig', [
            'entite'      => 'Rendez-vous',
            'entite_slug' => 'rendez_vous',
            'colonnes'    => ['ID', 'Patient', 'Médecin', 'Date', 'Motif', 'Statut'],
            'lignes'      => $rdvs,
            'champs'      => ['id', 'patient', 'medecin', 'dateHeure', 'motif', 'statut'],
            'route_prefix' => 'back_rendez_vous',
        ]);
    }

    #[Route('/dossiers-medicaux', name: 'dossiers_medicaux')]
    public function dossiersMedicaux(): Response
    {
        $dossiers = $this->em->getRepository(\App\Entity\DossierMedical::class)->findAll();
        return $this->render('back/crud/liste.html.twig', [
            'entite'      => 'Dossiers médicaux',
            'entite_slug' => 'dossier_medical',
            'colonnes'    => ['ID', 'Patient', 'Date création', 'Antécédents médicaux'],
            'lignes'      => $dossiers,
            'champs'      => ['id', 'patient', 'dateCreation', 'antecedentsMedicaux'],
            'route_prefix' => 'back_dossiers_medicaux',
        ]);
    }

    #[Route('/prescriptions', name: 'prescriptions')]
    public function prescriptions(): Response
    {
        $prescriptions = $this->em->getRepository(Prescription::class)->findAll();
        return $this->render('back/crud/liste.html.twig', [
            'entite'      => 'Prescriptions',
            'entite_slug' => 'prescription',
            'colonnes'    => ['ID', 'Consultation', 'Date', 'Instructions', 'Statut'],
            'lignes'      => $prescriptions,
            'champs'      => ['id', 'consultation', 'dateEmission', 'instructions', 'statut'],
            'route_prefix' => 'back_prescriptions',
        ]);
    }

    #[Route('/medicaments', name: 'medicaments')]
    public function medicaments(): Response
    {
        $medicaments = $this->em->getRepository(\App\Entity\Medicament::class)->findAll();
        return $this->render('back/crud/liste.html.twig', [
            'entite'      => 'Médicaments',
            'entite_slug' => 'medicament',
            'colonnes'    => ['ID', 'Nom', 'Dosage', 'Forme', 'Fabricant'],
            'lignes'      => $medicaments,
            'champs'      => ['id', 'nom', 'dosage', 'forme', 'fabricant'],
            'route_prefix' => 'back_medicaments',
        ]);
    }

    #[Route('/paiements', name: 'paiements')]
    public function paiements(): Response
    {
        $paiements = $this->em->getRepository(\App\Entity\Paiement::class)->findAll();
        return $this->render('back/crud/liste.html.twig', [
            'entite'      => 'Paiements',
            'entite_slug' => 'paiement',
            'colonnes'    => ['ID', 'Facture', 'Montant', 'Date', 'Méthode'],
            'lignes'      => $paiements,
            'champs'      => ['id', 'facture', 'montant', 'dateTransaction', 'methode'],
            'route_prefix' => 'back_paiements',
        ]);
    }

    #[Route('/factures', name: 'factures')]
    public function factures(): Response
    {
        $factures = $this->em->getRepository(Facture::class)->findAll();
        return $this->render('back/crud/liste.html.twig', [
            'entite'      => 'Factures',
            'entite_slug' => 'facture',
            'colonnes'    => ['ID', 'Patient', 'Montant', 'Date', 'Statut'],
            'lignes'      => $factures,
            'champs'      => ['id', 'patient', 'montant', 'dateEmission', 'statutPaiement'],
            'route_prefix' => 'back_factures',
        ]);
    }

    #[Route('/assurance', name: 'assurance')]
    public function assurance(): Response
    {
        $assurances = $this->em->getRepository(\App\Entity\InformationAssurance::class)->findAll();
        return $this->render('back/crud/liste.html.twig', [
            'entite'      => 'Assurances',
            'entite_slug' => 'assurance',
            'colonnes'    => ['ID', 'Patient', 'Compagnie', 'Police', 'Expiration'],
            'lignes'      => $assurances,
            'champs'      => ['id', 'patient', 'compagnie', 'numeroPolice', 'dateExpiration'],
            'route_prefix' => 'back_assurance',
        ]);
    }

    #[Route('/messages', name: 'messages')]
    public function messages(): Response
    {
        $messages = $this->em->getRepository(\App\Entity\Message::class)->findAll();
        return $this->render('back/crud/liste.html.twig', [
            'entite'      => 'Messages',
            'entite_slug' => 'message',
            'colonnes'    => ['ID', 'Expéditeur', 'Destinataire', 'Contenu', 'Date', 'Lu'],
            'lignes'      => $messages,
            'champs'      => ['id', 'expediteur', 'destinataire', 'contenu', 'dateEnvoi', 'lu'],
            'route_prefix' => 'back_messages',
        ]);
    }

    #[Route('/notifications', name: 'notifications')]
    public function notifications(): Response
    {
        $notifications = $this->em->getRepository(\App\Entity\Notification::class)->findAll();
        return $this->render('back/crud/liste.html.twig', [
            'entite'      => 'Notifications',
            'entite_slug' => 'notification',
            'colonnes'    => ['ID', 'Destinataire', 'Type', 'Date', 'Lu'],
            'lignes'      => $notifications,
            'champs'      => ['id', 'destinataire', 'type', 'dateEnvoi', 'lu'],
            'route_prefix' => 'back_notifications',
        ]);
    }

    #[Route('/audit-logs', name: 'audit_logs')]
    public function auditLogs(): Response
    {
        $logs = $this->em->getRepository(\App\Entity\AuditLog::class)->findBy([], ['dateAction' => 'DESC']);
        return $this->render('back/crud/liste.html.twig', [
            'entite'      => 'Audit logs',
            'entite_slug' => 'audit_log',
            'colonnes'    => ['ID', 'Utilisateur', 'Action', 'Entité', 'Date', 'IP'],
            'lignes'      => $logs,
            'champs'      => ['id', 'utilisateur', 'action', 'entiteCible', 'dateAction', 'adresseIp'],
            'route_prefix' => 'back_audit_logs',
        ]);
    }

    #[Route('/signes-vitaux', name: 'signes_vitaux')]
    public function signesVitaux(): Response
    {
        $signes = $this->em->getRepository(\App\Entity\SignesVitaux::class)->findAll();
        return $this->render('back/crud/liste.html.twig', [
            'entite'      => 'Signes vitaux',
            'entite_slug' => 'signes_vitaux',
            'colonnes'    => ['ID', 'Consultation', 'Date', 'Tension', 'Pouls', 'Température'],
            'lignes'      => $signes,
            'champs'      => ['id', 'consultation', 'dateMesure', 'tensionArterielle', 'frequenceCardiaque', 'temperature'],
            'route_prefix' => 'back_signes_vitaux',
        ]);
    }

    #[Route('/evaluations', name: 'evaluations')]
    public function evaluations(): Response
    {
        $evaluations = $this->em->getRepository(\App\Entity\Evaluation::class)->findAll();
        return $this->render('back/crud/liste.html.twig', [
            'entite'      => 'Évaluations',
            'entite_slug' => 'evaluation',
            'colonnes'    => ['ID', 'Patient', 'Consultation', 'Date', 'Note'],
            'lignes'      => $evaluations,
            'champs'      => ['id', 'patient', 'consultation', 'dateEvaluation', 'note'],
            'route_prefix' => 'back_evaluations',
        ]);
    }
}
