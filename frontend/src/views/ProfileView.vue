<script setup>
import { onMounted, ref } from 'vue'
import AppShell from '../components/AppShell.vue'
import { getMe } from '../services/api'
import { clearAuthSession, getStoredUser, setAuthSession } from '../services/auth'
import { useRouter } from 'vue-router'

const router = useRouter()
const loading = ref(true)
const error = ref('')
const profile = ref(getStoredUser())

async function loadProfile() {
  loading.value = true
  error.value = ''

  try {
    const me = await getMe()
    profile.value = me
    setAuthSession(null, me)
  } catch (err) {
    if (err?.status === 401) {
      clearAuthSession()
      router.replace('/login')
      return
    }
    error.value = err instanceof Error ? err.message : 'Failed to load profile.'
  } finally {
    loading.value = false
  }
}

onMounted(loadProfile)
</script>

<template>
  <AppShell>
    <section class="rounded-2xl border border-white/30 bg-[linear-gradient(120deg,rgba(54,111,140,0.92),rgba(186,89,53,0.9))] p-6 text-[#fdfaf2] shadow-[0_16px_40px_rgba(26,58,82,0.1)]">
      <p class="text-xs uppercase tracking-[0.14em] opacity-90">Account</p>
      <h1 class="mt-1 text-2xl font-bold sm:text-3xl">My Profile</h1>
    </section>

    <p v-if="error" class="mt-4 rounded-lg border border-red-200 bg-red-50 px-3 py-2 text-sm text-red-600">{{ error }}</p>

    <section class="mt-4 rounded-2xl border border-[#eadfce] bg-white/90 p-4 shadow-[0_16px_40px_rgba(26,58,82,0.08)] sm:p-6">
      <p v-if="loading" class="text-sm text-[#5b6d79]">Loading profile...</p>

      <dl v-else class="grid gap-4">
        <div>
          <dt class="text-xs font-semibold uppercase tracking-widest text-[#5b6d79]">Full Name</dt>
          <dd class="mt-1 text-lg font-semibold text-[#24313a]">{{ profile?.full_name || 'Not set' }}</dd>
        </div>

        <div>
          <dt class="text-xs font-semibold uppercase tracking-widest text-[#5b6d79]">Email</dt>
          <dd class="mt-1 text-base text-[#33424c]">{{ profile?.email || 'Not set' }}</dd>
        </div>

        <div>
          <dt class="text-xs font-semibold uppercase tracking-widest text-[#5b6d79]">Role</dt>
          <dd class="mt-1 text-base capitalize text-[#33424c]">{{ profile?.role || 'user' }}</dd>
        </div>

        <div>
          <dt class="text-xs font-semibold uppercase tracking-widest text-[#5b6d79]">Joined</dt>
          <dd class="mt-1 text-base text-[#33424c]">{{ profile?.created_at ? new Date(profile.created_at).toLocaleString() : 'N/A' }}</dd>
        </div>
      </dl>
    </section>
  </AppShell>
</template>
