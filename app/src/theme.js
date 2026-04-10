import { createTheme } from '@mui/material/styles'

export default createTheme({
  palette: {
    mode: 'light',
    primary: { main: '#1C2030', contrastText: '#F8F5F0' },
    secondary: { main: '#C25A36', contrastText: '#FFFFFF' },
    background: { default: '#F8F5F0', paper: '#FFFFFF' },
    text: { primary: '#1C2030', secondary: '#6B7189' },
    divider: '#E6DDD4',
  },
  typography: {
    fontFamily: '"DM Sans", "Roboto", sans-serif',
    h1: { fontFamily: '"DM Serif Display", serif', fontWeight: 400 },
    h2: { fontFamily: '"DM Serif Display", serif', fontWeight: 400 },
    h3: { fontFamily: '"DM Serif Display", serif', fontWeight: 400 },
    h4: { fontFamily: '"DM Serif Display", serif', fontWeight: 400 },
    h5: { fontWeight: 700 },
    h6: { fontWeight: 700 },
  },
  shape: { borderRadius: 8 },
  components: {
    MuiButton: {
      defaultProps: { disableElevation: true },
      styleOverrides: { root: { textTransform: 'none', fontWeight: 600 } },
    },
    MuiCard: {
      styleOverrides: { root: { backgroundImage: 'none', boxShadow: 'none', border: '1px solid #E6DDD4' } },
    },
    MuiPaper: {
      styleOverrides: { root: { backgroundImage: 'none' } },
    },
    MuiAppBar: {
      styleOverrides: {
        root: {
          backgroundImage: 'none',
          boxShadow: 'none',
        },
      },
    },
    MuiChip: {
      styleOverrides: { root: { fontWeight: 500 } },
    },
  },
})
