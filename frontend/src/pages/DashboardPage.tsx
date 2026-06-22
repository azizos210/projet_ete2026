import { useEffect, useState } from 'react';
import { useTranslation } from 'react-i18next';
import Grid from '@mui/material/Grid';
import Box from '@mui/material/Box';
import Typography from '@mui/material/Typography';
import Card from '@mui/material/Card';
import CardContent from '@mui/material/CardContent';
import Skeleton from '@mui/material/Skeleton';
import Alert from '@mui/material/Alert';
import Chip from '@mui/material/Chip';
import List from '@mui/material/List';
import ListItem from '@mui/material/ListItem';
import ListItemText from '@mui/material/ListItemText';
import ListItemAvatar from '@mui/material/ListItemAvatar';
import Avatar from '@mui/material/Avatar';
import Divider from '@mui/material/Divider';
import AlertTitle from '@mui/material/AlertTitle';
import { useAuth } from '../contexts/AuthContext';
import { patientApi, medecinApi, adminApi } from '../api/services';
import StatCard from '../components/dashboard/StatCard';
import DashboardHero from '../components/dashboard/DashboardHero';

import CalendarMonthIcon from '@mui/icons-material/CalendarMonth';
import MedicalServicesIcon from '@mui/icons-material/MedicalServices';
import DescriptionIcon from '@mui/icons-material/Description';
import PeopleIcon from '@mui/icons-material/People';
import LocalHospitalIcon from '@mui/icons-material/LocalHospital';
import ReceiptIcon from '@mui/icons-material/Receipt';
import TrendingUpIcon from '@mui/icons-material/TrendingUp';
import GroupsIcon from '@mui/icons-material/Groups';

const DEMO_ADMIN_DATA = {
  stats: {
    totalPatients: 1250,
    totalMedecins: 48,
    totalInfirmiers: 96,
    totalConsultations: 8320,
    rdvsAujourdhui: 64,
    facturesEnAttente: 28,
    chiffreAffaireMois: 185000,
    consultationsAujourdhui: 42,
  },
  prochainsRdvs: [
    { id: 1, patient: 'Amine Ben Ali', medecin: 'Khaled Mejri', dateHeure: '22/06/2026 14:30', motif: 'Consultation générale', statut: 'confirmé' },
    { id: 2, patient: 'Sarra Bahri', medecin: 'Hela Ben Salah', dateHeure: '22/06/2026 15:00', motif: 'Suivi cardiologie', statut: 'en_attente' },
    { id: 3, patient: 'Mohamed Kacem', medecin: 'Khaled Mejri', dateHeure: '23/06/2026 09:00', motif: 'Radio contrôle', statut: 'confirmé' },
    { id: 4, patient: 'Nadia Bouzid', medecin: 'Sami Trabelsi', dateHeure: '23/06/2026 10:30', motif: 'Consultation pédiatrique', statut: 'confirmé' },
    { id: 5, patient: 'Ali Mnif', medecin: 'Hela Ben Salah', dateHeure: '23/06/2026 14:00', motif: 'Bilan sanguin', statut: 'en_attente' },
  ],
};

const DEMO_MEDECIN_DATA = {
  consultationsAujourdhui: 8,
  prochainsRdvs: [
    { id: 1, patient: 'Amine Ben Ali', dateHeure: '22/06/2026 14:30', motif: 'Consultation générale', statut: 'confirmé' },
    { id: 2, patient: 'Sarra Bahri', dateHeure: '22/06/2026 15:00', motif: 'Suivi cardiologie', statut: 'en_attente' },
    { id: 3, patient: 'Mohamed Kacem', dateHeure: '23/06/2026 09:00', motif: 'Radio contrôle', statut: 'confirmé' },
    { id: 4, patient: 'Nadia Bouzid', dateHeure: '23/06/2026 10:30', motif: 'Consultation pédiatrique', statut: 'confirmé' },
    { id: 5, patient: 'Ali Mnif', dateHeure: '23/06/2026 14:00', motif: 'Bilan sanguin', statut: 'en_attente' },
  ],
  patientsRecents: [
    { id: 1, prenom: 'Amine', nom: 'Ben Ali', email: 'amine.benali@email.com', telephone: '+216 98 765 432' },
    { id: 2, prenom: 'Sarra', nom: 'Bahri', email: 'sarra.bahri@email.com', telephone: '+216 55 123 456' },
    { id: 3, prenom: 'Mohamed', nom: 'Kacem', email: 'mohamed.kacem@email.com', telephone: '+216 21 987 654' },
    { id: 4, prenom: 'Nadia', nom: 'Bouzid', email: 'nadia.bouzid@email.com', telephone: '+216 29 456 789' },
    { id: 5, prenom: 'Ali', nom: 'Mnif', email: 'ali.mnif@email.com', telephone: '+216 50 321 654' },
  ],
};

const DEMO_PATIENT_DATA = {
  prochainsRdvs: [
    { id: 1, medecin: 'Khaled Mejri', dateHeure: '22/06/2026 14:30', motif: 'Consultation générale', statut: 'confirmé' },
    { id: 2, medecin: 'Hela Ben Salah', dateHeure: '28/06/2026 10:00', motif: 'Suivi', statut: 'en_attente' },
  ],
  dernieresConsultations: [
    { id: 1, medecin: 'Sami Trabelsi', date: '15/06/2026', motif: 'Douleur abdominale', diagnostic: 'Gastrite légère', statut: 'terminée' },
    { id: 2, medecin: 'Khaled Mejri', date: '01/06/2026', motif: 'Bilan annuel', diagnostic: 'RAS', statut: 'terminée' },
    { id: 3, medecin: 'Hela Ben Salah', date: '20/05/2026', motif: 'Suivi cardiologie', diagnostic: 'Tension normale', statut: 'terminée' },
  ],
  documentsRecents: [
    { id: 1, nom: 'Bilan sanguin 2026', date: '15/06/2026', type: 'PDF' },
    { id: 2, nom: 'Radio thorax', date: '01/06/2026', type: 'Image' },
  ],
};

export default function DashboardPage() {
  const { hasRole, user } = useAuth();
  const { t } = useTranslation();
  const [loading, setLoading] = useState(true);
  const [patientData, setPatientData] = useState<any>(null);
  const [medecinData, setMedecinData] = useState<any>(null);
  const [adminData, setAdminData] = useState<any>(null);

  useEffect(() => {
    const fetchData = async () => {
      setLoading(true);
      try {
        if (hasRole('ROLE_ADMIN')) {
          const data = await adminApi.getDashboard();
          setAdminData(data);
        } else if (hasRole('ROLE_MEDECIN')) {
          const data = await medecinApi.getDashboard();
          setMedecinData(data);
        } else if (hasRole('ROLE_PATIENT')) {
          const data = await patientApi.getDashboard();
          setPatientData(data);
        }
      } catch {
        // fallback to demo data below
      } finally {
        setLoading(false);
      }
      // Use demo data when no API data was set (guest or API error)
      if (!user) {
        setAdminData(DEMO_ADMIN_DATA);
        setMedecinData(DEMO_MEDECIN_DATA);
        setPatientData(DEMO_PATIENT_DATA);
      }
    };
    fetchData();
  }, [hasRole, t, user]);

  if (loading) {
    return (
      <Box sx={{ p: 3 }}>
        <Skeleton variant="rectangular" height={200} sx={{ borderRadius: 4, mb: 2 }} />
        <Grid container spacing={3}>
          {[1, 2, 3, 4].map((i) => (
            <Grid key={i} size={{ xs: 12, sm: 6, md: 3 }}>
              <Skeleton variant="rectangular" height={140} sx={{ borderRadius: 4 }} />
            </Grid>
          ))}
        </Grid>
      </Box>
    );
  }

  return (
    <Box sx={{ p: 3 }}>
      <DashboardHero />

      {(hasRole('ROLE_ADMIN') || !user) && adminData && <AdminDashboardContent data={adminData} />}
      {(hasRole('ROLE_MEDECIN') || !user) && medecinData && <MedecinDashboardContent data={medecinData} />}
      {(hasRole('ROLE_PATIENT') || !user) && patientData && <PatientDashboardContent data={patientData} />}
      {(hasRole('ROLE_INFIRMIER') || !user) && <InfirmierDashboardContent />}
      {(hasRole('ROLE_SECRETAIRE') || !user) && <SecretaireDashboardContent />}
    </Box>
  );
}

function AdminDashboardContent({ data }: { data: any }) {
  const { t } = useTranslation();
  const stats = data.stats;

  return (
    <>
      <Grid container spacing={3} sx={{ mb: 3 }}>
        <Grid size={{ xs: 12, sm: 6, md: 3 }}>
          <StatCard title={t('admin.totalPatients')} value={stats.totalPatients} icon={<PeopleIcon />} color="#006D77" />
        </Grid>
        <Grid size={{ xs: 12, sm: 6, md: 3 }}>
          <StatCard title={t('admin.totalDoctors')} value={stats.totalMedecins} icon={<LocalHospitalIcon />} color="#0288D1" />
        </Grid>
        <Grid size={{ xs: 12, sm: 6, md: 3 }}>
          <StatCard title={t('admin.totalNurses')} value={stats.totalInfirmiers} icon={<GroupsIcon />} color="#2E7D32" />
        </Grid>
        <Grid size={{ xs: 12, sm: 6, md: 3 }}>
          <StatCard title={t('admin.totalConsultations')} value={stats.totalConsultations} icon={<MedicalServicesIcon />} color="#E29578" />
        </Grid>
        <Grid size={{ xs: 12, sm: 6, md: 3 }}>
          <StatCard title={t('admin.todayAppointments')} value={stats.rdvsAujourdhui} icon={<CalendarMonthIcon />} color="#F4A261" />
        </Grid>
        <Grid size={{ xs: 12, sm: 6, md: 3 }}>
          <StatCard title={t('admin.pendingInvoices')} value={stats.facturesEnAttente} icon={<ReceiptIcon />} color="#D32F2F" />
        </Grid>
        <Grid size={{ xs: 12, sm: 6, md: 3 }}>
          <StatCard
            title={t('admin.monthlyRevenue')}
            value={`${stats.chiffreAffaireMois?.toLocaleString()} DT`}
            icon={<TrendingUpIcon />}
            color="#2E7D32"
          />
        </Grid>
        <Grid size={{ xs: 12, sm: 6, md: 3 }}>
          <StatCard title={t('admin.todayAppointments')} value={stats.consultationsAujourdhui} icon={<MedicalServicesIcon />} color="#006D77" />
        </Grid>
      </Grid>

      <Card sx={{ mb: 3 }}>
        <CardContent>
          <Typography variant="h6" sx={{ mb: 2 }}>{t('medecin.upcomingAppointments')}</Typography>
          {data.prochainsRdvs?.length > 0 ? (
            <List>
              {data.prochainsRdvs.map((rdv: any) => (
                <Box key={rdv.id}>
                  <ListItem>
                    <ListItemAvatar>
                      <Avatar sx={{ bgcolor: '#006D77' }}>
                        <CalendarMonthIcon />
                      </Avatar>
                    </ListItemAvatar>
                    <ListItemText
                      primary={`${rdv.patient} - Dr ${rdv.medecin}`}
                      secondary={`${rdv.dateHeure} • ${rdv.motif || 'Consultation'}`}
                    />
                    <Chip label={rdv.statut} color="primary" size="small" />
                  </ListItem>
                  <Divider variant="inset" component="li" />
                </Box>
              ))}
            </List>
          ) : (
            <Typography color="text.secondary">{t('common.noData')}</Typography>
          )}
        </CardContent>
      </Card>
    </>
  );
}

function MedecinDashboardContent({ data }: { data: any }) {
  const { t } = useTranslation();
  return (
    <>
      <Grid container spacing={3} sx={{ mb: 3 }}>
        <Grid size={{ xs: 12, sm: 6, md: 4 }}>
          <StatCard title={t('medecin.todayConsultations')} value={data.consultationsAujourdhui} icon={<MedicalServicesIcon />} color="#006D77" />
        </Grid>
        <Grid size={{ xs: 12, sm: 6, md: 4 }}>
          <StatCard title={t('medecin.upcomingAppointments')} value={data.prochainsRdvs?.length || 0} icon={<CalendarMonthIcon />} color="#0288D1" />
        </Grid>
        <Grid size={{ xs: 12, sm: 6, md: 4 }}>
          <StatCard title={t('medecin.recentPatients')} value={data.patientsRecents?.length || 0} icon={<PeopleIcon />} color="#2E7D32" />
        </Grid>
      </Grid>

      <Grid container spacing={3}>
        <Grid size={{ xs: 12, md: 6 }}>
          <Card>
            <CardContent>
              <Typography variant="h6" sx={{ mb: 2 }}>{t('medecin.upcomingAppointments')}</Typography>
              {data.prochainsRdvs?.length > 0 ? (
                <List>
                  {data.prochainsRdvs.slice(0, 5).map((rdv: any) => (
                    <Box key={rdv.id}>
                      <ListItem>
                        <ListItemAvatar>
                          <Avatar sx={{ bgcolor: '#E29578' }}>
                            <CalendarMonthIcon />
                          </Avatar>
                        </ListItemAvatar>
                        <ListItemText
                          primary={rdv.patient}
                          secondary={`${rdv.dateHeure} • ${rdv.motif || 'Consultation'}`}
                        />
                        <Chip label={rdv.statut} size="small" color={rdv.statut === 'confirme' ? 'success' : 'warning'} />
                      </ListItem>
                      <Divider variant="inset" component="li" />
                    </Box>
                  ))}
                </List>
              ) : (
                <Typography color="text.secondary">{t('common.noData')}</Typography>
              )}
            </CardContent>
          </Card>
        </Grid>
        <Grid size={{ xs: 12, md: 6 }}>
          <Card>
            <CardContent>
              <Typography variant="h6" sx={{ mb: 2 }}>{t('medecin.recentPatients')}</Typography>
              {data.patientsRecents?.length > 0 ? (
                <List>
                  {data.patientsRecents.slice(0, 5).map((p: any) => (
                    <Box key={p.id}>
                      <ListItem>
                        <ListItemAvatar>
                          <Avatar sx={{ bgcolor: '#83C5BE' }}>{p.prenom?.[0]}{p.nom?.[0]}</Avatar>
                        </ListItemAvatar>
                        <ListItemText
                          primary={`${p.prenom} ${p.nom}`}
                          secondary={`${p.email} • ${p.telephone || ''}`}
                        />
                      </ListItem>
                      <Divider variant="inset" component="li" />
                    </Box>
                  ))}
                </List>
              ) : (
                <Typography color="text.secondary">{t('common.noData')}</Typography>
              )}
            </CardContent>
          </Card>
        </Grid>
      </Grid>
    </>
  );
}

function PatientDashboardContent({ data }: { data: any }) {
  const { t } = useTranslation();
  return (
    <>
      <Grid container spacing={3} sx={{ mb: 3 }}>
        <Grid size={{ xs: 12, sm: 6, md: 4 }}>
          <StatCard title={t('patient.upcomingAppointments')} value={data.prochainsRdvs?.length || 0} icon={<CalendarMonthIcon />} color="#006D77" />
        </Grid>
        <Grid size={{ xs: 12, sm: 6, md: 4 }}>
          <StatCard title={t('patient.recentConsultations')} value={data.dernieresConsultations?.length || 0} icon={<MedicalServicesIcon />} color="#0288D1" />
        </Grid>
        <Grid size={{ xs: 12, sm: 6, md: 4 }}>
          <StatCard title={t('patient.recentDocuments')} value={data.documentsRecents?.length || 0} icon={<DescriptionIcon />} color="#2E7D32" />
        </Grid>
      </Grid>

      <Grid container spacing={3}>
        <Grid size={{ xs: 12, md: 6 }}>
          <Card>
            <CardContent>
              <Typography variant="h6" sx={{ mb: 2 }}>{t('patient.upcomingAppointments')}</Typography>
              {data.prochainsRdvs?.length > 0 ? (
                <List>
                  {data.prochainsRdvs.slice(0, 5).map((rdv: any) => (
                    <Box key={rdv.id}>
                      <ListItem>
                        <ListItemAvatar>
                          <Avatar sx={{ bgcolor: '#E29578' }}>
                            <CalendarMonthIcon />
                          </Avatar>
                        </ListItemAvatar>
                        <ListItemText
                          primary={`Dr ${rdv.medecin}`}
                          secondary={`${rdv.dateHeure} • ${rdv.motif || 'Consultation'}`}
                        />
                        <Chip label={rdv.statut} size="small" color={rdv.statut === 'confirme' ? 'success' : 'warning'} />
                      </ListItem>
                      <Divider variant="inset" component="li" />
                    </Box>
                  ))}
                </List>
              ) : (
                <Typography color="text.secondary">{t('common.noData')}</Typography>
              )}
            </CardContent>
          </Card>
        </Grid>
        <Grid size={{ xs: 12, md: 6 }}>
          <Card>
            <CardContent>
              <Typography variant="h6" sx={{ mb: 2 }}>{t('patient.recentConsultations')}</Typography>
              {data.dernieresConsultations?.length > 0 ? (
                <List>
                  {data.dernieresConsultations.map((c: any) => (
                    <Box key={c.id}>
                      <ListItem>
                        <ListItemAvatar>
                          <Avatar sx={{ bgcolor: '#83C5BE' }}>
                            <MedicalServicesIcon />
                          </Avatar>
                        </ListItemAvatar>
                        <ListItemText
                          primary={`Dr ${c.medecin}`}
                          secondary={`${c.date} • ${c.motif || 'Consultation'}${c.diagnostic ? ' — ' + c.diagnostic : ''}`}
                        />
                        <Chip label={c.statut} size="small" />
                      </ListItem>
                      <Divider variant="inset" component="li" />
                    </Box>
                  ))}
                </List>
              ) : (
                <Typography color="text.secondary">{t('common.noData')}</Typography>
              )}
            </CardContent>
          </Card>
        </Grid>
      </Grid>
    </>
  );
}

function InfirmierDashboardContent() {
  const { t } = useTranslation();
  return (
    <Alert severity="info" sx={{ borderRadius: 3 }}>
      <AlertTitle>Espace Infirmier</AlertTitle>
      {t('common.loading')}...
    </Alert>
  );
}

function SecretaireDashboardContent() {
  const { t } = useTranslation();
  return (
    <Alert severity="info" sx={{ borderRadius: 3 }}>
      <AlertTitle>Espace Secrétaire</AlertTitle>
      {t('common.loading')}...
    </Alert>
  );
}
