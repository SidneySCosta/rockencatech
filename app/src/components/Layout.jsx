import AppBar from '@mui/material/AppBar'
import Box from '@mui/material/Box'
import Button from '@mui/material/Button'
import Toolbar from '@mui/material/Toolbar'
import Typography from '@mui/material/Typography'
import ShoppingBagOutlinedIcon from '@mui/icons-material/ShoppingBagOutlined'
import LogoutIcon from '@mui/icons-material/Logout'
import { Link, Outlet, useNavigate } from 'react-router-dom'
import { useAuth } from '../contexts/AuthContext'
import api from '../services/api'

export default function Layout() {
  const { isAuthenticated, logout } = useAuth()
  const navigate = useNavigate()

  async function handleLogout() {
    try {
      await api.post('/logout')
    } catch {
      // token já pode estar inválido — continua o logout local
    }
    logout()
    navigate('/')
  }

  return (
    <>
      <AppBar
        position="sticky"
        elevation={0}
        sx={{ bgcolor: 'background.paper', borderBottom: '1px solid', borderColor: 'divider' }}
      >
        <Toolbar>
          <Box
            display="flex"
            alignItems="center"
            gap={1}
            sx={{ flexGrow: 1, cursor: 'pointer' }}
            onClick={() => navigate('/')}
          >
            <ShoppingBagOutlinedIcon sx={{ color: 'secondary.main', fontSize: 26 }} />
            <Typography
              variant="h6"
              sx={{ fontFamily: '"DM Serif Display", serif', color: 'text.primary', letterSpacing: 0.5 }}
            >
              ShopEasy
            </Typography>
          </Box>

          {isAuthenticated ? (
            <Button
              color="inherit"
              startIcon={<LogoutIcon />}
              onClick={handleLogout}
              sx={{ color: 'text.secondary' }}
            >
              Sair
            </Button>
          ) : (
            <>
              <Button color="inherit" component={Link} to="/login" sx={{ color: 'text.primary' }}>
                Login
              </Button>
              <Button variant="contained" component={Link} to="/register" sx={{ ml: 1 }}>
                Cadastrar-se
              </Button>
            </>
          )}
        </Toolbar>
      </AppBar>

      <Box component="main" sx={{ minHeight: '100vh', bgcolor: 'background.default' }}>
        <Outlet />
      </Box>
    </>
  )
}
