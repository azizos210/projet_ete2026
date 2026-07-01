import { useState, useEffect } from 'react';
import { useTranslation } from 'react-i18next';
import Box from '@mui/material/Box';
import Typography from '@mui/material/Typography';
import Card from '@mui/material/Card';
import Chip from '@mui/material/Chip';
import Skeleton from '@mui/material/Skeleton';
import Table from '@mui/material/Table';
import TableBody from '@mui/material/TableBody';
import TableCell from '@mui/material/TableCell';
import TableContainer from '@mui/material/TableContainer';
import TableHead from '@mui/material/TableHead';
import TableRow from '@mui/material/TableRow';
import { medecinApi } from '../../api/services';
import type { RendezVous } from '../../types';

export default function MedecinRdvPage() {
  const { t } = useTranslation();
  const [data, setData] = useState<RendezVous[]>([]);
  const [loading, setLoading] = useState(true);

  useEffect(() => {
    medecinApi.getRendezVous()
      .then(setData)
      .catch(() => {})
      .finally(() => setLoading(false));
  }, []);

  const getStatusColor = (s: string) => {
    switch (s) {
      case 'confirme': return 'success';
      case 'en_attente': return 'warning';
      case 'annule': return 'error';
      case 'termine': return 'info';
      case 'no_show': return 'default';
      default: return 'default';
    }
  };

  if (loading) return <Box sx={{ p: 3 }}><Skeleton variant="rectangular" height={400} sx={{ borderRadius: 3 }} /></Box>;

  return (
    <Box sx={{ p: 3 }}>
      <Typography variant="h4" sx={{ fontWeight: 700, mb: 3 }}>{t('nav.appointments')}</Typography>
      <Card>
        <TableContainer>
          <Table>
            <TableHead>
              <TableRow>
                <TableCell>{t('common.date')}</TableCell>
                <TableCell>Patient</TableCell>
                <TableCell>Motif</TableCell>
                <TableCell>{t('common.status')}</TableCell>
                <TableCell>Notes</TableCell>
              </TableRow>
            </TableHead>
            <TableBody>
              {data.map((r) => (
                <TableRow key={r.id} hover>
                  <TableCell sx={{ whiteSpace: 'nowrap' }}>{r.dateHeure}</TableCell>
                  <TableCell sx={{ fontWeight: 500 }}>{r.patient}</TableCell>
                  <TableCell>{r.motif || '—'}</TableCell>
                  <TableCell>
                    <Chip label={r.statut} size="small" color={getStatusColor(r.statut)} />
                  </TableCell>
                  <TableCell sx={{ maxWidth: 250 }}>{r.notes || '—'}</TableCell>
                </TableRow>
              ))}
              {data.length === 0 && (
                <TableRow><TableCell colSpan={5} align="center">{t('common.noData')}</TableCell></TableRow>
              )}
            </TableBody>
          </Table>
        </TableContainer>
      </Card>
    </Box>
  );
}
