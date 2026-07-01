import { useEffect, useState } from 'react';
import { useNavigate } from 'react-router-dom';
import { useTranslation } from 'react-i18next';
import Grid from '@mui/material/Grid';
import Box from '@mui/material/Box';
import Typography from '@mui/material/Typography';
import Card from '@mui/material/Card';
import CardContent from '@mui/material/CardContent';
import Skeleton from '@mui/material/Skeleton';
import Chip from '@mui/material/Chip';
import List from '@mui/material/List';
import ListItem from '@mui/material/ListItem';
import ListItemText from '@mui/material/ListItemText';
import ListItemAvatar from '@mui/material/ListItemAvatar';
import Avatar from '@mui/material/Avatar';
import Divider from '@mui/material/Divider';
import { useAuth } from '../contexts/AuthContext';
import { medecinApi } from '../api/services';
import StatCard from '../components/dashboard/StatCard';
import DashboardHero from '../components/dashboard/DashboardHero';

import MedicalServicesIcon from '@mui/icons-material/MedicalServices';
import CalendarMonthIcon from '@mui/icons-material/CalendarMonth';
import PeopleIcon from '@mui/icons-material/People';

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

export default function DashboardPage() {
  const { hasRole, user } = useAuth();
  const { t } = useTranslation();
  const navigate = useNavigate();
  const [loading, setLoading] = useState(true);
  const [medecinData, setMedecinData] = useState<any>(null);

  useEffect(() => {
    if (!user) return;
    if (hasRole('ROLE_PATIENT')) { navigate('/app/patient/dashboard', { replace: true }); return; }
    if (hasRole('ROLE_ADMIN')) { window.location.href = '/back'; return; }
  }, [user, hasRole, navigate]);

  useEffect(() => {
    const fetchData = async () => {
      setLoading(true);
      try {
        if (user && hasRole('ROLE_MEDECIN')) {
          const data = await medecinApi.getDashboard();
          setMedecinData(data);
        }
      } catch {
      } finally {
        setLoading(false);
      }
      if (!user) {
        setMedecinData(DEMO_MEDECIN_DATA);
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

      {medecinData && <MedecinDashboardContent data={medecinData} />}
    </Box>
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
                        <Chip label={rdv.statut} size="small" color={rdv.statut === 'confirmé' ? 'success' : 'warning'} />
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
