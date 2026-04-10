import { createContext, useContext, useEffect, useState } from 'react'
import api from '../services/api'

const AuthContext = createContext(null)

function loadUser() {
  try {
    const raw = localStorage.getItem('user')
    return raw ? JSON.parse(raw) : null
  } catch {
    return null
  }
}

export function AuthProvider({ children }) {
  const [isAuthenticated, setIsAuthenticated] = useState(
    () => !!localStorage.getItem('token')
  )
  const [user, setUser] = useState(loadUser)

  // Se tem token mas não tem user (sessão antiga), busca da API
  useEffect(() => {
    if (isAuthenticated && !user) {
      api.get('/user').then(({ data }) => {
        const userData = data.data ?? data
        localStorage.setItem('user', JSON.stringify(userData))
        setUser(userData)
      }).catch(() => {})
    }
  }, [isAuthenticated])

  function login(token, userData) {
    localStorage.setItem('token', token)
    if (userData) localStorage.setItem('user', JSON.stringify(userData))
    setIsAuthenticated(true)
    setUser(userData ?? null)
  }

  function logout() {
    localStorage.removeItem('token')
    localStorage.removeItem('user')
    setIsAuthenticated(false)
    setUser(null)
  }

  return (
    <AuthContext.Provider value={{ isAuthenticated, user, login, logout }}>
      {children}
    </AuthContext.Provider>
  )
}

export const useAuth = () => useContext(AuthContext)
