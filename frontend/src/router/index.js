import { createRouter, createWebHistory } from 'vue-router'
import LoginView from '../views/LoginView.vue'
import RegisterView from '../views/RegisterView.vue'
import DashboardView from '../views/DashboardView.vue'
import QuestionsView from '../views/QuestionsView.vue'
import QuestionDetailView from '../views/QuestionDetailView.vue'
import QuestionFormView from '../views/QuestionFormView.vue'
import ResponsesView from '../views/ResponsesView.vue'
import FavouritesView from '../views/FavouritesView.vue'
import ProfileView from '../views/ProfileView.vue'
import NotFoundView from '../views/NotFoundView.vue'
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
  },
  {
    path: '/questions',
    component: QuestionsView,
    meta: { requiresAuth: true }
  },
  {
    path: '/questions/new',
    component: QuestionFormView,
    meta: { requiresAuth: true }
  },
  {
    path: '/questions/:id',
    component: QuestionDetailView,
    meta: { requiresAuth: true }
  },
  {
    path: '/questions/:id/edit',
    component: QuestionFormView,
    meta: { requiresAuth: true }
  },
  {
    path: '/responses',
    component: ResponsesView,
    meta: { requiresAuth: true }
  },
  {
    path: '/favourites',
    component: FavouritesView,
    meta: { requiresAuth: true }
  },
  {
    path: '/profile',
    component: ProfileView,
    meta: { requiresAuth: true }
  },
  {
    path: '/:pathMatch(.*)*',
    component: NotFoundView
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
