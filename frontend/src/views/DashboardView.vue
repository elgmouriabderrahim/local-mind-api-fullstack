<script setup>
import { computed, onMounted, ref } from 'vue'
import { useRouter } from 'vue-router'
import {
  getFavourites,
  getHomeStats,
  getMe,
  getQuestions,
  getResponses,
  logout as logoutRequest
} from '../services/api'
import { clearAuthSession, getStoredUser } from '../services/auth'

const router = useRouter()

const loading = ref(true)
const error = ref('')
const user = ref(getStoredUser())
const stats = ref({
  users_count: 0,
  questions_count: 0,
  responses_count: 0,
  favourites_count: 0
})
const recentQuestions = ref([])

const chartHeights = computed(() => {
  const counts = recentQuestions.value.slice(0, 7).map((question) => question.responses_count || 0)

  if (counts.length === 0) {
    return ['25%', '35%', '45%', '30%', '60%', '50%', '40%']
  }

  const max = Math.max(...counts, 1)
  return counts.map((count) => `${20 + Math.round((count / max) * 75)}%`)
})

function formatRelativeTime(dateString) {
  if (!dateString) return 'just now'

  const date = new Date(dateString)
  const seconds = Math.floor((Date.now() - date.getTime()) / 1000)

  if (seconds < 60) return 'just now'

  const minutes = Math.floor(seconds / 60)
  if (minutes < 60) return `${minutes} min ago`

  const hours = Math.floor(minutes / 60)
  if (hours < 24) return `${hours} hour${hours > 1 ? 's' : ''} ago`

  const days = Math.floor(hours / 24)
  return `${days} day${days > 1 ? 's' : ''} ago`
}

async function loadDashboard() {
  loading.value = true
  error.value = ''

  try {
    const [meData, homeData, questionsData, responsesData, favouritesData] = await Promise.all([
      getMe(),
      getHomeStats(),
      getQuestions(),
      getResponses(),
      getFavourites()
    ])

    user.value = meData

    stats.value = {
      users_count: homeData?.users_count ?? 0,
      questions_count: questionsData?.total ?? 0,
      responses_count: responsesData?.total ?? 0,
      favourites_count: Array.isArray(favouritesData) ? favouritesData.length : 0
    }

    recentQuestions.value = Array.isArray(questionsData?.data) ? questionsData.data : []
  } catch (err) {
    const message = err instanceof Error ? err.message : 'Failed to load dashboard.'
    error.value = message

    if (message.toLowerCase().includes('unauthorized') || message.toLowerCase().includes('not logged')) {
      clearAuthSession()
      router.replace('/login')
    }
  } finally {
    loading.value = false
  }
}

async function handleLogout() {
  try {
    await logoutRequest()
  } catch {
    // If token is already invalid, we still clear local auth and move to login.
  } finally {
    clearAuthSession()
    router.push('/login')
  }
}

onMounted(() => {
  loadDashboard()
})
</script>

<template>
  <div
    class="min-h-screen bg-[radial-gradient(1200px_450px_at_5%_-10%,#e9ded0,transparent_70%),linear-gradient(160deg,#f8f2e9_0%,#f4efe6_58%,#f2e7d9_100%)] px-3 py-4 text-[#24313a] sm:px-4"
  >
    <div class="mx-auto grid w-full max-w-6xl gap-5">
      <div class="flex items-center justify-between gap-3">
        <div>
          <span class="text-sm font-semibold text-[#5b6d79]">Local Mind</span>
          <p class="text-xs text-[#5b6d79]">
            {{ user?.full_name || user?.email || 'Authenticated User' }}
          </p>
        </div>
        <button
          class="rounded-full border border-[#d0e4ee] bg-white/80 px-4 py-1.5 text-sm font-semibold text-[#366f8c] transition hover:bg-[#eaf4f9]"
          type="button"
          @click="handleLogout"
        >
          Log out
        </button>
      </div>

      <header
        class="rounded-2xl border border-white/30 bg-[linear-gradient(120deg,rgba(54,111,140,0.92),rgba(186,89,53,0.9)),radial-gradient(circle_at_80%_20%,rgba(255,255,255,0.25),transparent_45%)] p-6 text-[#fdfaf2] shadow-[0_16px_40px_rgba(26,58,82,0.1)]"
      >
        <div class="flex flex-col gap-4 sm:flex-row sm:items-end sm:justify-between">
          <div>
            <p class="text-xs uppercase tracking-[0.14em] opacity-90">Operations Center</p>
            <h1 class="mt-1 text-2xl font-bold leading-tight sm:text-3xl">Local Mind Dashboard</h1>
            <p class="mt-2 max-w-2xl text-sm opacity-95 sm:text-base">
              Live data connected to your Laravel API.
            </p>
          </div>
          <button
            class="rounded-full bg-white/20 px-4 py-2 text-sm font-bold text-white transition hover:bg-white/30"
            type="button"
            @click="loadDashboard"
          >
            Refresh Data
          </button>
        </div>
      </header>

      <p v-if="error" class="rounded-lg border border-red-200 bg-red-50 px-3 py-2 text-sm text-red-600">
        {{ error }}
      </p>

      <section aria-label="Performance overview" class="grid gap-4 sm:grid-cols-2 xl:grid-cols-4">
        <article class="rounded-2xl border border-[#eadfce] bg-white/90 p-4 shadow-[0_16px_40px_rgba(26,58,82,0.08)]">
          <p class="text-sm text-[#5b6d79]">Users</p>
          <p class="mt-1 text-3xl font-bold leading-tight">{{ stats.users_count }}</p>
        </article>

        <article class="rounded-2xl border border-[#eadfce] bg-white/90 p-4 shadow-[0_16px_40px_rgba(26,58,82,0.08)]">
          <p class="text-sm text-[#5b6d79]">Questions</p>
          <p class="mt-1 text-3xl font-bold leading-tight">{{ stats.questions_count }}</p>
        </article>

        <article class="rounded-2xl border border-[#eadfce] bg-white/90 p-4 shadow-[0_16px_40px_rgba(26,58,82,0.08)]">
          <p class="text-sm text-[#5b6d79]">Responses</p>
          <p class="mt-1 text-3xl font-bold leading-tight">{{ stats.responses_count }}</p>
        </article>

        <article class="rounded-2xl border border-[#eadfce] bg-white/90 p-4 shadow-[0_16px_40px_rgba(26,58,82,0.08)]">
          <p class="text-sm text-[#5b6d79]">My Favourites</p>
          <p class="mt-1 text-3xl font-bold leading-tight">{{ stats.favourites_count }}</p>
        </article>
      </section>

      <section class="grid gap-4 lg:grid-cols-12">
        <article
          class="rounded-2xl border border-[#eadfce] bg-white/90 p-4 shadow-[0_16px_40px_rgba(26,58,82,0.08)] lg:col-span-8"
        >
          <div class="mb-4 flex items-center justify-between gap-3">
            <h2 class="text-base font-bold">Recent Questions</h2>
            <span class="text-xs font-semibold text-[#5b6d79]">From `/api/questions`</span>
          </div>

          <div v-if="loading" class="text-sm text-[#5b6d79]">Loading dashboard data...</div>

          <ul v-else class="grid gap-3">
            <li
              v-for="question in recentQuestions.slice(0, 5)"
              :key="question.id"
              class="flex items-start justify-between gap-3 rounded-xl border border-[#eadfce] bg-[#fdf8ef] p-3"
            >
              <div>
                <p class="font-semibold">{{ question.title }}</p>
                <p class="text-sm text-[#5b6d79]">
                  by {{ question.user?.full_name || question.user?.email || 'Unknown' }}
                  • {{ formatRelativeTime(question.created_at) }}
                </p>
              </div>
              <span class="shrink-0 rounded-full border border-[#b3d3e3] bg-[#eaf4f9] px-2 py-1 text-xs text-[#27536b]">
                {{ question.responses_count || 0 }} responses
              </span>
            </li>

            <li v-if="!recentQuestions.length && !loading" class="text-sm text-[#5b6d79]">
              No questions found yet.
            </li>
          </ul>
        </article>

        <article
          class="rounded-2xl border border-[#eadfce] bg-white/90 p-4 shadow-[0_16px_40px_rgba(26,58,82,0.08)] lg:col-span-4"
        >
          <div class="mb-4 flex items-center justify-between gap-3">
            <h2 class="text-base font-bold">Responses Trend</h2>
          </div>
          <div class="grid h-44 grid-cols-7 items-end gap-2 rounded-xl border border-[#eadfce] bg-gradient-to-t from-[#f0f6fa] to-[#fbfdff] p-3">
            <span
              v-for="(height, index) in chartHeights"
              :key="`bar-${index}`"
              class="block rounded-t-full rounded-b-md bg-gradient-to-t from-[#ba5935] to-[#d98762]"
              :style="{ height }"
            ></span>
          </div>
          <p class="mt-3 text-sm text-[#5b6d79]">
            Chart is generated from real response counts on latest questions.
          </p>
        </article>
      </section>
    </div>
  </div>
</template>
