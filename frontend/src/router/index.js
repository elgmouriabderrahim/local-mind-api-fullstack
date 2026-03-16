import { createRouter, createWebHistory } from 'vue-router'
import LoginView from '../views/LoginView.vue'
import RegisterView from '../views/RegisterView.vue'
import DashboardView from '../views/DashboardView.vue'
import { getAuthToken } from '../services/auth'

const routes = [
  {
    path: '/',
    redirect: '/login'
  },
  {
    path: '/login',
    component: LoginView,
    meta: { guestOnly: true }
  },
  {
    path: '/register',
    component: RegisterView,
    meta: { guestOnly: true }
  },
  {
    path: '/dashboard',
    component: DashboardView,
    meta: { requiresAuth: true }
  }
]

const router = createRouter({
  history: createWebHistory(),
  routes
})

router.beforeEach((to) => {
  const isLoggedIn = Boolean(getAuthToken())

  if (to.meta.requiresAuth && !isLoggedIn) {
    return '/login'
  }

  if (to.meta.guestOnly && isLoggedIn) {
    return '/dashboard'
  }

  return true
})

export default router
