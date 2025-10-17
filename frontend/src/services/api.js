const API_BASE_URL = 'http://127.0.0.1:8000/api'

class ApiService {
  constructor() {
    this.baseURL = API_BASE_URL
  }

  async request(endpoint, options = {}) {
    const url = `${this.baseURL}${endpoint}`
    const config = {
      headers: {
        'Content-Type': 'application/json',
        'Accept': 'application/json',
        ...options.headers,
      },
      ...options,
    }

    const token = localStorage.getItem('auth_token')
    if (token) {
      config.headers.Authorization = `Bearer ${token}`
    }

    try {
      const response = await fetch(url, config)
      const data = await response.json()

      if (!response.ok) {
        throw new Error(data.message || 'Une erreur est survenue')
      }

      return data
    } catch (error) {
      console.error('Erreur API:', error)
      throw error
    }
  }

//coneexion
  async login(email, password) {
    return this.request('/auth/login', {
      method: 'POST',
      body: JSON.stringify({ email, password }),
    })
  }

  //inscription
  async register(name, email, password) {
    return this.request('/auth/register', {
      method: 'POST',
      body: JSON.stringify({ name, email, password }),
    })
  }

  setToken(token) {
    localStorage.setItem('auth_token', token)
  }

  getToken() {
    return localStorage.getItem('auth_token')
  }

  removeToken() {
    localStorage.removeItem('auth_token')
  }

  isAuthenticated() {
    return !!this.getToken()
  }
}

export default new ApiService()
