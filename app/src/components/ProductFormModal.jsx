import { useEffect, useState } from 'react'
import Alert from '@mui/material/Alert'
import Button from '@mui/material/Button'
import CircularProgress from '@mui/material/CircularProgress'
import Dialog from '@mui/material/Dialog'
import DialogActions from '@mui/material/DialogActions'
import DialogContent from '@mui/material/DialogContent'
import DialogTitle from '@mui/material/DialogTitle'
import FormControl from '@mui/material/FormControl'
import FormHelperText from '@mui/material/FormHelperText'
import InputLabel from '@mui/material/InputLabel'
import MenuItem from '@mui/material/MenuItem'
import Select from '@mui/material/Select'
import Stack from '@mui/material/Stack'
import TextField from '@mui/material/TextField'
import api from '../services/api'

const EMPTY_FORM = { name: '', description: '', price: '', category_id: '', image_url: '' }

export default function ProductFormModal({ open, onClose, onSuccess, product }) {
  const [form, setForm] = useState(EMPTY_FORM)
  const [categories, setCategories] = useState([])
  const [loading, setLoading] = useState(false)
  const [errors, setErrors] = useState({})
  const [generalError, setGeneralError] = useState('')

  const isEdit = product !== null && product !== undefined

  useEffect(() => {
    if (!open) return
    setErrors({})
    setGeneralError('')
    setForm(
      isEdit
        ? {
            name: product.name ?? '',
            description: product.description ?? '',
            price: product.price ?? '',
            category_id: product.category?.id ?? product.category_id ?? '',
            image_url: product.image_url ?? '',
          }
        : EMPTY_FORM
    )
    api.get('/categories').then(({ data }) => setCategories(data.data)).catch(() => {})
  }, [open])

  function handleChange(e) {
    const { name, value } = e.target
    setForm((prev) => ({ ...prev, [name]: value }))
    setErrors((prev) => ({ ...prev, [name]: undefined }))
  }

  async function handleSubmit() {
    const fieldErrors = {}
    if (!form.name.trim()) fieldErrors.name = 'Nome é obrigatório'
    if (!form.price) fieldErrors.price = 'Preço é obrigatório'
    if (!form.category_id) fieldErrors.category_id = 'Categoria é obrigatória'
    if (Object.keys(fieldErrors).length) {
      setErrors(fieldErrors)
      return
    }

    setLoading(true)
    setGeneralError('')
    try {
      const payload = {
        name: form.name,
        description: form.description,
        price: parseFloat(form.price),
        category_id: form.category_id,
        image_url: form.image_url,
      }
      if (isEdit) {
        await api.put(`/products/${product.id}`, payload)
      } else {
        await api.post('/products', payload)
      }
      onSuccess()
      onClose()
    } catch (err) {
      if (err.response?.status === 422) {
        setErrors(err.response.data.errors ?? {})
      } else {
        setGeneralError('Erro ao salvar produto. Tente novamente.')
      }
    } finally {
      setLoading(false)
    }
  }

  return (
    <Dialog open={open} onClose={loading ? undefined : onClose} maxWidth="sm" fullWidth>
      <DialogTitle>{isEdit ? 'Editar Produto' : 'Criar Produto'}</DialogTitle>
      <DialogContent>
        <Stack spacing={2} sx={{ mt: 1 }}>
          <TextField
            label="Nome *"
            name="name"
            value={form.name}
            onChange={handleChange}
            error={!!errors.name}
            helperText={errors.name}
            fullWidth
          />
          <TextField
            label="Descrição"
            name="description"
            value={form.description}
            onChange={handleChange}
            multiline
            rows={3}
            fullWidth
          />
          <TextField
            label="Preço *"
            name="price"
            type="number"
            value={form.price}
            onChange={handleChange}
            error={!!errors.price}
            helperText={errors.price}
            inputProps={{ min: 0, step: '0.01' }}
            fullWidth
          />
          <FormControl fullWidth error={!!errors.category_id}>
            <InputLabel>Categoria *</InputLabel>
            <Select
              name="category_id"
              value={form.category_id}
              label="Categoria *"
              onChange={handleChange}
            >
              {categories.map((cat) => (
                <MenuItem key={cat.id} value={cat.id}>
                  {cat.name}
                </MenuItem>
              ))}
            </Select>
            {errors.category_id && <FormHelperText>{errors.category_id}</FormHelperText>}
          </FormControl>
          <TextField
            label="URL da Imagem"
            name="image_url"
            value={form.image_url}
            onChange={handleChange}
            fullWidth
          />
          {generalError && <Alert severity="error">{generalError}</Alert>}
        </Stack>
      </DialogContent>
      <DialogActions sx={{ px: 3, pb: 2 }}>
        <Button onClick={onClose} disabled={loading}>
          Cancelar
        </Button>
        <Button
          variant="contained"
          onClick={handleSubmit}
          disabled={loading}
          startIcon={loading ? <CircularProgress size={16} color="inherit" /> : null}
        >
          Salvar
        </Button>
      </DialogActions>
    </Dialog>
  )
}
