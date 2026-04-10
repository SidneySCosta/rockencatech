import { useEffect, useState } from 'react'
import { Link, useNavigate, useParams } from 'react-router-dom'
import Box from '@mui/material/Box'
import Button from '@mui/material/Button'
import Chip from '@mui/material/Chip'
import Container from '@mui/material/Container'
import Divider from '@mui/material/Divider'
import Skeleton from '@mui/material/Skeleton'
import Typography from '@mui/material/Typography'
import ArrowBackIcon from '@mui/icons-material/ArrowBack'
import CheckroomIcon from '@mui/icons-material/Checkroom'
import api from '../services/api'

const formatBRL = (value) =>
  new Intl.NumberFormat('pt-BR', { style: 'currency', currency: 'BRL' }).format(value)

export default function ProductDetailPage() {
  const { id } = useParams()
  const navigate = useNavigate()

  const [product,  setProduct]  = useState(null)
  const [loading,  setLoading]  = useState(true)
  const [notFound, setNotFound] = useState(false)
  const [error,    setError]    = useState(null)

  useEffect(() => {
    setLoading(true)
    setNotFound(false)
    setError(null)
    api
      .get(`/products/${id}`)
      .then(({ data }) => setProduct(data.data))
      .catch((err) => {
        if (err.response?.status === 404) setNotFound(true)
        else setError('Erro ao carregar produto.')
      })
      .finally(() => setLoading(false))
  }, [id])

  if (notFound) {
    return (
      <Container maxWidth="lg" sx={{ py: 4 }}>
        <Box textAlign="center" py={10}>
          <CheckroomIcon sx={{ fontSize: 80, color: 'text.secondary', mb: 2, opacity: 0.4 }} />
          <Typography variant="h5" gutterBottom>
            Produto não encontrado
          </Typography>
          <Typography color="text.secondary" mb={3}>
            O produto que você está procurando não existe ou foi removido.
          </Typography>
          <Button component={Link} to="/" variant="contained">
            Voltar ao catálogo
          </Button>
        </Box>
      </Container>
    )
  }

  if (error) {
    return (
      <Container maxWidth="lg" sx={{ py: 4 }}>
        <Box textAlign="center" py={10}>
          <Typography variant="h6" color="error" gutterBottom>
            {error}
          </Typography>
          <Button variant="contained" onClick={() => navigate(-1)}>
            Voltar
          </Button>
        </Box>
      </Container>
    )
  }

  return (
    <Container maxWidth="lg" sx={{ py: 4 }}>
      <Button startIcon={<ArrowBackIcon />} onClick={() => navigate(-1)} sx={{ mb: 3 }}>
        Voltar ao catálogo
      </Button>

      <Box display="flex" gap={5} flexDirection={{ xs: 'column', md: 'row' }}>
        {/* Image */}
        <Box
          sx={{
            flexShrink: 0,
            width: { xs: '100%', md: 440 },
            height: { xs: 320, md: 480 },
            bgcolor: '#F0EAE3',
            borderRadius: 2,
            display: 'flex',
            alignItems: 'center',
            justifyContent: 'center',
            overflow: 'hidden',
            border: '1px solid',
            borderColor: 'divider',
          }}
        >
          {loading ? (
            <Skeleton variant="rectangular" width="100%" height="100%" />
          ) : product?.image_url ? (
            <img
              src={product.image_url}
              alt={product.name}
              style={{ width: '100%', height: '100%', objectFit: 'cover' }}
            />
          ) : (
            <CheckroomIcon sx={{ fontSize: 120, color: 'text.secondary', opacity: 0.4 }} />
          )}
        </Box>

        {/* Details */}
        <Box flexGrow={1} pt={{ xs: 0, md: 1 }}>
          {loading ? (
            <>
              <Skeleton width="40%" sx={{ mb: 1 }} />
              <Skeleton width="70%" height={50} sx={{ mb: 1 }} />
              <Skeleton width="30%" height={48} sx={{ mb: 2 }} />
              <Skeleton width="100%" />
              <Skeleton width="100%" />
              <Skeleton width="80%" />
            </>
          ) : (
            <>
              <Chip
                label={product.category?.name}
                size="small"
                variant="outlined"
                sx={{ mb: 2, borderColor: 'divider', color: 'text.secondary' }}
              />
              <Typography variant="h4" gutterBottom>
                {product.name}
              </Typography>
              <Typography variant="h5" color="secondary.main" fontWeight={700} gutterBottom>
                {formatBRL(product.price)}
              </Typography>

              {product.description && (
                <>
                  <Divider sx={{ my: 3 }} />
                  <Typography variant="body1" color="text.secondary" sx={{ lineHeight: 1.9 }}>
                    {product.description}
                  </Typography>
                </>
              )}

              <Button variant="contained" size="large" sx={{ mt: 4, px: 5, py: 1.5 }}>
                Adicionar ao carrinho
              </Button>
            </>
          )}
        </Box>
      </Box>
    </Container>
  )
}
