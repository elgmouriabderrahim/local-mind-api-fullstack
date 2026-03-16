<script setup>
import { computed, onMounted, ref } from 'vue'
import { RouterLink, useRoute, useRouter } from 'vue-router'
import AppShell from '../components/AppShell.vue'
import {
  createResponse,
  deleteQuestion,
  deleteResponse,
  getFavourites,
  getQuestion,
  toggleFavourite,
  updateResponse
} from '../services/api'
import { clearAuthSession, getStoredUser } from '../services/auth'

const route = useRoute()
const router = useRouter()
const currentUser = ref(getStoredUser())

const loading = ref(true)
const submittingResponse = ref(false)
const error = ref('')
const question = ref(null)
const responseContent = ref('')
const editingResponseId = ref(null)
const editingContent = ref('')
const isFavourited = ref(false)

const canManageQuestion = computed(() => {
  if (!question.value || !currentUser.value) return false
  return question.value.user_id === currentUser.value.id || currentUser.value.role === 'admin'
})

function canManageResponse(response) {
  if (!currentUser.value) return false
  return response.user_id === currentUser.value.id || currentUser.value.role === 'admin'
}

async function loadQuestion() {
  loading.value = true
  error.value = ''

  try {
    const [questionPayload, favouritesPayload] = await Promise.all([
      getQuestion(route.params.id),
      getFavourites()
    ])

    question.value = questionPayload
    if (Array.isArray(favouritesPayload)) {
      isFavourited.value = favouritesPayload.some((item) => item.question_id === questionPayload?.id)
    }
  } catch (err) {
    if (err?.status === 401) {
      clearAuthSession()
      router.replace('/login')
      return
    }
    error.value = err instanceof Error ? err.message : 'Failed to load question details.'
  } finally {
    loading.value = false
  }
}

async function handleDeleteQuestion() {
  if (!question.value || !window.confirm('Delete this question?')) return

  try {
    await deleteQuestion(question.value.id)
    await router.push('/questions')
  } catch (err) {
    error.value = err instanceof Error ? err.message : 'Failed to delete question.'
  }
}

async function handleCreateResponse() {
  if (!question.value || !responseContent.value.trim()) return

  submittingResponse.value = true
  error.value = ''

  try {
    const result = await createResponse(question.value.id, {
      content: responseContent.value.trim()
    })

    const created = result?.data
    if (created) {
      question.value.responses = [created, ...(question.value.responses || [])]
    }
    responseContent.value = ''
  } catch (err) {
    error.value = err instanceof Error ? err.message : 'Failed to post response.'
  } finally {
    submittingResponse.value = false
  }
}

function startEditResponse(response) {
  editingResponseId.value = response.id
  editingContent.value = response.content
}

function cancelEditResponse() {
  editingResponseId.value = null
  editingContent.value = ''
}

async function saveEditResponse(response) {
  if (!editingContent.value.trim()) return

  try {
    const result = await updateResponse(response.id, {
      content: editingContent.value.trim()
    })

    const updated = result?.data
    if (updated && question.value?.responses) {
      question.value.responses = question.value.responses.map((item) => {
        if (item.id !== response.id) return item
        return { ...item, ...updated }
      })
    }

    cancelEditResponse()
  } catch (err) {
    error.value = err instanceof Error ? err.message : 'Failed to update response.'
  }
}

async function handleDeleteResponse(responseId) {
  if (!window.confirm('Delete this response?')) return

  try {
    await deleteResponse(responseId)
    if (question.value?.responses) {
      question.value.responses = question.value.responses.filter((response) => response.id !== responseId)
    }
  } catch (err) {
    error.value = err instanceof Error ? err.message : 'Failed to delete response.'
  }
}

async function handleToggleFavourite() {
  if (!question.value) return

  try {
    const result = await toggleFavourite(question.value.id)
    isFavourited.value = Boolean(result?.is_favourited)
  } catch (err) {
    error.value = err instanceof Error ? err.message : 'Failed to toggle favourite.'
  }
}

onMounted(loadQuestion)
</script>

<template>
  <AppShell>
    <section class="rounded-2xl border border-[#eadfce] bg-white/90 p-4 shadow-[0_16px_40px_rgba(26,58,82,0.08)] sm:p-6">
      <p v-if="loading" class="text-sm text-[#5b6d79]">Loading question details...</p>
      <p v-else-if="!question" class="text-sm text-[#5b6d79]">Question not found.</p>

      <template v-else>
        <div class="flex flex-wrap items-start justify-between gap-3">
          <div>
            <h1 class="text-2xl font-bold text-[#24313a]">{{ question.title }}</h1>
            <p class="mt-1 text-sm text-[#5b6d79]">
              by {{ question.user?.full_name || question.user?.email || 'Unknown user' }}
            </p>
            <p v-if="question.location_name" class="mt-1 text-sm text-[#5b6d79]">Location: {{ question.location_name }}</p>
          </div>

          <div class="flex flex-wrap items-center gap-2">
            <button
              type="button"
              class="rounded-full border border-[#d0e4ee] bg-white px-3 py-1 text-xs font-semibold text-[#366f8c] transition hover:bg-[#eaf4f9]"
              @click="handleToggleFavourite"
            >
              {{ isFavourited ? 'Unfavourite' : 'Favourite' }}
            </button>

            <RouterLink
              v-if="canManageQuestion"
              :to="`/questions/${question.id}/edit`"
              class="rounded-full border border-[#d0e4ee] bg-white px-3 py-1 text-xs font-semibold text-[#366f8c] transition hover:bg-[#eaf4f9]"
            >
              Edit
            </RouterLink>

            <button
              v-if="canManageQuestion"
              type="button"
              class="rounded-full border border-red-200 bg-white px-3 py-1 text-xs font-semibold text-red-600 transition hover:bg-red-50"
              @click="handleDeleteQuestion"
            >
              Delete
            </button>
          </div>
        </div>

        <p class="mt-4 whitespace-pre-wrap text-sm leading-relaxed text-[#33424c]">{{ question.content }}</p>
      </template>
    </section>

    <p v-if="error" class="mt-4 rounded-lg border border-red-200 bg-red-50 px-3 py-2 text-sm text-red-600">{{ error }}</p>

    <section class="mt-4 rounded-2xl border border-[#eadfce] bg-white/90 p-4 shadow-[0_16px_40px_rgba(26,58,82,0.08)] sm:p-6">
      <h2 class="text-lg font-bold">Responses</h2>

      <form class="mt-3 grid gap-3" @submit.prevent="handleCreateResponse">
        <textarea
          v-model="responseContent"
          rows="4"
          placeholder="Write your response..."
          class="w-full rounded-lg border border-[#d8cdbf] bg-white px-3 py-2 text-sm outline-none transition focus:border-[#29a9c5]"
        ></textarea>
        <div class="flex justify-end">
          <button
            type="submit"
            :disabled="submittingResponse"
            class="rounded-full bg-[#366f8c] px-4 py-2 text-sm font-bold text-white transition hover:bg-[#2f627d] disabled:cursor-not-allowed disabled:opacity-70"
          >
            {{ submittingResponse ? 'Posting...' : 'Post Response' }}
          </button>
        </div>
      </form>

      <ul class="mt-4 grid gap-3">
        <li
          v-for="response in question?.responses || []"
          :key="response.id"
          class="rounded-xl border border-[#eadfce] bg-[#fdf8ef] p-3"
        >
          <div class="flex flex-wrap items-center justify-between gap-2">
            <p class="text-xs font-semibold text-[#5b6d79]">
              {{ response.user?.full_name || response.user?.email || 'Unknown user' }}
            </p>

            <div v-if="canManageResponse(response)" class="flex items-center gap-2">
              <button
                v-if="editingResponseId !== response.id"
                type="button"
                class="text-xs font-semibold text-[#366f8c] hover:underline"
                @click="startEditResponse(response)"
              >
                Edit
              </button>
              <button
                type="button"
                class="text-xs font-semibold text-red-600 hover:underline"
                @click="handleDeleteResponse(response.id)"
              >
                Delete
              </button>
            </div>
          </div>

          <div v-if="editingResponseId === response.id" class="mt-2 grid gap-2">
            <textarea
              v-model="editingContent"
              rows="3"
              class="w-full rounded-lg border border-[#d8cdbf] bg-white px-3 py-2 text-sm outline-none transition focus:border-[#29a9c5]"
            ></textarea>
            <div class="flex items-center justify-end gap-2">
              <button
                type="button"
                class="rounded-full border border-[#d0e4ee] bg-white px-3 py-1 text-xs font-semibold text-[#366f8c] transition hover:bg-[#eaf4f9]"
                @click="cancelEditResponse"
              >
                Cancel
              </button>
              <button
                type="button"
                class="rounded-full bg-[#366f8c] px-3 py-1 text-xs font-semibold text-white transition hover:bg-[#2f627d]"
                @click="saveEditResponse(response)"
              >
                Save
              </button>
            </div>
          </div>

          <p v-else class="mt-2 whitespace-pre-wrap text-sm text-[#33424c]">{{ response.content }}</p>
        </li>

        <li v-if="!(question?.responses || []).length" class="text-sm text-[#5b6d79]">No responses yet.</li>
      </ul>
    </section>
  </AppShell>
</template>
