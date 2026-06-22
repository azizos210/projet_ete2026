import { useState, useEffect } from 'react';
import { useTranslation } from 'react-i18next';
import Box from '@mui/material/Box';
import Typography from '@mui/material/Typography';
import Card from '@mui/material/Card';
import Table from '@mui/material/Table';
import TableBody from '@mui/material/TableBody';
import TableCell from '@mui/material/TableCell';
import TableContainer from '@mui/material/TableContainer';
import TableHead from '@mui/material/TableHead';
import TableRow from '@mui/material/TableRow';
import Chip from '@mui/material/Chip';
import Skeleton from '@mui/material/Skeleton';
import { adminApi } from '../../api/services';
import type { AuditLogEntry } from '../../types';

export default function AdminAuditLogsPage() {
  const { t } = useTranslation();
  const [logs, setLogs] = useState<AuditLogEntry[]>([]);
  const [loading, setLoading] = useState(true);

  useEffect(() => {
    adminApi.getAuditLogs()
      .then(setLogs)
      .catch(() => {})
      .finally(() => setLoading(false));
  }, []);

  return (
    <Box sx={{ p: 3 }}>
      <Typography variant="h4" sx={{ fontWeight: 700, mb: 3 }}>{t('nav.auditLogs')}</Typography>
      {loading ? (
        <Skeleton variant="rectangular" height={400} sx={{ borderRadius: 3 }} />
      ) : (
        <Card>
          <TableContainer>
            <Table>
              <TableHead>
                <TableRow>
                  <TableCell>Date</TableCell>
                  <TableCell>{t('common.profile')}</TableCell>
                  <TableCell>Action</TableCell>
                  <TableCell>Entité</TableCell>
                  <TableCell>Détails</TableCell>
                  <TableCell>IP</TableCell>
                </TableRow>
              </TableHead>
              <TableBody>
                {logs.map((log) => (
                  <TableRow key={log.id} hover sx={{ '&:last-child td': { border: 0 } }}>
                    <TableCell sx={{ whiteSpace: 'nowrap' }}>{log.dateAction}</TableCell>
                    <TableCell>{log.utilisateur}</TableCell>
                    <TableCell>
                      <Chip label={log.action} size="small" color="info" variant="outlined" />
                    </TableCell>
                    <TableCell>{log.entite} #{log.entiteId}</TableCell>
                    <TableCell sx={{ maxWidth: 300, overflow: 'hidden', textOverflow: 'ellipsis' }}>
                      {log.details || '—'}
                    </TableCell>
                    <TableCell>{log.adresseIp || '—'}</TableCell>
                  </TableRow>
                ))}
                {logs.length === 0 && (
                  <TableRow>
                    <TableCell colSpan={6} align="center">
                      <Typography color="text.secondary">{t('common.noData')}</Typography>
                    </TableCell>
                  </TableRow>
                )}
              </TableBody>
            </Table>
          </TableContainer>
        </Card>
      )}
    </Box>
  );
}
