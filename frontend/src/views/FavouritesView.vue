<script setup>
import { computed, onMounted, ref } from 'vue'
import { RouterLink, useRouter } from 'vue-router'
import AppShell from '../components/AppShell.vue'
import { getFavourites, toggleFavourite } from '../services/api'
import { clearAuthSession } from '../services/auth'

const router = useRouter()

const loading = ref(true)
const error = ref('')
const favourites = ref([])

const hasFavourites = computed(() => favourites.value.length > 0)

async function loadFavourites() {
  loading.value = true
  error.value = ''

  try {
    const payload = await getFavourites()
    favourites.value = Array.isArray(payload) ? payload : []
  } catch (err) {
    if (err?.status === 401) {
      clearAuthSession()
      router.replace('/login')
      return
    }
    error.value = err instanceof Error ? err.message : 'Failed to load favourites.'
  } finally {
    loading.value = false
  }
}

async function handleUnfavourite(questionId) {
  try {
    await toggleFavourite(questionId)
    favourites.value = favourites.value.filter((item) => item.question_id !== questionId)
  } catch (err) {
    error.value = err instanceof Error ? err.message : 'Failed to update favourite.'
  }
}

onMounted(loadFavourites)
</script>

<template>
  <AppShell>
    <section class="rounded-2xl border border-white/30 bg-[linear-gradient(120deg,rgba(54,111,140,0.92),rgba(186,89,53,0.9))] p-6 text-[#fdfaf2] shadow-[0_16px_40px_rgba(26,58,82,0.1)]">
      <p class="text-xs uppercase tracking-[0.14em] opacity-90">Saved Content</p>
      <h1 class="mt-1 text-2xl font-bold sm:text-3xl">Favourites</h1>
    </section>

    <p v-if="error" class="mt-4 rounded-lg border border-red-200 bg-red-50 px-3 py-2 text-sm text-red-600">{{ error }}</p>

    <section class="mt-4 rounded-2xl border border-[#eadfce] bg-white/90 p-4 shadow-[0_16px_40px_rgba(26,58,82,0.08)]">
      <p v-if="loading" class="text-sm text-[#5b6d79]">Loading favourites...</p>
      <p v-else-if="!hasFavourites" class="text-sm text-[#5b6d79]">No favourites yet.</p>

      <ul v-else class="grid gap-3">
        <li
          v-for="favourite in favourites"
          :key="favourite.id"
          class="rounded-xl border border-[#eadfce] bg-[#fdf8ef] p-4"
        >
          <div class="flex flex-wrap items-start justify-between gap-3">
            <div>
              <RouterLink
                :to="`/questions/${favourite.question_id}`"
                class="text-lg font-bold text-[#24313a] hover:underline"
              >
                {{ favourite.question?.title || `Question #${favourite.question_id}` }}
              </RouterLink>
              <p class="mt-1 text-sm text-[#5b6d79]">
                {{ favourite.question?.user?.full_name || favourite.question?.user?.email || 'Unknown user' }}
              </p>
            </div>
            <button
              type="button"
              class="rounded-full border border-red-200 bg-white px-3 py-1 text-xs font-semibold text-red-600 transition hover:bg-red-50"
              @click="handleUnfavourite(favourite.question_id)"
            >
              Remove
            </button>
          </div>
        </li>
      </ul>
    </section>
  </AppShell>
</template>
