import Card from '@mui/material/Card'
import CardActionArea from '@mui/material/CardActionArea'
import CardContent from '@mui/material/CardContent'
import CardMedia from '@mui/material/CardMedia'
import Chip from '@mui/material/Chip'
import Typography from '@mui/material/Typography'
import CheckroomIcon from '@mui/icons-material/Checkroom'
import { Link } from 'react-router-dom'

const formatBRL = (value) =>
  new Intl.NumberFormat('pt-BR', { style: 'currency', currency: 'BRL' }).format(value)

export default function ProductCard({ id, name, price, image_url, category }) {
  return (
    <Card
      sx={{
        height: '100%',
        display: 'flex',
        flexDirection: 'column',
        transition: 'border-color 0.2s, box-shadow 0.2s',
        '&:hover': { borderColor: 'secondary.main', boxShadow: '0 4px 20px rgba(0,0,0,0.08)' },
      }}
    >
      <CardActionArea
        component={Link}
        to={`/products/${id}`}
        sx={{ flexGrow: 1, display: 'flex', flexDirection: 'column', alignItems: 'stretch' }}
      >
        <CardMedia
          sx={{
            height: 240,
            display: 'flex',
            alignItems: 'center',
            justifyContent: 'center',
            bgcolor: '#F0EAE3',
            overflow: 'hidden',
          }}
        >
          {image_url ? (
            <img
              src={image_url}
              alt={name}
              style={{ width: '100%', height: '100%', objectFit: 'cover' }}
            />
          ) : (
            <CheckroomIcon sx={{ fontSize: 80, color: 'text.secondary', opacity: 0.4 }} />
          )}
        </CardMedia>
        <CardContent sx={{ flexGrow: 1 }}>
          <Typography
            variant="subtitle1"
            fontWeight={600}
            sx={{
              display: '-webkit-box',
              WebkitLineClamp: 2,
              WebkitBoxOrient: 'vertical',
              overflow: 'hidden',
              mb: 1,
              color: 'text.primary',
            }}
          >
            {name}
          </Typography>
          <Chip
            label={category?.name}
            size="small"
            variant="outlined"
            sx={{ mb: 1.5, borderColor: 'divider', color: 'text.secondary', fontSize: '0.7rem' }}
          />
          <Typography variant="subtitle1" color="secondary.main" fontWeight={700}>
            {formatBRL(price)}
          </Typography>
        </CardContent>
      </CardActionArea>
    </Card>
  )
}
