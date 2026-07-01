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
import Avatar from '@mui/material/Avatar';
import Skeleton from '@mui/material/Skeleton';
import TextField from '@mui/material/TextField';
import InputAdornment from '@mui/material/InputAdornment';
import SearchIcon from '@mui/icons-material/Search';
import { adminApi } from '../../api/services';
import type { Utilisateur } from '../../types';

export default function AdminUsersPage() {
  const { t } = useTranslation();
  const [users, setUsers] = useState<Utilisateur[]>([]);
  const [loading, setLoading] = useState(true);
  const [search, setSearch] = useState('');

  useEffect(() => {
    adminApi.getUtilisateurs()
      .then(setUsers)
      .catch(() => {})
      .finally(() => setLoading(false));
  }, []);

  const filtered = users.filter((u) =>
    `${u.nom} ${u.prenom} ${u.email}`.toLowerCase().includes(search.toLowerCase())
  );

  const getRoleChip = (role: string) => {
    const map: Record<string, { label: string; color: any }> = {
      ROLE_ADMIN: { label: 'Admin', color: 'error' },
      ROLE_DIRECTEUR_MEDICAL: { label: 'Dr. Medical', color: 'warning' },
      ROLE_MEDECIN: { label: 'Médecin', color: 'primary' },
      ROLE_INFIRMIER: { label: 'Infirmier', color: 'info' },
      ROLE_SECRETAIRE: { label: 'Secrétaire', color: 'secondary' },
      ROLE_PATIENT: { label: 'Patient', color: 'success' },
    };
    const found = map[role] || { label: role, color: 'default' };
    return <Chip key={role} label={found.label} color={found.color as any} size="small" sx={{ mr: 0.5 }} />;
  };

  return (
    <Box sx={{ p: 3 }}>
      <Typography variant="h4" sx={{ fontWeight: 700, mb: 1 }}>{t('admin.userManagement')}</Typography>
      <TextField
        size="small"
        placeholder={t('common.search')}
        value={search}
        onChange={(e) => setSearch(e.target.value)}
        sx={{ mb: 3, width: 300 }}
        slotProps={{
          input: {
            startAdornment: <InputAdornment position="start"><SearchIcon /></InputAdornment>,
          },
        }}
      />
      {loading ? (
        <Skeleton variant="rectangular" height={400} sx={{ borderRadius: 3 }} />
      ) : (
        <Card>
          <TableContainer>
            <Table>
              <TableHead>
                <TableRow>
                  <TableCell>{t('common.profile')}</TableCell>
                  <TableCell>Email</TableCell>
                  <TableCell>Téléphone</TableCell>
                  <TableCell>Rôles</TableCell>
                  <TableCell>Statut</TableCell>
                  <TableCell>Profil</TableCell>
                  <TableCell>{t('common.date')}</TableCell>
                </TableRow>
              </TableHead>
              <TableBody>
                {filtered.map((u) => (
                  <TableRow key={u.id} hover sx={{ '&:last-child td': { border: 0 } }}>
                    <TableCell>
                      <Box sx={{ display: 'flex', alignItems: 'center', gap: 1.5 }}>
                        <Avatar sx={{ width: 36, height: 36, fontSize: 14, bgcolor: '#006D77' }}>
                          {u.prenom?.[0]}{u.nom?.[0]}
                        </Avatar>
                        <Box>
                          <Typography variant="body2" sx={{ fontWeight: 600 }}>{u.prenom} {u.nom}</Typography>
                        </Box>
                      </Box>
                    </TableCell>
                    <TableCell>{u.email}</TableCell>
                    <TableCell>{u.telephone || '—'}</TableCell>
                    <TableCell>{u.roles.map(getRoleChip)}</TableCell>
                    <TableCell>
                      <Chip
                        label={u.actif ? 'Actif' : 'Inactif'}
                        color={u.actif ? 'success' : 'default'}
                        size="small"
                      />
                    </TableCell>
                    <TableCell>
                      <Chip label={u.profil} variant="outlined" size="small" />
                    </TableCell>
                    <TableCell>{u.dateCreation}</TableCell>
                  </TableRow>
                ))}
              </TableBody>
            </Table>
          </TableContainer>
        </Card>
      )}
    </Box>
  );
}
