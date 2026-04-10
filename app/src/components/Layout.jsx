import { useState } from 'react'
import AppBar from '@mui/material/AppBar'
import Avatar from '@mui/material/Avatar'
import Box from '@mui/material/Box'
import Button from '@mui/material/Button'
import Container from '@mui/material/Container'
import Divider from '@mui/material/Divider'
import IconButton from '@mui/material/IconButton'
import ListItemIcon from '@mui/material/ListItemIcon'
import Menu from '@mui/material/Menu'
import MenuItem from '@mui/material/MenuItem'
import Toolbar from '@mui/material/Toolbar'
import Typography from '@mui/material/Typography'
import CategoryIcon from '@mui/icons-material/Category'
import LogoutIcon from '@mui/icons-material/Logout'
import ShoppingBagOutlinedIcon from '@mui/icons-material/ShoppingBagOutlined'
import { Link, Outlet, useNavigate } from 'react-router-dom'
import { useAuth } from '../contexts/AuthContext'
import CategoryManagerModal from './CategoryManagerModal'
import api from '../services/api'

export default function Layout() {
  const { isAuthenticated, user, logout } = useAuth()

  const initials = user?.name
    ? user.name.split(' ').map((n) => n[0]).slice(0, 2).join('').toUpperCase()
    : '?'
  const navigate = useNavigate()
  const [menuAnchor, setMenuAnchor] = useState(null)
  const [categoryOpen, setCategoryOpen] = useState(false)

  async function handleLogout() {
    try {
      await api.post('/logout')
    } catch {
      // token já pode estar inválido — continua o logout local
    }
    logout()
    navigate('/')
  }

  function openMenu(e) { setMenuAnchor(e.currentTarget) }
  function closeMenu() { setMenuAnchor(null) }

  function handleCategoryOpen() {
    closeMenu()
    setCategoryOpen(true)
  }

  function handleMenuLogout() {
    closeMenu()
    handleLogout()
  }

  return (
    <>
      <AppBar
        position="sticky"
        elevation={0}
        sx={{ bgcolor: 'background.paper', borderBottom: '1px solid', borderColor: 'divider' }}
      >
        <Toolbar disableGutters sx={{ width: '100%' }}>
          <Container maxWidth="xl" sx={{ display: 'flex', flexDirection: 'row', alignItems: 'center' }}>
          <Box
            onClick={() => navigate('/')}
            sx={{
              flexGrow: 1,
              cursor: 'pointer',
              display: 'flex',
              flexDirection: 'row',
              alignItems: 'center',
              gap: '8px',
            }}
          >
            <ShoppingBagOutlinedIcon sx={{ color: 'secondary.main', fontSize: 26, display: 'block' }} />
            <Typography
              variant="h6"
              sx={{ fontFamily: '"DM Serif Display", serif', color: 'text.primary', letterSpacing: 0.5, lineHeight: 1 }}
            >
              ShopEasy
            </Typography>
          </Box>

          {isAuthenticated ? (
            <>
              <IconButton onClick={openMenu} sx={{ p: 0.5 }}>
                <Avatar
                  sx={{
                    width: 36,
                    height: 36,
                    bgcolor: 'secondary.main',
                    fontSize: 14,
                    fontWeight: 700,
                  }}
                >
                  {initials}
                </Avatar>
              </IconButton>
              <Menu
                anchorEl={menuAnchor}
                open={!!menuAnchor}
                onClose={closeMenu}
                transformOrigin={{ horizontal: 'right', vertical: 'top' }}
                anchorOrigin={{ horizontal: 'right', vertical: 'bottom' }}
                slotProps={{ paper: { sx: { minWidth: 220 } } }}
              >
                <Box sx={{ px: 2, py: 1.5 }}>
                  <Typography variant="subtitle2" fontWeight={600} noWrap>
                    {user?.name ?? 'Usuário'}
                  </Typography>
                  <Typography variant="caption" color="text.secondary" noWrap>
                    {user?.email ?? ''}
                  </Typography>
                </Box>
                <Divider />
                <MenuItem onClick={handleCategoryOpen}>
                  <ListItemIcon><CategoryIcon fontSize="small" /></ListItemIcon>
                  Gerenciar Categorias
                </MenuItem>
                <MenuItem onClick={handleMenuLogout}>
                  <ListItemIcon><LogoutIcon fontSize="small" /></ListItemIcon>
                  Sair
                </MenuItem>
              </Menu>
            </>
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
          </Container>
        </Toolbar>
      </AppBar>

      <Box component="main" sx={{ minHeight: '100vh', bgcolor: 'background.default' }}>
        <Outlet />
      </Box>

      <CategoryManagerModal open={categoryOpen} onClose={() => setCategoryOpen(false)} />
    </>
  )
}
