import { createTheme, responsiveFontSizes } from '@mui/material/styles';

const medicalPalette = {
  primary: {
    main: '#006D77',
    light: '#83C5BE',
    dark: '#004D56',
    contrastText: '#FFFFFF',
  },
  secondary: {
    main: '#E29578',
    light: '#FFDDD2',
    dark: '#C87254',
    contrastText: '#FFFFFF',
  },
  error: {
    main: '#D32F2F',
    light: '#EF5350',
    dark: '#C62828',
  },
  warning: {
    main: '#F4A261',
    light: '#FFB74D',
    dark: '#E68619',
  },
  info: {
    main: '#0288D1',
    light: '#4FC3F7',
    dark: '#01579B',
  },
  success: {
    main: '#2E7D32',
    light: '#81C784',
    dark: '#1B5E20',
  },
};

const lightTheme = responsiveFontSizes(createTheme({
  palette: {
    mode: 'light',
    ...medicalPalette,
    background: {
      default: '#F5F7FA',
      paper: '#FFFFFF',
    },
    text: {
      primary: '#1A202C',
      secondary: '#4A5568',
    },
  },
  typography: {
    fontFamily: '"Inter", "Roboto", "Helvetica Neue", Arial, sans-serif',
    h1: { fontWeight: 700, fontSize: '2rem' },
    h2: { fontWeight: 600, fontSize: '1.75rem' },
    h3: { fontWeight: 600, fontSize: '1.5rem' },
    h4: { fontWeight: 600, fontSize: '1.25rem' },
    h5: { fontWeight: 600, fontSize: '1.1rem' },
    h6: { fontWeight: 600, fontSize: '1rem' },
    button: { textTransform: 'none', fontWeight: 600 },
  },
  shape: {
    borderRadius: 12,
  },
  components: {
    MuiCard: {
      styleOverrides: {
        root: {
          boxShadow: '0 2px 12px rgba(0,0,0,0.08)',
          borderRadius: 16,
          border: '1px solid rgba(0,0,0,0.04)',
        },
      },
    },
    MuiCardContent: {
      styleOverrides: {
        root: {
          padding: 24,
        },
      },
    },
    MuiButton: {
      styleOverrides: {
        root: {
          borderRadius: 10,
          padding: '10px 24px',
        },
        contained: {
          boxShadow: '0 2px 8px rgba(0,0,0,0.12)',
        },
      },
    },
    MuiDrawer: {
      styleOverrides: {
        paper: {
          border: 'none',
          boxShadow: '2px 0 12px rgba(0,0,0,0.05)',
        },
      },
    },
    MuiAppBar: {
      styleOverrides: {
        root: {
          boxShadow: '0 1px 8px rgba(0,0,0,0.08)',
        },
      },
    },
    MuiChip: {
      styleOverrides: {
        root: {
          borderRadius: 8,
          fontWeight: 500,
        },
      },
    },
    MuiTableHead: {
      styleOverrides: {
        root: {
          '& .MuiTableCell-head': {
            fontWeight: 600,
            color: '#4A5568',
          },
        },
      },
    },
  },
}));

const darkTheme = responsiveFontSizes(createTheme({
  palette: {
    mode: 'dark',
    primary: medicalPalette.primary,
    secondary: medicalPalette.secondary,
    error: medicalPalette.error,
    warning: medicalPalette.warning,
    info: medicalPalette.info,
    success: medicalPalette.success,
    background: {
      default: '#0F1923',
      paper: '#1A2332',
    },
    text: {
      primary: '#E2E8F0',
      secondary: '#A0AEC0',
    },
  },
  typography: lightTheme.typography,
  shape: lightTheme.shape,
  components: {
    ...lightTheme.components,
    MuiCard: {
      styleOverrides: {
        root: {
          boxShadow: '0 2px 12px rgba(0,0,0,0.3)',
          borderRadius: 16,
          border: '1px solid rgba(255,255,255,0.06)',
          background: '#1A2332',
        },
      },
    },
    MuiDrawer: {
      styleOverrides: {
        paper: {
          border: 'none',
          boxShadow: '2px 0 12px rgba(0,0,0,0.2)',
        },
      },
    },
  },
}));

export { lightTheme, darkTheme };
