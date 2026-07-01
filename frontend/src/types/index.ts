export interface User {
  id: number;
  email: string;
  firstName: string;
  lastName: string;
  roles: string[];
}

export interface PatientProfile {
  id: number;
  nom: string;
  prenom: string;
  email: string;
  telephone: string | null;
  dateNaissance: string | null;
  genre: string | null;
  groupeSanguin: string | null;
  allergies: string | null;
  numeroSecuriteSociale: string | null;
}

export interface MedecinProfile {
  id: number;
  nomComplet: string;
  specialite: string;
  email: string;
  telephone: string | null;
  numeroOrdre: string;
}

export interface RendezVous {
  id: number;
  dateHeure: string;
  patient?: string;
  medecin?: string;
  motif: string | null;
  statut: string;
  notes?: string | null;
}

export interface Consultation {
  id: number;
  dateConsultation?: string;
  date?: string;
  patient?: string;
  medecin?: string;
  motif: string | null;
  diagnostic: string | null;
  statut: string;
}

export interface DocumentMedical {
  id: number;
  titre: string;
  type: string | null;
  description?: string | null;
  dateAjout: string;
}

export interface DossierMedical {
  id: number;
  dateCreation: string;
  antecedents: string | null;
  allergies: string | null;
  traitementsEnCours: string | null;
  consultations: Consultation[];
  documents: DocumentMedical[];
}

export interface Prescription {
  id: number;
  datePrescription: string;
  patient: string | null;
  statut: string;
  lignes: LignePrescription[];
}

export interface LignePrescription {
  id: number;
  medicament: string | null;
  dosage: string | null;
  frequence: string | null;
  duree: string | null;
}

export interface AdminStats {
  totalPatients: number;
  totalMedecins: number;
  totalInfirmiers: number;
  totalConsultations: number;
  consultationsAujourdhui: number;
  rdvsAujourdhui: number;
  facturesEnAttente: number;
  chiffreAffaireMois: number;
}

export interface Utilisateur {
  id: number;
  email: string;
  nom: string;
  prenom: string;
  telephone: string | null;
  roles: string[];
  actif: boolean;
  dateCreation: string;
  derniereConnexion: string | null;
  profil: string;
}

export interface PatientDashboardData {
  patient: PatientProfile;
  prochainsRdvs: RendezVous[];
  dernieresConsultations: Consultation[];
  documentsRecents: DocumentMedical[];
}

export interface MedecinDashboardData {
  medecin: MedecinProfile;
  consultationsAujourdhui: number;
  prochainsRdvs: RendezVous[];
  patientsRecents: PatientProfile[];
}

export interface AdminDashboardData {
  stats: AdminStats;
  prochainsRdvs: RendezVous[];
}

export interface AuditLogEntry {
  id: number;
  utilisateur: string;
  action: string;
  entite: string;
  entiteId: number | null;
  details: string | null;
  dateAction: string;
  adresseIp: string | null;
}
