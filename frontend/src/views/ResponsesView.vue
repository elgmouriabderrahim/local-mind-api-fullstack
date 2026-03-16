<script setup>
import { computed, onMounted, ref } from 'vue'
import { RouterLink, useRouter } from 'vue-router'
import AppShell from '../components/AppShell.vue'
import { deleteResponse, getResponses, updateResponse } from '../services/api'
import { clearAuthSession, getStoredUser } from '../services/auth'

const router = useRouter()
const currentUser = ref(getStoredUser())

const loading = ref(true)
const error = ref('')
const responses = ref([])
const editingResponseId = ref(null)
const editingContent = ref('')

const hasResponses = computed(() => responses.value.length > 0)

function canManageResponse(response) {
  if (!currentUser.value) return false
  return response.user_id === currentUser.value.id || currentUser.value.role === 'admin'
}

async function loadResponses() {
  loading.value = true
  error.value = ''

  try {
    const payload = await getResponses()
    responses.value = Array.isArray(payload?.data) ? payload.data : []
  } catch (err) {
    if (err?.status === 401) {
      clearAuthSession()
      router.replace('/login')
      return
    }
    error.value = err instanceof Error ? err.message : 'Failed to load responses.'
  } finally {
    loading.value = false
  }
}

function startEdit(response) {
  editingResponseId.value = response.id
  editingContent.value = response.content
}

function cancelEdit() {
  editingResponseId.value = null
  editingContent.value = ''
}

async function saveEdit(response) {
  if (!editingContent.value.trim()) return

  try {
    const result = await updateResponse(response.id, {
      content: editingContent.value.trim()
    })

    const updated = result?.data
    if (updated) {
      responses.value = responses.value.map((item) => {
        if (item.id !== response.id) return item
        return { ...item, ...updated }
      })
    }

    cancelEdit()
  } catch (err) {
    error.value = err instanceof Error ? err.message : 'Failed to update response.'
  }
}

async function handleDelete(responseId) {
  if (!window.confirm('Delete this response?')) return

  try {
    await deleteResponse(responseId)
    responses.value = responses.value.filter((response) => response.id !== responseId)
  } catch (err) {
    error.value = err instanceof Error ? err.message : 'Failed to delete response.'
  }
}

onMounted(loadResponses)
</script>

<template>
  <AppShell>
    <section class="rounded-2xl border border-white/30 bg-[linear-gradient(120deg,rgba(54,111,140,0.92),rgba(186,89,53,0.9))] p-6 text-[#fdfaf2] shadow-[0_16px_40px_rgba(26,58,82,0.1)]">
      <p class="text-xs uppercase tracking-[0.14em] opacity-90">Community Activity</p>
      <h1 class="mt-1 text-2xl font-bold sm:text-3xl">Responses</h1>
    </section>

    <p v-if="error" class="mt-4 rounded-lg border border-red-200 bg-red-50 px-3 py-2 text-sm text-red-600">{{ error }}</p>

    <section class="mt-4 rounded-2xl border border-[#eadfce] bg-white/90 p-4 shadow-[0_16px_40px_rgba(26,58,82,0.08)]">
      <p v-if="loading" class="text-sm text-[#5b6d79]">Loading responses...</p>
      <p v-else-if="!hasResponses" class="text-sm text-[#5b6d79]">No responses yet.</p>

      <ul v-else class="grid gap-3">
        <li
          v-for="response in responses"
          :key="response.id"
          class="rounded-xl border border-[#eadfce] bg-[#fdf8ef] p-4"
        >
          <div class="flex flex-wrap items-center justify-between gap-2">
            <div>
              <p class="text-xs font-semibold text-[#5b6d79]">
                {{ response.user?.full_name || response.user?.email || 'Unknown user' }}
              </p>
              <RouterLink
                :to="`/questions/${response.question_id}`"
                class="text-sm font-semibold text-[#366f8c] hover:underline"
              >
                {{ response.question?.title || `Question #${response.question_id}` }}
              </RouterLink>
            </div>

            <div v-if="canManageResponse(response)" class="flex items-center gap-2">
              <button
                v-if="editingResponseId !== response.id"
                type="button"
                class="rounded-full border border-[#d0e4ee] bg-white px-3 py-1 text-xs font-semibold text-[#366f8c] transition hover:bg-[#eaf4f9]"
                @click="startEdit(response)"
              >
                Edit
              </button>
              <button
                type="button"
                class="rounded-full border border-red-200 bg-white px-3 py-1 text-xs font-semibold text-red-600 transition hover:bg-red-50"
                @click="handleDelete(response.id)"
              >
                Delete
              </button>
            </div>
          </div>

          <div v-if="editingResponseId === response.id" class="mt-3 grid gap-2">
            <textarea
              v-model="editingContent"
              rows="3"
              class="w-full rounded-lg border border-[#d8cdbf] bg-white px-3 py-2 text-sm outline-none transition focus:border-[#29a9c5]"
            ></textarea>
            <div class="flex items-center justify-end gap-2">
              <button
                type="button"
                class="rounded-full border border-[#d0e4ee] bg-white px-3 py-1 text-xs font-semibold text-[#366f8c] transition hover:bg-[#eaf4f9]"
                @click="cancelEdit"
              >
                Cancel
              </button>
              <button
                type="button"
                class="rounded-full bg-[#366f8c] px-3 py-1 text-xs font-semibold text-white transition hover:bg-[#2f627d]"
                @click="saveEdit(response)"
              >
                Save
              </button>
            </div>
          </div>
          <p v-else class="mt-2 whitespace-pre-wrap text-sm text-[#33424c]">{{ response.content }}</p>
        </li>
      </ul>
    </section>
  </AppShell>
</template>
