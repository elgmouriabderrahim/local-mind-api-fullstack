<script setup>
import { computed } from 'vue'
import { RouterLink, useRoute, useRouter } from 'vue-router'
import { logout as logoutRequest } from '../services/api'
import { clearAuthSession, getStoredUser } from '../services/auth'

const route = useRoute()
const router = useRouter()

const user = computed(() => getStoredUser())

const allLinks = [
  { to: '/dashboard', label: 'Dashboard', adminOnly: true },
  { to: '/questions', label: 'Questions' },
  { to: '/responses', label: 'Responses' },
  { to: '/favourites', label: 'Favourites' },
  { to: '/profile', label: 'Profile' }
]

const links = computed(() =>
  allLinks.filter((link) => !link.adminOnly || user.value?.role === 'admin')
)

function isActive(path) {
  return route.path === path || route.path.startsWith(`${path}/`)
}

async function handleLogout() {
  try {
    await logoutRequest()
  } catch {
    // Token may already be invalid; clearing local session is enough.
  } finally {
    clearAuthSession()
    router.push('/login')
  }
}
</script>

<template>
  <div
    class="min-h-screen bg-[radial-gradient(1200px_450px_at_5%_-10%,#e9ded0,transparent_70%),linear-gradient(160deg,#f8f2e9_0%,#f4efe6_58%,#f2e7d9_100%)] px-3 py-4 text-[#24313a] sm:px-4"
  >
    <div class="mx-auto grid w-full max-w-6xl gap-5">
      <header class="rounded-2xl border border-[#eadfce] bg-white/90 px-4 py-3 shadow-[0_16px_40px_rgba(26,58,82,0.08)]">
        <div class="flex flex-wrap items-center justify-between gap-3">
          <div>
            <p class="text-sm font-semibold text-[#5b6d79]">Local Mind</p>
            <p class="text-xs text-[#5b6d79]">{{ user?.full_name || user?.email || 'Authenticated User' }}</p>
          </div>

          <nav class="flex flex-wrap items-center gap-2">
            <RouterLink
              v-for="link in links"
              :key="link.to"
              :to="link.to"
              class="rounded-full px-3 py-1.5 text-xs font-semibold transition"
              :class="isActive(link.to) ? 'bg-[#366f8c] text-white' : 'border border-[#d0e4ee] bg-white text-[#366f8c] hover:bg-[#eaf4f9]'"
            >
              {{ link.label }}
            </RouterLink>
          </nav>

          <button
            class="rounded-full border border-[#d0e4ee] bg-white px-4 py-1.5 text-sm font-semibold text-[#366f8c] transition hover:bg-[#eaf4f9]"
            type="button"
            @click="handleLogout"
          >
            Log out
          </button>
        </div>
      </header>

      <main>
        <slot />
      </main>
    </div>
  </div>
</template>
