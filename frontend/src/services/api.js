import { getAuthToken } from './auth'

const API_BASE_URL = import.meta.env.VITE_API_BASE_URL || 'http://localhost:8000/api'

function normalizeErrorMessage(payload, fallback) {
  if (payload?.message) return payload.message

  if (payload?.errors && typeof payload.errors === 'object') {
    const firstKey = Object.keys(payload.errors)[0]
    const first = payload.errors[firstKey]

    if (Array.isArray(first) && first[0]) {
      return first[0]
    }
  }

  return fallback
}

async function request(path, { method = 'GET', body, auth = false } = {}) {
  const headers = {
    Accept: 'application/json'
  }

  if (body !== undefined) {
    headers['Content-Type'] = 'application/json'
  }

  if (auth) {
    const token = getAuthToken()
    if (!token) {
      throw new Error('You are not logged in.')
    }
    headers.Authorization = `Bearer ${token}`
  }

  const response = await fetch(`${API_BASE_URL}${path}`, {
    method,
    headers,
    body: body !== undefined ? JSON.stringify(body) : undefined
  })

  let payload = null
  try {
    payload = await response.json()
  } catch {
    payload = null
  }

  if (!response.ok) {
    const fallback = response.status === 401
      ? 'Unauthorized. Please log in again.'
      : 'Request failed. Please try again.'
    const error = new Error(normalizeErrorMessage(payload, fallback))
    error.status = response.status
    throw error
  }

  return payload
}

export function login(payload) {
  return request('/login', {
    method: 'POST',
    body: payload
  })
}

export function register(payload) {
  return request('/register', {
    method: 'POST',
    body: payload
  })
}

export function logout() {
  return request('/logout', {
    method: 'POST',
    auth: true
  })
}

export function getMe() {
  return request('/me', { auth: true })
}

export function getHomeStats() {
  return request('/home')
}

export function getQuestions() {
  return request('/questions', { auth: true })
}

export function getQuestion(questionId) {
  return request(`/questions/${questionId}`, { auth: true })
}

export function createQuestion(payload) {
  return request('/questions', {
    method: 'POST',
    auth: true,
    body: payload
  })
}

export function updateQuestion(questionId, payload) {
  return request(`/questions/${questionId}`, {
    method: 'PUT',
    auth: true,
    body: payload
  })
}

export function deleteQuestion(questionId) {
  return request(`/questions/${questionId}`, {
    method: 'DELETE',
    auth: true
  })
}

export function getResponses() {
  return request('/responses', { auth: true })
}

export function createResponse(questionId, payload) {
  return request(`/questions/${questionId}/responses`, {
    method: 'POST',
    auth: true,
    body: payload
  })
}

export function updateResponse(responseId, payload) {
  return request(`/responses/${responseId}`, {
    method: 'PUT',
    auth: true,
    body: payload
  })
}

export function deleteResponse(responseId) {
  return request(`/responses/${responseId}`, {
    method: 'DELETE',
    auth: true
  })
}

export function getFavourites() {
  return request('/favourites', { auth: true })
}

export function toggleFavourite(questionId) {
  return request(`/questions/${questionId}/favourite`, {
    method: 'POST',
    auth: true
  })
}
