import { useState } from 'react';
import { Outlet, useNavigate, useLocation } from 'react-router-dom';
import { useTranslation } from 'react-i18next';
import Box from '@mui/material/Box';
import Drawer from '@mui/material/Drawer';
import AppBar from '@mui/material/AppBar';
import Toolbar from '@mui/material/Toolbar';
import Typography from '@mui/material/Typography';
import IconButton from '@mui/material/IconButton';
import Avatar from '@mui/material/Avatar';
import Menu from '@mui/material/Menu';
import MenuItem from '@mui/material/MenuItem';
import ListItemIcon from '@mui/material/ListItemIcon';
import Tooltip from '@mui/material/Tooltip';
import Divider from '@mui/material/Divider';
import List from '@mui/material/List';
import ListItem from '@mui/material/ListItem';
import ListItemButton from '@mui/material/ListItemButton';
import ListItemText from '@mui/material/ListItemText';
import Chip from '@mui/material/Chip';
import Badge from '@mui/material/Badge';
import { useAuth } from '../contexts/AuthContext';

import DashboardIcon from '@mui/icons-material/Dashboard';
import PeopleIcon from '@mui/icons-material/People';
import CalendarMonthIcon from '@mui/icons-material/CalendarMonth';
import MedicalServicesIcon from '@mui/icons-material/MedicalServices';
import DescriptionIcon from '@mui/icons-material/Description';
import MedicationIcon from '@mui/icons-material/Medication';
import MessageIcon from '@mui/icons-material/Message';
import NotificationsIcon from '@mui/icons-material/Notifications';
import BarChartIcon from '@mui/icons-material/BarChart';
import SecurityIcon from '@mui/icons-material/Security';
import SettingsIcon from '@mui/icons-material/Settings';
import LogoutIcon from '@mui/icons-material/Logout';
import LightModeIcon from '@mui/icons-material/LightMode';
import DarkModeIcon from '@mui/icons-material/DarkMode';
import LanguageIcon from '@mui/icons-material/Language';
import MenuIcon from '@mui/icons-material/Menu';
import ChevronLeftIcon from '@mui/icons-material/ChevronLeft';
import LocalHospitalIcon from '@mui/icons-material/LocalHospital';
import PersonIcon from '@mui/icons-material/Person';
import EditIcon from '@mui/icons-material/Edit';

const DRAWER_WIDTH = 260;

interface NavItem {
  label: string;
  icon: React.ReactNode;
  path: string;
}

export default function DashboardLayout() {
  const [collapsed, setCollapsed] = useState(false);
  const [darkMode, setDarkMode] = useState(() => localStorage.getItem('theme') === 'dark');
  const [anchorEl, setAnchorEl] = useState<null | HTMLElement>(null);
  const [langAnchorEl, setLangAnchorEl] = useState<null | HTMLElement>(null);

  const { t, i18n } = useTranslation();
  const { user, logout, hasRole } = useAuth();
  const navigate = useNavigate();
  const location = useLocation();

  const isDark = darkMode;
  const isRtl = i18n.language === 'ar';

  const bgMain = isDark ? '#0F1923' : '#F5F7FA';
  const bgDrawer = isDark ? '#0F1923' : '#FFFFFF';
  const bgAppBar = isDark ? '#1A2332' : '#FFFFFF';
  const textColor = isDark ? '#E2E8F0' : '#1A202C';
  const borderColor = isDark ? 'rgba(255,255,255,0.06)' : 'rgba(0,0,0,0.06)';

  const toggleTheme = () => {
    const newMode = !darkMode;
    setDarkMode(newMode);
    localStorage.setItem('theme', newMode ? 'dark' : 'light');
  };

  const changeLanguage = (lng: string) => {
    void i18n.changeLanguage(lng);
    document.documentElement.dir = lng === 'ar' ? 'rtl' : 'ltr';
    document.documentElement.lang = lng;
    setLangAnchorEl(null);
  };

  const drawerWidth = collapsed ? 72 : DRAWER_WIDTH;

  const navItems: NavItem[] = [
    { label: t('nav.dashboard'), icon: <DashboardIcon />, path: '/app/dashboard' },
    ...(hasRole('ROLE_ADMIN') || hasRole('ROLE_DIRECTEUR_MEDICAL') ? [
      { label: t('nav.patients'), icon: <PeopleIcon />, path: '/app/admin/patients' },
      { label: t('nav.users'), icon: <SecurityIcon />, path: '/app/admin/users' },
      { label: t('nav.auditLogs'), icon: <BarChartIcon />, path: '/app/admin/audit' },
      { label: t('nav.analytics'), icon: <BarChartIcon />, path: '/app/admin/analytics' },
    ] : []),
    ...(hasRole('ROLE_MEDECIN') ? [
      { label: t('nav.consultations'), icon: <MedicalServicesIcon />, path: '/app/medecin/consultations' },
      { label: t('nav.appointments'), icon: <CalendarMonthIcon />, path: '/app/medecin/rendez-vous' },
      { label: t('nav.prescriptions'), icon: <MedicationIcon />, path: '/app/medecin/prescriptions' },
      { label: t('nav.patients'), icon: <PeopleIcon />, path: '/app/medecin/patients' },
    ] : []),
    ...(hasRole('ROLE_PATIENT') ? [
      { label: t('nav.dashboard'), icon: <DashboardIcon />, path: '/app/patient/dashboard' },
      { label: t('nav.appointments'), icon: <CalendarMonthIcon />, path: '/app/patient/rdv' },
      { label: t('nav.medicalRecords'), icon: <DescriptionIcon />, path: '/app/patient/dossier' },
      { label: t('nav.messages'), icon: <MessageIcon />, path: '/app/patient/messages' },
      { label: t('nav.profile'), icon: <PersonIcon />, path: '/app/profile' },
    ] : []),
    ...(hasRole('ROLE_INFIRMIER') ? [
      { label: t('nav.consultations'), icon: <MedicalServicesIcon />, path: '/app/infirmier' },
      { label: t('nav.calendar'), icon: <CalendarMonthIcon />, path: '/app/infirmier' },
    ] : []),
    { label: t('nav.notifications'), icon: <NotificationsIcon />, path: '/app/notifications' },
    { label: t('nav.settings'), icon: <SettingsIcon />, path: '/app/settings' },
  ];

  return (
    <Box sx={{ display: 'flex', minHeight: '100vh', direction: isRtl ? 'rtl' : 'ltr' }}>
      <AppBar
        position="fixed"
        sx={{
          width: `calc(100% - ${drawerWidth}px)`,
          ml: `${drawerWidth}px`,
          bgcolor: bgAppBar,
          color: textColor,
          transition: 'width 0.2s, margin 0.2s',
        }}
        elevation={0}
      >
        <Toolbar>
          <IconButton onClick={() => setCollapsed(!collapsed)} edge="start" sx={{ mr: 1 }}>
            {collapsed ? <MenuIcon /> : <ChevronLeftIcon />}
          </IconButton>
          <Box sx={{ flexGrow: 1 }} />
          <Tooltip title={t('theme.light') + ' / ' + t('theme.dark')}>
            <IconButton onClick={toggleTheme} sx={{ mr: 1 }}>
              {isDark ? <LightModeIcon /> : <DarkModeIcon />}
            </IconButton>
          </Tooltip>
          <Tooltip title={t('language.fr')}>
            <IconButton onClick={(e) => setLangAnchorEl(e.currentTarget)} sx={{ mr: 1 }}>
              <LanguageIcon />
            </IconButton>
          </Tooltip>
          <Menu anchorEl={langAnchorEl} open={Boolean(langAnchorEl)} onClose={() => setLangAnchorEl(null)}>
            <MenuItem onClick={() => changeLanguage('fr')}>{t('language.fr')}</MenuItem>
            <MenuItem onClick={() => changeLanguage('en')}>{t('language.en')}</MenuItem>
            <MenuItem onClick={() => changeLanguage('ar')}>{t('language.ar')}</MenuItem>
          </Menu>
          {user && (
            <Tooltip title={t('common.profile')}>
              <IconButton onClick={(e) => setAnchorEl(e.currentTarget)}>
                <Badge color="primary" variant="dot">
                  <Avatar sx={{ width: 36, height: 36, bgcolor: '#006D77', fontSize: 14 }}>
                    {user?.firstName?.[0]}{user?.lastName?.[0]}
                  </Avatar>
                </Badge>
              </IconButton>
            </Tooltip>
          )}
          <Menu anchorEl={anchorEl} open={Boolean(anchorEl)} onClose={() => setAnchorEl(null)}>
            <MenuItem dense disabled>
              <Typography variant="body2" color="text.secondary">{user?.email}</Typography>
            </MenuItem>
            <Divider />
            <MenuItem onClick={() => { setAnchorEl(null); navigate('/app/profile'); }}>
              <ListItemIcon><Avatar sx={{ width: 20, height: 20, fontSize: 10 }} /></ListItemIcon>
              {t('common.profile')}
            </MenuItem>
            <MenuItem onClick={() => { setAnchorEl(null); logout(); navigate('/'); }}>
              <ListItemIcon><LogoutIcon fontSize="small" /></ListItemIcon>
              {t('common.logout')}
            </MenuItem>
          </Menu>
        </Toolbar>
      </AppBar>

      <Drawer
        variant="permanent"
        sx={{
          width: drawerWidth,
          flexShrink: 0,
          '& .MuiDrawer-paper': {
            width: drawerWidth,
            boxSizing: 'border-box',
            bgcolor: bgDrawer,
            borderRight: `1px solid ${borderColor}`,
            transition: 'width 0.2s',
            overflowX: 'hidden',
          },
        }}
      >
        <Toolbar sx={{ display: 'flex', alignItems: 'center', px: collapsed ? 1 : 2 }}>
          <LocalHospitalIcon sx={{ color: '#006D77', fontSize: 32, mr: collapsed ? 0 : 1 }} />
          {!collapsed && (
            <Typography variant="h6" sx={{ fontWeight: 700, color: '#006D77', whiteSpace: 'nowrap' }}>
              Hôpital
            </Typography>
          )}
        </Toolbar>
        <Divider />
        {!collapsed && user && (
          <Box
            sx={{
              display: 'flex',
              alignItems: 'center',
              gap: 1.5,
              px: 2,
              py: 1.5,
              cursor: 'pointer',
              borderRadius: 0,
              '&:hover': { bgcolor: isDark ? 'rgba(255,255,255,0.04)' : 'rgba(0,0,0,0.02)' },
            }}
            onClick={() => navigate('/app/profile')}
          >
            <Avatar sx={{ width: 38, height: 38, bgcolor: '#006D77', fontSize: 14, fontWeight: 600 }}>
              {user?.firstName?.[0]}{user?.lastName?.[0]}
            </Avatar>
            <Box sx={{ flex: 1, minWidth: 0 }}>
              <Typography variant="body2" sx={{ fontWeight: 600, lineHeight: 1.2, color: textColor }}>
                {user?.firstName} {user?.lastName}
              </Typography>
              <Typography variant="caption" color="text.secondary" sx={{ display: 'block', lineHeight: 1.2 }}>
                {user?.roles?.includes('ROLE_ADMIN') ? 'Administrateur' : user?.roles?.includes('ROLE_MEDECIN') ? 'Médecin' : user?.roles?.includes('ROLE_PATIENT') ? 'Patient' : user?.roles?.includes('ROLE_INFIRMIER') ? 'Infirmier' : 'Utilisateur'}
              </Typography>
            </Box>
            <EditIcon sx={{ fontSize: 16, color: 'text.secondary', opacity: 0.6 }} />
          </Box>
        )}
        <Divider />
        <List sx={{ px: 1 }}>
          {navItems.map((item) => {
            const active = location.pathname === item.path;
            return (
              <ListItem key={item.path} disablePadding sx={{ mb: 0.5 }}>
                <ListItemButton
                  onClick={() => navigate(item.path)}
                  selected={active}
                  sx={{
                    borderRadius: 2,
                    minHeight: 44,
                    px: collapsed ? 1 : 2,
                    '&.Mui-selected': {
                      bgcolor: isDark ? 'rgba(0,109,119,0.15)' : 'rgba(0,109,119,0.08)',
                      '&:hover': { bgcolor: isDark ? 'rgba(0,109,119,0.25)' : 'rgba(0,109,119,0.12)' },
                    },
                  }}
                >
                  <Box
                    sx={{
                      minWidth: collapsed ? 0 : 40,
                      display: 'flex',
                      alignItems: 'center',
                      color: active ? '#006D77' : undefined,
                    }}
                  >
                    {item.icon}
                  </Box>
                  {!collapsed && (
                    <ListItemText
                      primary={item.label}
                      sx={{
                        '& .MuiListItemText-primary': {
                          fontSize: 14,
                          fontWeight: active ? 600 : 400,
                        },
                      }}
                    />
                  )}
                </ListItemButton>
              </ListItem>
            );
          })}
        </List>
        <Box sx={{ flexGrow: 1 }} />
        {!collapsed && user && (
          <Box sx={{ px: 2, py: 2 }}>
            <Chip
              icon={<LocalHospitalIcon />}
              label={user?.roles?.includes('ROLE_ADMIN') ? 'Administrateur' : user?.roles?.includes('ROLE_MEDECIN') ? 'Médecin' : user?.roles?.includes('ROLE_PATIENT') ? 'Patient' : 'Utilisateur'}
              color="primary"
              variant="outlined"
              size="small"
              sx={{ width: '100%' }}
            />
          </Box>
        )}
      </Drawer>

      <Box
        component="main"
        sx={{
          flexGrow: 1,
          bgcolor: bgMain,
          minHeight: '100vh',
          pt: '64px',
          transition: 'margin 0.2s',
        }}
      >
        <Outlet />
      </Box>
    </Box>
  );
}
