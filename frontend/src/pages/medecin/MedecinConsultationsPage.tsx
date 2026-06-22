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
import type { Consultation } from '../../types';

export default function MedecinConsultationsPage() {
  const { t } = useTranslation();
  const [data, setData] = useState<Consultation[]>([]);
  const [loading, setLoading] = useState(true);

  useEffect(() => {
    medecinApi.getConsultations()
      .then(setData)
      .catch(() => {})
      .finally(() => setLoading(false));
  }, []);

  if (loading) return <Box sx={{ p: 3 }}><Skeleton variant="rectangular" height={400} sx={{ borderRadius: 3 }} /></Box>;

  return (
    <Box sx={{ p: 3 }}>
      <Typography variant="h4" sx={{ fontWeight: 700, mb: 3 }}>{t('nav.consultations')}</Typography>
      <Card>
        <TableContainer>
          <Table>
            <TableHead>
              <TableRow>
                <TableCell>{t('common.date')}</TableCell>
                <TableCell>Patient</TableCell>
                <TableCell>Motif</TableCell>
                <TableCell>Diagnostic</TableCell>
                <TableCell>{t('common.status')}</TableCell>
              </TableRow>
            </TableHead>
            <TableBody>
              {data.map((c) => (
                <TableRow key={c.id} hover>
                  <TableCell sx={{ whiteSpace: 'nowrap' }}>{c.dateConsultation}</TableCell>
                  <TableCell sx={{ fontWeight: 500 }}>{c.patient}</TableCell>
                  <TableCell>{c.motif || '—'}</TableCell>
                  <TableCell sx={{ maxWidth: 300 }}>{c.diagnostic || '—'}</TableCell>
                  <TableCell>
                    <Chip
                      label={c.statut}
                      size="small"
                      color={c.statut === 'terminee' ? 'success' : c.statut === 'en_cours' ? 'info' : 'default'}
                    />
                  </TableCell>
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
