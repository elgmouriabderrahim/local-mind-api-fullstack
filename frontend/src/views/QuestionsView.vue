<script setup>
import { computed, onMounted, ref } from 'vue'
import { RouterLink, useRouter } from 'vue-router'
import AppShell from '../components/AppShell.vue'
import { deleteQuestion, getFavourites, getQuestions, toggleFavourite } from '../services/api'
import { clearAuthSession, getStoredUser } from '../services/auth'

const router = useRouter()
const loading = ref(true)
const error = ref('')
const questions = ref([])
const favouriteQuestionIds = ref(new Set())
const currentUser = ref(getStoredUser())

const hasQuestions = computed(() => questions.value.length > 0)

function isMine(question) {
  return question?.user_id === currentUser.value?.id
}

function isFavourited(questionId) {
  return favouriteQuestionIds.value.has(questionId)
}

function mapQuestions(payload) {
  return Array.isArray(payload?.data) ? payload.data : []
}

function mapFavourites(payload) {
  if (!Array.isArray(payload)) return new Set()
  return new Set(payload.map((favourite) => favourite.question_id))
}

async function loadQuestions() {
  loading.value = true
  error.value = ''

  try {
    const [questionsPayload, favouritesPayload] = await Promise.all([getQuestions(), getFavourites()])
    questions.value = mapQuestions(questionsPayload)
    favouriteQuestionIds.value = mapFavourites(favouritesPayload)
  } catch (err) {
    if (err?.status === 401) {
      clearAuthSession()
      router.replace('/login')
      return
    }
    error.value = err instanceof Error ? err.message : 'Failed to load questions.'
  } finally {
    loading.value = false
  }
}

async function handleToggleFavourite(questionId) {
  try {
    const result = await toggleFavourite(questionId)
    if (result?.is_favourited) {
      favouriteQuestionIds.value.add(questionId)
    } else {
      favouriteQuestionIds.value.delete(questionId)
    }
  } catch (err) {
    error.value = err instanceof Error ? err.message : 'Failed to toggle favourite.'
  }
}

async function handleDelete(questionId) {
  if (!window.confirm('Delete this question?')) return

  try {
    await deleteQuestion(questionId)
    questions.value = questions.value.filter((question) => question.id !== questionId)
    favouriteQuestionIds.value.delete(questionId)
  } catch (err) {
    error.value = err instanceof Error ? err.message : 'Failed to delete question.'
  }
}

onMounted(loadQuestions)
</script>

<template>
  <AppShell>
    <section class="rounded-2xl border border-white/30 bg-[linear-gradient(120deg,rgba(54,111,140,0.92),rgba(186,89,53,0.9))] p-6 text-[#fdfaf2] shadow-[0_16px_40px_rgba(26,58,82,0.1)]">
      <div class="flex flex-wrap items-center justify-between gap-3">
        <div>
          <p class="text-xs uppercase tracking-[0.14em] opacity-90">Community Feed</p>
          <h1 class="mt-1 text-2xl font-bold sm:text-3xl">Questions</h1>
        </div>
        <RouterLink
          to="/questions/new"
          class="rounded-full bg-white/20 px-4 py-2 text-sm font-bold text-white transition hover:bg-white/30"
        >
          New Question
        </RouterLink>
      </div>
    </section>

    <p v-if="error" class="mt-4 rounded-lg border border-red-200 bg-red-50 px-3 py-2 text-sm text-red-600">{{ error }}</p>

    <section class="mt-4 rounded-2xl border border-[#eadfce] bg-white/90 p-4 shadow-[0_16px_40px_rgba(26,58,82,0.08)]">
      <p v-if="loading" class="text-sm text-[#5b6d79]">Loading questions...</p>
      <p v-else-if="!hasQuestions" class="text-sm text-[#5b6d79]">No questions yet. Create the first one.</p>

      <ul v-else class="grid gap-3">
        <li
          v-for="question in questions"
          :key="question.id"
          class="rounded-xl border border-[#eadfce] bg-[#fdf8ef] p-4"
        >
          <div class="flex flex-wrap items-start justify-between gap-3">
            <div>
              <RouterLink :to="`/questions/${question.id}`" class="text-lg font-bold text-[#24313a] hover:underline">
                {{ question.title }}
              </RouterLink>
              <p class="mt-1 text-sm text-[#5b6d79]">
                {{ question.user?.full_name || question.user?.email || 'Unknown user' }}
                • {{ question.responses_count || 0 }} responses
              </p>
            </div>

            <div class="flex flex-wrap items-center gap-2">
              <button
                type="button"
                class="rounded-full border border-[#d0e4ee] bg-white px-3 py-1 text-xs font-semibold text-[#366f8c] transition hover:bg-[#eaf4f9]"
                @click="handleToggleFavourite(question.id)"
              >
                {{ isFavourited(question.id) ? 'Unfavourite' : 'Favourite' }}
              </button>

              <RouterLink
                :to="`/questions/${question.id}`"
                class="rounded-full border border-[#d0e4ee] bg-white px-3 py-1 text-xs font-semibold text-[#366f8c] transition hover:bg-[#eaf4f9]"
              >
                Open
              </RouterLink>

              <RouterLink
                v-if="isMine(question)"
                :to="`/questions/${question.id}/edit`"
                class="rounded-full border border-[#d0e4ee] bg-white px-3 py-1 text-xs font-semibold text-[#366f8c] transition hover:bg-[#eaf4f9]"
              >
                Edit
              </RouterLink>

              <button
                v-if="isMine(question)"
                type="button"
                class="rounded-full border border-red-200 bg-white px-3 py-1 text-xs font-semibold text-red-600 transition hover:bg-red-50"
                @click="handleDelete(question.id)"
              >
                Delete
              </button>
            </div>
          </div>

          <p class="mt-3 text-sm text-[#33424c]">{{ question.content }}</p>
        </li>
      </ul>
    </section>
  </AppShell>
</template>
