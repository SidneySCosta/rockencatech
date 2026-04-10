import { useState } from 'react'
import { Navigate, Link, useNavigate } from 'react-router-dom'
import Alert from '@mui/material/Alert'
import Box from '@mui/material/Box'
import Button from '@mui/material/Button'
import CircularProgress from '@mui/material/CircularProgress'
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
      login(data.token, data.data)
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
      sx={{
        minHeight: 'calc(100vh - 64px)',
        width: '100%',
        display: 'flex',
        flexDirection: 'column',
        alignItems: 'center',
        justifyContent: 'center',
        bgcolor: 'background.default',
        px: 2,
      }}
    >
      {/* Logo */}
      <Box sx={{ display: 'flex', flexDirection: 'row', alignItems: 'center', gap: 1, mb: 3 }}>
        <ShoppingBagOutlinedIcon sx={{ color: 'secondary.main', fontSize: 32 }} />
        <Typography
          variant="h5"
          sx={{ fontFamily: '"DM Serif Display", serif', color: 'text.primary' }}
        >
          ShopEasy
        </Typography>
      </Box>

      <Typography variant="h4" sx={{ textAlign: 'center' }} mb={0.5}>
        Entrar na sua conta
      </Typography>
      <Typography variant="body2" color="text.secondary" sx={{ textAlign: 'center' }} mb={4}>
        Insira seus dados para continuar
      </Typography>

      <Box component="form" onSubmit={handleSubmit} noValidate sx={{ width: '100%', maxWidth: 400 }}>
        <TextField
          label="E-mail"
          type="email"
          fullWidth
          placeholder="seu@email.com"
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
          placeholder="••••••••"
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
          color="secondary"
          fullWidth
          size="large"
          disabled={loading}
          sx={{ mt: 3, mb: 2, py: 1.5 }}
        >
          {loading ? <CircularProgress size={24} color="inherit" /> : 'Entrar'}
        </Button>

        <Typography variant="body2" color="text.secondary" sx={{ textAlign: 'center' }}>
          Não tem uma conta?{' '}
          <Link to="/register" style={{ color: '#C25A36', textDecoration: 'none', fontWeight: 600 }}>
            Cadastre-se
          </Link>
        </Typography>
      </Box>
    </Box>
  )
}
