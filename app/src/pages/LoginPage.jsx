import { useState } from 'react'
import { Navigate, Link, useNavigate } from 'react-router-dom'
import Alert from '@mui/material/Alert'
import Box from '@mui/material/Box'
import Button from '@mui/material/Button'
import CircularProgress from '@mui/material/CircularProgress'
import Paper from '@mui/material/Paper'
import TextField from '@mui/material/TextField'
import Typography from '@mui/material/Typography'
import ShoppingBagOutlinedIcon from '@mui/icons-material/ShoppingBagOutlined'
import { useAuth } from '../contexts/AuthContext'
import api from '../services/api'

export default function LoginPage() {
  const { isAuthenticated, login } = useAuth()
  const navigate = useNavigate()

  const [email,    setEmail]    = useState('')
  const [password, setPassword] = useState('')
  const [loading,  setLoading]  = useState(false)
  const [apiError, setApiError] = useState(null)
  const [errors,   setErrors]   = useState({})

  if (isAuthenticated) return <Navigate to="/" replace />

  function validate() {
    const e = {}
    if (!email)    e.email    = 'Email é obrigatório'
    if (!password) e.password = 'Senha é obrigatória'
    setErrors(e)
    return Object.keys(e).length === 0
  }

  async function handleSubmit(e) {
    e.preventDefault()
    if (!validate()) return

    setLoading(true)
    setApiError(null)
    try {
      const { data } = await api.post('/login', { email, password })
      login(data.token)
      navigate('/')
    } catch (err) {
      if (err.response?.status === 401) {
        setApiError('Credenciais inválidas. Verifique seu email e senha.')
      } else {
        setApiError('Erro ao conectar. Tente novamente.')
      }
    } finally {
      setLoading(false)
    }
  }

  return (
    <Box
      minHeight="100vh"
      display="flex"
      alignItems="center"
      justifyContent="center"
      px={2}
      sx={{ bgcolor: 'background.default' }}
    >
      <Paper
        elevation={0}
        sx={{
          width: '100%',
          maxWidth: 420,
          p: 4,
          border: '1px solid',
          borderColor: 'divider',
          borderRadius: 2,
        }}
      >
        {/* Logo */}
        <Box display="flex" alignItems="center" gap={1} mb={3}>
          <ShoppingBagOutlinedIcon sx={{ color: 'secondary.main', fontSize: 28 }} />
          <Typography
            variant="h5"
            sx={{ fontFamily: '"DM Serif Display", serif', color: 'text.primary' }}
          >
            ShopEasy
          </Typography>
        </Box>

        <Typography variant="h6" fontWeight={700} mb={0.5}>
          Entrar na sua conta
        </Typography>
        <Typography variant="body2" color="text.secondary" mb={3}>
          Insira seus dados para continuar
        </Typography>

        <Box component="form" onSubmit={handleSubmit} noValidate>
          <TextField
            label="Email"
            type="email"
            fullWidth
            value={email}
            onChange={(e) => setEmail(e.target.value)}
            error={!!errors.email}
            helperText={errors.email}
            margin="normal"
            autoComplete="email"
          />
          <TextField
            label="Senha"
            type="password"
            fullWidth
            value={password}
            onChange={(e) => setPassword(e.target.value)}
            error={!!errors.password}
            helperText={errors.password}
            margin="normal"
            autoComplete="current-password"
          />

          {apiError && (
            <Alert severity="error" sx={{ mt: 2 }}>
              {apiError}
            </Alert>
          )}

          <Button
            type="submit"
            variant="contained"
            fullWidth
            size="large"
            disabled={loading}
            sx={{ mt: 3, mb: 2, py: 1.5 }}
          >
            {loading ? <CircularProgress size={24} color="inherit" /> : 'Entrar'}
          </Button>

          <Typography variant="body2" color="text.secondary" textAlign="center">
            Não tem uma conta?{' '}
            <Link to="/register" style={{ color: '#C25A36', textDecoration: 'none', fontWeight: 600 }}>
              Cadastre-se
            </Link>
          </Typography>
        </Box>
      </Paper>
    </Box>
  )
}
