import { useEffect, useState } from 'react'
import Box from '@mui/material/Box'
import Button from '@mui/material/Button'
import CircularProgress from '@mui/material/CircularProgress'
import Dialog from '@mui/material/Dialog'
import DialogContent from '@mui/material/DialogContent'
import DialogTitle from '@mui/material/DialogTitle'
import Divider from '@mui/material/Divider'
import List from '@mui/material/List'
import ListItem from '@mui/material/ListItem'
import ListItemText from '@mui/material/ListItemText'
import Stack from '@mui/material/Stack'
import TextField from '@mui/material/TextField'
import Typography from '@mui/material/Typography'
import api from '../services/api'

export default function CategoryManagerModal({ open, onClose }) {
  const [categories, setCategories] = useState([])
  const [newName, setNewName] = useState('')
  const [addLoading, setAddLoading] = useState(false)

  useEffect(() => {
    if (!open) return
    setNewName('')
    api.get('/categories').then(({ data }) => setCategories(data.data)).catch(() => {})
  }, [open])

  async function handleAdd() {
    if (!newName.trim()) return
    setAddLoading(true)
    try {
      const { data } = await api.post('/categories', { name: newName.trim() })
      setCategories((prev) => [...prev, data.data])
      setNewName('')
    } finally {
      setAddLoading(false)
    }
  }

  return (
    <Dialog open={open} onClose={onClose} maxWidth="xs" fullWidth>
      <DialogTitle>Gerenciar Categorias</DialogTitle>
      <DialogContent sx={{ px: 2 }}>
        {categories.length === 0 ? (
          <Typography color="text.secondary" fontSize={14} sx={{ py: 1 }}>
            Nenhuma categoria cadastrada.
          </Typography>
        ) : (
          <List dense disablePadding>
            {categories.map((cat) => (
              <ListItem key={cat.id} disableGutters>
                <ListItemText primary={cat.name} />
              </ListItem>
            ))}
          </List>
        )}

        <Divider sx={{ my: 2 }} />

        <Stack direction="row" spacing={1} alignItems="center">
          <TextField
            size="small"
            label="Nova categoria"
            value={newName}
            onChange={(e) => setNewName(e.target.value)}
            onKeyDown={(e) => e.key === 'Enter' && handleAdd()}
            fullWidth
          />
          <Box sx={{ flexShrink: 0 }}>
            <Button
              variant="contained"
              onClick={handleAdd}
              disabled={addLoading || !newName.trim()}
              startIcon={addLoading ? <CircularProgress size={14} color="inherit" /> : null}
            >
              Adicionar
            </Button>
          </Box>
        </Stack>
      </DialogContent>
    </Dialog>
  )
}
