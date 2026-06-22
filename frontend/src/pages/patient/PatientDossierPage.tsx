import { useState, useEffect } from 'react';
import { useTranslation } from 'react-i18next';
import Box from '@mui/material/Box';
import Typography from '@mui/material/Typography';
import Card from '@mui/material/Card';
import CardContent from '@mui/material/CardContent';
import Chip from '@mui/material/Chip';
import Skeleton from '@mui/material/Skeleton';
import Grid from '@mui/material/Grid';
import Divider from '@mui/material/Divider';
import List from '@mui/material/List';
import ListItem from '@mui/material/ListItem';
import ListItemText from '@mui/material/ListItemText';
import ListItemAvatar from '@mui/material/ListItemAvatar';
import Avatar from '@mui/material/Avatar';
import Button from '@mui/material/Button';
import DownloadIcon from '@mui/icons-material/Download';
import MedicalServicesIcon from '@mui/icons-material/MedicalServices';
import DescriptionIcon from '@mui/icons-material/Description';
import { patientApi } from '../../api/services';
import type { DossierMedical } from '../../types';

export default function PatientDossierPage() {
  const { t } = useTranslation();
  const [dossier, setDossier] = useState<DossierMedical | null>(null);
  const [loading, setLoading] = useState(true);

  useEffect(() => {
    patientApi.getDossierMedical()
      .then(setDossier)
      .catch(() => {})
      .finally(() => setLoading(false));
  }, []);

  if (loading) return <Box sx={{ p: 3 }}><Skeleton variant="rectangular" height={400} sx={{ borderRadius: 3 }} /></Box>;

  if (!dossier) return <Box sx={{ p: 3 }}><Typography>{t('common.noData')}</Typography></Box>;

  return (
    <Box sx={{ p: 3 }}>
      <Typography variant="h4" sx={{ fontWeight: 700, mb: 1 }}>{t('patient.medicalRecord')}</Typography>
      <Card sx={{ mb: 3 }}>
        <CardContent>
          <Typography variant="h6" sx={{ mb: 2 }}>Informations générales</Typography>
          <Grid container spacing={2}>
            <Grid size={{ xs: 12, sm: 4 }}>
              <Typography variant="body2" color="text.secondary">Date de création</Typography>
              <Typography sx={{ fontWeight: 500 }}>{dossier.dateCreation}</Typography>
            </Grid>
            <Grid size={{ xs: 12, sm: 4 }}>
              <Typography variant="body2" color="text.secondary">Antécédents</Typography>
              <Typography sx={{ fontWeight: 500 }}>{dossier.antecedents || 'Aucun'}</Typography>
            </Grid>
            <Grid size={{ xs: 12, sm: 4 }}>
              <Typography variant="body2" color="text.secondary">Allergies</Typography>
              <Typography sx={{ fontWeight: 500 }}>{dossier.allergies || 'Aucune'}</Typography>
            </Grid>
          </Grid>
          <Box sx={{ mt: 2 }}>
            <Typography variant="body2" color="text.secondary">Traitements en cours</Typography>
            <Typography sx={{ fontWeight: 500 }}>{dossier.traitementsEnCours || 'Aucun'}</Typography>
          </Box>
        </CardContent>
      </Card>

      <Typography variant="h6" sx={{ mb: 2 }}>Consultations ({dossier.consultations.length})</Typography>
      <Card sx={{ mb: 3 }}>
        <List>
          {dossier.consultations.map((c, i) => (
            <Box key={c.id}>
              <ListItem>
                <ListItemAvatar>
                  <Avatar sx={{ bgcolor: '#006D77' }}><MedicalServicesIcon /></Avatar>
                </ListItemAvatar>
                <ListItemText
                  primary={`Dr ${c.medecin} — ${c.motif || 'Consultation'}`}
                  secondary={`${c.date} • ${c.diagnostic || 'Diagnostic en attente'}`}
                />
                <Chip label={c.statut} size="small" />
              </ListItem>
              {i < dossier.consultations.length - 1 && <Divider variant="inset" component="li" />}
            </Box>
          ))}
          {dossier.consultations.length === 0 && (
            <ListItem><ListItemText primary={t('common.noData')} /></ListItem>
          )}
        </List>
      </Card>

      <Typography variant="h6" sx={{ mb: 2 }}>Documents ({dossier.documents.length})</Typography>
      <Card>
        <List>
          {dossier.documents.map((d, i) => (
            <Box key={d.id}>
              <ListItem>
                <ListItemAvatar>
                  <Avatar sx={{ bgcolor: '#83C5BE' }}><DescriptionIcon /></Avatar>
                </ListItemAvatar>
                <ListItemText
                  primary={d.titre}
                  secondary={`${d.dateAjout} • ${d.type || 'Document'}`}
                />
                <Button size="small" variant="outlined" startIcon={<DownloadIcon />}>
                  {t('patient.downloadDocument')}
                </Button>
              </ListItem>
              {i < dossier.documents.length - 1 && <Divider variant="inset" component="li" />}
            </Box>
          ))}
          {dossier.documents.length === 0 && (
            <ListItem><ListItemText primary={t('common.noData')} /></ListItem>
          )}
        </List>
      </Card>
    </Box>
  );
}
