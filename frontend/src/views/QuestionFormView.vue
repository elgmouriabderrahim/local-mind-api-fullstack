<script setup>
import { computed, onMounted, ref } from 'vue'
import { useRoute, useRouter } from 'vue-router'
import AppShell from '../components/AppShell.vue'
import { createQuestion, getQuestion, updateQuestion } from '../services/api'
import { clearAuthSession, getStoredUser } from '../services/auth'

const route = useRoute()
const router = useRouter()
const user = getStoredUser()

const questionId = computed(() => route.params.id)
const isEditMode = computed(() => Boolean(questionId.value))

const loading = ref(false)
const submitting = ref(false)
const error = ref('')
const title = ref('')
const content = ref('')
const locationName = ref('')

async function loadQuestion() {
  if (!isEditMode.value) return

  loading.value = true
  error.value = ''

  try {
    const question = await getQuestion(questionId.value)

    if (question?.user_id !== user?.id && user?.role !== 'admin') {
      error.value = 'You can only edit your own questions.'
      return
    }

    title.value = question?.title || ''
    content.value = question?.content || ''
    locationName.value = question?.location_name || ''
  } catch (err) {
    if (err?.status === 401) {
      clearAuthSession()
      router.replace('/login')
      return
    }
    error.value = err instanceof Error ? err.message : 'Failed to load question.'
  } finally {
    loading.value = false
  }
}

async function handleSubmit() {
  error.value = ''

  if (!title.value.trim() || !content.value.trim()) {
    error.value = 'Title and content are required.'
    return
  }

  submitting.value = true

  const payload = {
    title: title.value.trim(),
    content: content.value.trim(),
    location_name: locationName.value.trim() || null
  }

  try {
    if (isEditMode.value) {
      await updateQuestion(questionId.value, payload)
      await router.push(`/questions/${questionId.value}`)
    } else {
      const result = await createQuestion(payload)
      const createdId = result?.data?.id
      await router.push(createdId ? `/questions/${createdId}` : '/questions')
    }
  } catch (err) {
    if (err?.status === 401) {
      clearAuthSession()
      router.replace('/login')
      return
    }
    error.value = err instanceof Error ? err.message : 'Failed to save question.'
  } finally {
    submitting.value = false
  }
}

onMounted(loadQuestion)
</script>

<template>
  <AppShell>
    <section class="rounded-2xl border border-white/30 bg-[linear-gradient(120deg,rgba(54,111,140,0.92),rgba(186,89,53,0.9))] p-6 text-[#fdfaf2] shadow-[0_16px_40px_rgba(26,58,82,0.1)]">
      <p class="text-xs uppercase tracking-[0.14em] opacity-90">Question Editor</p>
      <h1 class="mt-1 text-2xl font-bold sm:text-3xl">{{ isEditMode ? 'Edit Question' : 'Create Question' }}</h1>
    </section>

    <section class="mt-4 rounded-2xl border border-[#eadfce] bg-white/90 p-4 shadow-[0_16px_40px_rgba(26,58,82,0.08)] sm:p-6">
      <p v-if="loading" class="text-sm text-[#5b6d79]">Loading question...</p>

      <form v-else class="grid gap-4" @submit.prevent="handleSubmit">
        <div>
          <label class="mb-1 block text-xs font-semibold uppercase tracking-widest text-[#5b6d79]" for="question-title">
            Title
          </label>
          <input
            id="question-title"
            v-model="title"
            type="text"
            maxlength="100"
            required
            class="w-full rounded-lg border border-[#d8cdbf] bg-white px-3 py-2 text-sm outline-none transition focus:border-[#29a9c5]"
          />
        </div>

        <div>
          <label class="mb-1 block text-xs font-semibold uppercase tracking-widest text-[#5b6d79]" for="question-content">
            Content
          </label>
          <textarea
            id="question-content"
            v-model="content"
            rows="7"
            required
            class="w-full rounded-lg border border-[#d8cdbf] bg-white px-3 py-2 text-sm outline-none transition focus:border-[#29a9c5]"
          ></textarea>
        </div>

        <div>
          <label class="mb-1 block text-xs font-semibold uppercase tracking-widest text-[#5b6d79]" for="question-location">
            Location (optional)
          </label>
          <input
            id="question-location"
            v-model="locationName"
            type="text"
            maxlength="255"
            class="w-full rounded-lg border border-[#d8cdbf] bg-white px-3 py-2 text-sm outline-none transition focus:border-[#29a9c5]"
          />
        </div>

        <p v-if="error" class="rounded-lg border border-red-200 bg-red-50 px-3 py-2 text-sm text-red-600">{{ error }}</p>

        <div class="flex flex-wrap items-center gap-2">
          <button
            type="submit"
            :disabled="submitting"
            class="rounded-full bg-[#366f8c] px-4 py-2 text-sm font-bold text-white transition hover:bg-[#2f627d] disabled:cursor-not-allowed disabled:opacity-70"
          >
            {{ submitting ? 'Saving...' : isEditMode ? 'Save Changes' : 'Create Question' }}
          </button>

          <button
            type="button"
            class="rounded-full border border-[#d0e4ee] bg-white px-4 py-2 text-sm font-semibold text-[#366f8c] transition hover:bg-[#eaf4f9]"
            @click="router.push('/questions')"
          >
            Cancel
          </button>
        </div>
      </form>
    </section>
  </AppShell>
</template>
