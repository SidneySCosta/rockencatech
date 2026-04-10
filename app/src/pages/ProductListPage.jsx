import { useEffect, useState } from 'react'
import Alert from '@mui/material/Alert'
import Box from '@mui/material/Box'
import Button from '@mui/material/Button'
import Container from '@mui/material/Container'
import FormControl from '@mui/material/FormControl'
import Grid from '@mui/material/Grid'
import InputLabel from '@mui/material/InputLabel'
import MenuItem from '@mui/material/MenuItem'
import Pagination from '@mui/material/Pagination'
import Select from '@mui/material/Select'
import Skeleton from '@mui/material/Skeleton'
import Stack from '@mui/material/Stack'
import TextField from '@mui/material/TextField'
import Typography from '@mui/material/Typography'
import AddIcon from '@mui/icons-material/Add'
import CategoryIcon from '@mui/icons-material/Category'
import CheckroomIcon from '@mui/icons-material/Checkroom'
import SearchIcon from '@mui/icons-material/Search'
import InputAdornment from '@mui/material/InputAdornment'
import Divider from '@mui/material/Divider'
import ProductCard from '../components/ProductCard'
import ProductFormModal from '../components/ProductFormModal'
import DeleteConfirmDialog from '../components/DeleteConfirmDialog'
import CategoryManagerModal from '../components/CategoryManagerModal'
import { useAuth } from '../contexts/AuthContext'
import api from '../services/api'

export default function ProductListPage() {
  const { isAuthenticated } = useAuth()

  const [products,        setProducts]        = useState([])
  const [meta,            setMeta]            = useState({ total: 0, last_page: 1, current_page: 1, per_page: 12 })
  const [categories,      setCategories]      = useState([])
  const [loading,         setLoading]         = useState(true)
  const [error,           setError]           = useState(null)
  const [search,          setSearch]          = useState('')
  const [category,        setCategory]        = useState('')
  const [page,            setPage]            = useState(1)
  const [debouncedSearch, setDebouncedSearch] = useState('')

  // Modal state
  const [createOpen,     setCreateOpen]     = useState(false)
  const [editProduct,    setEditProduct]    = useState(null)
  const [deleteProduct,  setDeleteProduct]  = useState(null)
  const [deleteLoading,  setDeleteLoading]  = useState(false)
  const [categoryOpen,   setCategoryOpen]   = useState(false)

  useEffect(() => {
    const timer = setTimeout(() => {
      setDebouncedSearch(search)
      setPage(1)
    }, 500)
    return () => clearTimeout(timer)
  }, [search])

  useEffect(() => {
    api.get('/categories').then(({ data }) => setCategories(data.data)).catch(() => {})
  }, [])

  useEffect(() => {
    fetchProducts()
  }, [debouncedSearch, category, page])

  async function fetchProducts() {
    setLoading(true)
    setError(null)
    try {
      const params = { page }
      if (debouncedSearch) params.search = debouncedSearch
      if (category)        params.category = category
      const { data } = await api.get('/products', { params })
      setProducts(data.data)
      setMeta(data.meta)
    } catch {
      setError('Erro ao carregar produtos. Tente novamente.')
    } finally {
      setLoading(false)
    }
  }

  function handleCategoryChange(e) {
    setCategory(e.target.value)
    setPage(1)
  }

  function handlePageChange(_, value) {
    setPage(value)
    window.scrollTo({ top: 0, behavior: 'smooth' })
  }

  async function handleDeleteConfirm() {
    setDeleteLoading(true)
    try {
      await api.delete(`/products/${deleteProduct.id}`)
      setProducts((prev) => prev.filter((p) => p.id !== deleteProduct.id))
      setDeleteProduct(null)
    } finally {
      setDeleteLoading(false)
    }
  }

  return (
    <Box>
      {/* Hero */}
      <Box
        sx={{
          textAlign: 'center',
          py: { xs: 6, md: 10 },
          px: 2,
          bgcolor: 'background.default',
          borderBottom: '1px solid',
          borderColor: 'divider',
        }}
      >
        <Typography variant="h3" mb={1.5}>
          Nossa Coleção
        </Typography>
        <Typography color="text.secondary" sx={{ maxWidth: 420, mx: 'auto' }}>
          Produtos selecionados com cuidado, para um estilo autêntico e consciente.
        </Typography>
      </Box>

      <Container maxWidth="xl" sx={{ py: 5 }}>
        {/* Filters + Admin actions */}
        <Grid container spacing={2} mb={2} alignItems="center">
          <Grid size={{ xs: 12, md: isAuthenticated ? 6 : 8 }}>
            <TextField
              fullWidth
              placeholder="Buscar produtos..."
              value={search}
              onChange={(e) => setSearch(e.target.value)}
              slotProps={{
                input: {
                  startAdornment: (
                    <InputAdornment position="start">
                      <SearchIcon color="action" />
                    </InputAdornment>
                  ),
                },
              }}
            />
          </Grid>
          <Grid size={{ xs: 12, md: isAuthenticated ? 3 : 4 }}>
            <FormControl fullWidth>
              <InputLabel>Categoria</InputLabel>
              <Select value={category} label="Categoria" onChange={handleCategoryChange}>
                <MenuItem value="">Todas as categorias</MenuItem>
                {categories.map((cat) => (
                  <MenuItem key={cat.id} value={cat.id}>
                    {cat.name}
                  </MenuItem>
                ))}
              </Select>
            </FormControl>
          </Grid>
          {isAuthenticated && (
            <Grid size={{ xs: 12, md: 3 }}>
              <Stack direction="row" spacing={1}>
                <Button
                  variant="contained"
                  startIcon={<AddIcon />}
                  onClick={() => setCreateOpen(true)}
                  fullWidth
                >
                  Novo Produto
                </Button>
                <Button
                  variant="outlined"
                  startIcon={<CategoryIcon />}
                  onClick={() => setCategoryOpen(true)}
                  fullWidth
                >
                  Categorias
                </Button>
              </Stack>
            </Grid>
          )}
        </Grid>

        <Divider sx={{ mb: 4 }} />

        {error && (
          <Alert
            severity="error"
            action={
              <Button color="inherit" size="small" onClick={fetchProducts}>
                Tentar novamente
              </Button>
            }
            sx={{ mb: 3 }}
          >
            {error}
          </Alert>
        )}

        <Grid container spacing={3}>
          {loading
            ? Array.from({ length: 12 }).map((_, i) => (
                <Grid key={i} size={{ xs: 12, sm: 6, md: 4, lg: 3 }}>
                  <Skeleton variant="rectangular" height={240} sx={{ borderRadius: 2 }} />
                  <Skeleton width="80%" sx={{ mt: 1 }} />
                  <Skeleton width="40%" />
                  <Skeleton width="50%" />
                </Grid>
              ))
            : products.map((product) => (
                <Grid key={product.id} size={{ xs: 12, sm: 6, md: 4, lg: 3 }}>
                  <ProductCard
                    id={product.id}
                    name={product.name}
                    price={product.price}
                    image_url={product.image_url}
                    category={product.category}
                    showActions={isAuthenticated}
                    onEdit={() => setEditProduct(product)}
                    onDelete={() => setDeleteProduct(product)}
                  />
                </Grid>
              ))}
        </Grid>

        {!loading && !error && products.length === 0 && (
          <Box textAlign="center" py={12}>
            <CheckroomIcon sx={{ fontSize: 64, color: 'text.secondary', mb: 2, opacity: 0.4 }} />
            <Typography color="text.secondary">
              Nenhum produto encontrado{debouncedSearch ? ` para "${debouncedSearch}"` : ''}
            </Typography>
          </Box>
        )}

        {!loading && meta.last_page > 1 && (
          <Box display="flex" justifyContent="center" mt={6}>
            <Pagination
              count={meta.last_page}
              page={page}
              onChange={handlePageChange}
              color="primary"
              size="large"
            />
          </Box>
        )}
      </Container>

      {/* Modais */}
      <ProductFormModal
        open={createOpen}
        onClose={() => setCreateOpen(false)}
        onSuccess={fetchProducts}
        product={null}
      />

      <ProductFormModal
        open={!!editProduct}
        onClose={() => setEditProduct(null)}
        onSuccess={fetchProducts}
        product={editProduct}
      />

      <DeleteConfirmDialog
        open={!!deleteProduct}
        onClose={() => setDeleteProduct(null)}
        onConfirm={handleDeleteConfirm}
        title="Excluir produto"
        description={`Tem certeza que deseja excluir "${deleteProduct?.name}"?`}
        loading={deleteLoading}
      />

      <CategoryManagerModal
        open={categoryOpen}
        onClose={() => setCategoryOpen(false)}
      />
    </Box>
  )
}
