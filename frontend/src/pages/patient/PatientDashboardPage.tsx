import { useTranslation } from 'react-i18next';
import { useNavigate } from 'react-router-dom';
import Box from '@mui/material/Box';
import Typography from '@mui/material/Typography';
import Grid from '@mui/material/Grid';
import Card from '@mui/material/Card';
import CardContent from '@mui/material/CardContent';
import Chip from '@mui/material/Chip';
import Button from '@mui/material/Button';
import Avatar from '@mui/material/Avatar';
import List from '@mui/material/List';
import ListItem from '@mui/material/ListItem';
import ListItemText from '@mui/material/ListItemText';
import ListItemAvatar from '@mui/material/ListItemAvatar';
import Divider from '@mui/material/Divider';

import CalendarMonthIcon from '@mui/icons-material/CalendarMonth';
import MedicalServicesIcon from '@mui/icons-material/MedicalServices';
import DescriptionIcon from '@mui/icons-material/Description';
import MedicationIcon from '@mui/icons-material/Medication';
import LocalHospitalIcon from '@mui/icons-material/LocalHospital';
import ArrowForwardIcon from '@mui/icons-material/ArrowForward';
import AddIcon from '@mui/icons-material/Add';
import PhoneIcon from '@mui/icons-material/Phone';
import FavoriteIcon from '@mui/icons-material/Favorite';
import MonitorHeartIcon from '@mui/icons-material/MonitorHeart';
import ChecklistIcon from '@mui/icons-material/Checklist';
import BoltIcon from '@mui/icons-material/Bolt';
import WaterDropIcon from '@mui/icons-material/WaterDrop';
import WarningAmberIcon from '@mui/icons-material/WarningAmber';
import CheckCircleIcon from '@mui/icons-material/CheckCircle';
import AccessTimeIcon from '@mui/icons-material/AccessTime';

const DEMO_PATIENT = {
  nom: 'Ben Ali',
  prenom: 'Amine',
  groupeSanguin: 'A+',
  allergies: 'Pénicilline',
  medecinTraitant: 'Dr. Khaled Mejri',
  numDossier: 'DOS-2024-0042',
};

const DEMO_PROCHAINS_RDV = [
  { id: 1, medecin: 'Dr. Khaled Mejri', specialite: 'Généraliste', date: '24/06/2026', heure: '14:30', motif: 'Consultation de suivi', statut: 'confirmé', lieu: 'Cabinet A, 2ème étage' },
  { id: 2, medecin: 'Dr. Hela Ben Salah', specialite: 'Cardiologue', date: '28/06/2026', heure: '10:00', motif: 'Échographie cardiaque', statut: 'en_attente', lieu: 'Service Cardiologie, Rez-de-chaussée' },
  { id: 3, medecin: 'Dr. Sami Trabelsi', specialite: 'Pédiatre', date: '05/07/2026', heure: '09:15', motif: 'Vaccin rappel', statut: 'planifié', lieu: 'Cabinet B, 1er étage' },
];

const DEMO_CONSULTATIONS = [
  { id: 1, medecin: 'Dr. Sami Trabelsi', date: '15/06/2026', motif: 'Douleur abdominale', diagnostic: 'Gastrite légère — traitement prescrit', statut: 'terminée' },
  { id: 2, medecin: 'Dr. Khaled Mejri', date: '01/06/2026', motif: 'Bilan annuel', diagnostic: 'RAS — patient en bonne santé', statut: 'terminée' },
  { id: 3, medecin: 'Dr. Hela Ben Salah', date: '20/05/2026', motif: 'Suivi cardiologie', diagnostic: 'Tension artérielle normale, poursuivre régime', statut: 'terminée' },
  { id: 4, medecin: 'Dr. Khaled Mejri', date: '10/04/2026', motif: 'Renouvellement ordonnance', diagnostic: 'Poursuite traitement antihypertenseur', statut: 'terminée' },
];

const DEMO_TRAITEMENTS = [
  { id: 1, medicament: 'Amlodipine 5mg', dosage: '1 comprimé/jour', heure: 'Matin', duree: '3 mois', prise: true },
  { id: 2, medicament: 'Oméprazole 20mg', dosage: '1 gélule/jour', heure: 'Avant petit-déjeuner', duree: '4 semaines', prise: false },
  { id: 3, medicament: 'Vitamine D 1000UI', dosage: '1 capsule/semaine', heure: 'Dimanche matin', duree: '6 mois', prise: true },
];

const HEALTH_METRICS = [
  { label: 'Tension artérielle', value: '12/8', unit: 'mmHg', icon: <MonitorHeartIcon />, color: '#006D77', status: 'normal' },
  { label: 'Fréquence cardiaque', value: '72', unit: 'bpm', icon: <FavoriteIcon />, color: '#E29578', status: 'normal' },
  { label: 'Glycémie à jeun', value: '0.92', unit: 'g/L', icon: <WaterDropIcon />, color: '#0288D1', status: 'normal' },
  { label: 'IMC', value: '24.2', unit: 'kg/m²', icon: <BoltIcon />, color: '#2E7D32', status: 'normal' },
];

const STATUT_COLORS: Record<string, string> = {
  confirmé: 'success',
  en_attente: 'warning',
  planifié: 'info',
  terminée: 'default',
};

function StatCard({ title, value, icon, color, subtitle }: { title: string; value: string | number; icon: React.ReactNode; color: string; subtitle?: string }) {
  return (
    <Card sx={{ height: '100%' }}>
      <CardContent sx={{ p: 3, '&:last-child': { pb: 3 } }}>
        <Box sx={{ display: 'flex', alignItems: 'flex-start', justifyContent: 'space-between', mb: 1 }}>
          <Typography variant="body2" color="text.secondary" sx={{ fontWeight: 500 }}>{title}</Typography>
          <Avatar sx={{ bgcolor: color, width: 40, height: 40 }}>{icon}</Avatar>
        </Box>
        <Typography variant="h4" sx={{ fontWeight: 700, color: '#1A202C', lineHeight: 1.2 }}>{value}</Typography>
        {subtitle && <Typography variant="caption" color="text.secondary">{subtitle}</Typography>}
      </CardContent>
    </Card>
  );
}

export default function PatientDashboardPage() {
  const { t } = useTranslation();
  const navigate = useNavigate();

  return (
    <Box sx={{ p: 3 }}>
      {/* Patient Hero */}
      <Card
        sx={{
          mb: 4,
          borderRadius: 4,
          background: 'linear-gradient(135deg, #006D77 0%, #83C5BE 100%)',
          color: '#fff',
        }}
      >
        <CardContent sx={{ p: { xs: 3, md: 4 } }}>
          <Grid container spacing={2} sx={{ alignItems: 'center' }}>
            <Grid size={{ xs: 12, md: 8 }}>
              <Typography variant="body2" sx={{ opacity: 0.8, mb: 0.5 }}>
                Dossier N° {DEMO_PATIENT.numDossier}
              </Typography>
              <Typography variant="h4" sx={{ fontWeight: 700, mb: 0.5 }}>
                {t('common.home')}, {DEMO_PATIENT.prenom}
              </Typography>
              <Typography variant="body1" sx={{ opacity: 0.9, mb: 2 }}>
                {t('dashboard.heroSubtitle', 'Bienvenue sur votre espace santé')}
              </Typography>
              <Box sx={{ display: 'flex', gap: 1, flexWrap: 'wrap' }}>
                <Chip icon={<FavoriteIcon />} label={`Groupe sanguin : ${DEMO_PATIENT.groupeSanguin}`} size="small" sx={{ color: '#fff', borderColor: 'rgba(255,255,255,0.5)', bgcolor: 'rgba(255,255,255,0.1)' }} variant="outlined" />
                <Chip icon={<WarningAmberIcon />} label={`Allergies : ${DEMO_PATIENT.allergies}`} size="small" sx={{ color: '#fff', borderColor: 'rgba(255,255,255,0.5)', bgcolor: 'rgba(255,255,255,0.1)' }} variant="outlined" />
                <Chip icon={<LocalHospitalIcon />} label={`Médecin traitant : ${DEMO_PATIENT.medecinTraitant}`} size="small" sx={{ color: '#fff', borderColor: 'rgba(255,255,255,0.5)', bgcolor: 'rgba(255,255,255,0.1)' }} variant="outlined" />
              </Box>
            </Grid>
            <Grid size={{ xs: 12, md: 4 }} sx={{ display: 'flex', gap: 1, justifyContent: { md: 'flex-end' } }}>
              <Button variant="contained" startIcon={<AddIcon />} onClick={() => navigate('/app/patient/rdv')} sx={{ bgcolor: 'rgba(255,255,255,0.2)', '&:hover': { bgcolor: 'rgba(255,255,255,0.3)' }, backdropFilter: 'blur(8px)' }}>
                Prendre RDV
              </Button>
              <Button variant="contained" startIcon={<PhoneIcon />} onClick={() => {}} sx={{ bgcolor: 'rgba(255,255,255,0.2)', '&:hover': { bgcolor: 'rgba(255,255,255,0.3)' }, backdropFilter: 'blur(8px)', minWidth: 48, px: 1 }}>
                <PhoneIcon />
              </Button>
            </Grid>
          </Grid>
        </CardContent>
      </Card>

      {/* Health Metrics */}
      <Typography variant="h6" sx={{ fontWeight: 600, mb: 2, display: 'flex', alignItems: 'center', gap: 1 }}>
        <MonitorHeartIcon color="primary" />
        Mes indicateurs de santé
      </Typography>
      <Grid container spacing={2} sx={{ mb: 4 }}>
        {HEALTH_METRICS.map((metric) => (
          <Grid key={metric.label} size={{ xs: 6, sm: 3 }}>
            <Card>
              <CardContent sx={{ p: 2.5, '&:last-child': { pb: 2.5 }, textAlign: 'center' }}>
                <Avatar sx={{ bgcolor: `${metric.color}15`, color: metric.color, width: 44, height: 44, mx: 'auto', mb: 1 }}>
                  {metric.icon}
                </Avatar>
                <Typography variant="h5" sx={{ fontWeight: 700 }}>{metric.value}</Typography>
                <Typography variant="caption" color="text.secondary" sx={{ display: 'block' }}>{metric.unit}</Typography>
                <Typography variant="caption" sx={{ color: metric.color, fontWeight: 500 }}>{metric.label}</Typography>
              </CardContent>
            </Card>
          </Grid>
        ))}
      </Grid>

      {/* Stats Cards */}
      <Grid container spacing={2.5} sx={{ mb: 4 }}>
        <Grid size={{ xs: 6, sm: 3 }}>
          <StatCard title="Prochains RDV" value={DEMO_PROCHAINS_RDV.length} icon={<CalendarMonthIcon />} color="#006D77" subtitle="Cette semaine" />
        </Grid>
        <Grid size={{ xs: 6, sm: 3 }}>
          <StatCard title="Consultations" value={DEMO_CONSULTATIONS.length} icon={<MedicalServicesIcon />} color="#0288D1" subtitle="Ce mois" />
        </Grid>
        <Grid size={{ xs: 6, sm: 3 }}>
          <StatCard title="Traitements" value={DEMO_TRAITEMENTS.length} icon={<MedicationIcon />} color="#2E7D32" subtitle="En cours" />
        </Grid>
        <Grid size={{ xs: 6, sm: 3 }}>
          <StatCard title="Ordonnances" value="2" icon={<DescriptionIcon />} color="#E29578" subtitle="Actives" />
        </Grid>
      </Grid>

      <Grid container spacing={3}>
        {/* Upcoming Appointments */}
        <Grid size={{ xs: 12, md: 6 }}>
          <Card>
            <CardContent>
              <Box sx={{ display: 'flex', justifyContent: 'space-between', alignItems: 'center', mb: 2 }}>
                <Typography variant="h6" sx={{ fontWeight: 600, display: 'flex', alignItems: 'center', gap: 1 }}>
                  <CalendarMonthIcon color="primary" />
                  Prochains rendez-vous
                </Typography>
                <Button size="small" endIcon={<ArrowForwardIcon />} onClick={() => navigate('/app/patient/rdv')}>
                  Voir tout
                </Button>
              </Box>
              <List disablePadding>
                {DEMO_PROCHAINS_RDV.map((rdv, i) => (
                  <Box key={rdv.id}>
                    <ListItem sx={{ px: 0, alignItems: 'flex-start' }}>
                      <ListItemAvatar sx={{ mt: 0.5 }}>
                        <Avatar sx={{ bgcolor: rdv.statut === 'confirmé' ? '#006D77' : rdv.statut === 'en_attente' ? '#E29578' : '#83C5BE', width: 44, height: 44 }}>
                          <CalendarMonthIcon />
                        </Avatar>
                      </ListItemAvatar>
                      <ListItemText
                        primary={
                          <Typography sx={{ fontWeight: 600 }}>
                            {rdv.medecin}
                            <Chip label={rdv.specialite} size="small" variant="outlined" sx={{ ml: 1, fontSize: 11 }} />
                          </Typography>
                        }
                        secondary={
                          <>
                            <Box sx={{ display: 'flex', alignItems: 'center', gap: 0.5, mt: 0.3 }}>
                              <AccessTimeIcon sx={{ fontSize: 14, color: 'text.secondary' }} />
                              <Typography variant="body2" component="span">{rdv.date} à {rdv.heure}</Typography>
                            </Box>
                            <Typography variant="body2" color="text.secondary">{rdv.motif}</Typography>
                            <Typography variant="caption" color="text.secondary">{rdv.lieu}</Typography>
                          </>
                        }
                      />
                      <Chip label={rdv.statut} size="small" color={STATUT_COLORS[rdv.statut] as any} sx={{ mt: 1, ml: 1 }} />
                    </ListItem>
                    {i < DEMO_PROCHAINS_RDV.length - 1 && <Divider component="li" />}
                  </Box>
                ))}
              </List>
            </CardContent>
          </Card>
        </Grid>

        {/* Current Treatments */}
        <Grid size={{ xs: 12, md: 6 }}>
          <Card>
            <CardContent>
              <Box sx={{ display: 'flex', justifyContent: 'space-between', alignItems: 'center', mb: 2 }}>
                <Typography variant="h6" sx={{ fontWeight: 600, display: 'flex', alignItems: 'center', gap: 1 }}>
                  <MedicationIcon color="primary" />
                  Traitements en cours
                </Typography>
                <Chip icon={<ChecklistIcon />} label="3 traitements" size="small" color="primary" variant="outlined" />
              </Box>
              <List disablePadding>
                {DEMO_TRAITEMENTS.map((traitement, i) => (
                  <Box key={traitement.id}>
                    <ListItem sx={{ px: 0 }}>
                      <ListItemAvatar sx={{ mt: 0 }}>
                        <Avatar sx={{ bgcolor: traitement.prise ? '#E8F5E9' : '#FFF3E0', width: 40, height: 40 }}>
                          {traitement.prise ? <CheckCircleIcon sx={{ color: '#2E7D32', fontSize: 22 }} /> : <AccessTimeIcon sx={{ color: '#E65100', fontSize: 22 }} />}
                        </Avatar>
                      </ListItemAvatar>
                      <ListItemText
                        primary={
                          <Typography sx={{ fontWeight: 600 }}>
                            {traitement.medicament}
                            {traitement.prise && <Chip label="Pris" size="small" color="success" sx={{ ml: 1, height: 20, fontSize: 10 }} />}
                          </Typography>
                        }
                        secondary={
                          <>
                            <Typography variant="body2">{traitement.dosage}</Typography>
                            <Typography variant="caption" color="text.secondary">{traitement.heure} · {traitement.duree}</Typography>
                          </>
                        }
                      />
                    </ListItem>
                    {i < DEMO_TRAITEMENTS.length - 1 && <Divider component="li" />}
                  </Box>
                ))}
              </List>
            </CardContent>
          </Card>

          {/* Health Tips / Smart Insights */}
          <Card sx={{ mt: 3, bgcolor: '#F0F9FF', border: '1px solid #BAE6FD' }}>
            <CardContent>
              <Typography variant="subtitle2" sx={{ fontWeight: 600, mb: 1, display: 'flex', alignItems: 'center', gap: 0.5, color: '#0369A1' }}>
                <BoltIcon sx={{ fontSize: 18 }} />
                Conseil personnalisé du jour
              </Typography>
              <Typography variant="body2" color="text.secondary">
                Basé sur votre historique, pensez à faire votre bilan sanguin trimestriel cette semaine.
                Votre dernier bilan date du 22/03/2026.
              </Typography>
              <Box sx={{ mt: 1.5, display: 'flex', gap: 1 }}>
                <Button size="small" variant="contained" color="info" sx={{ borderRadius: 2 }} onClick={() => navigate('/app/patient/rdv')}>
                  Prendre RDV
                </Button>
                <Button size="small" variant="outlined" color="info" sx={{ borderRadius: 2 }}>
                  Plus de détails
                </Button>
              </Box>
            </CardContent>
          </Card>
        </Grid>

        {/* Recent Consultations */}
        <Grid size={{ xs: 12 }}>
          <Card>
            <CardContent>
              <Box sx={{ display: 'flex', justifyContent: 'space-between', alignItems: 'center', mb: 2 }}>
                <Typography variant="h6" sx={{ fontWeight: 600, display: 'flex', alignItems: 'center', gap: 1 }}>
                  <MedicalServicesIcon color="primary" />
                  Dernières consultations
                </Typography>
                <Button size="small" endIcon={<ArrowForwardIcon />} onClick={() => navigate('/app/patient/dossier')}>
                  Voir le dossier
                </Button>
              </Box>
              <List disablePadding>
                {DEMO_CONSULTATIONS.map((c, i) => (
                  <Box key={c.id}>
                    <ListItem sx={{ px: 0 }}>
                      <ListItemAvatar sx={{ mt: 0.3 }}>
                        <Avatar sx={{ bgcolor: '#E8F5E9', width: 44, height: 44 }}>
                          <MedicalServicesIcon sx={{ color: '#2E7D32' }} />
                        </Avatar>
                      </ListItemAvatar>
                      <ListItemText
                        primary={
                          <Typography sx={{ fontWeight: 600 }}>
                            {c.medecin}
                            <Chip label={c.statut} size="small" color="default" sx={{ ml: 1, height: 20, fontSize: 10 }} />
                          </Typography>
                        }
                        secondary={
                          <>
                            <Typography variant="body2">{c.date} — {c.motif}</Typography>
                            <Typography variant="body2" color="text.secondary" sx={{ fontStyle: 'italic' }}>"{c.diagnostic}"</Typography>
                          </>
                        }
                      />
                    </ListItem>
                    {i < DEMO_CONSULTATIONS.length - 1 && <Divider component="li" />}
                  </Box>
                ))}
              </List>
            </CardContent>
          </Card>
        </Grid>
      </Grid>
    </Box>
  );
}
