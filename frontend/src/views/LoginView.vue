<script setup>
import { ref } from 'vue'
import { useRouter } from 'vue-router'

const router = useRouter()
const email = ref('')
const password = ref('')
const error = ref('')

function handleLogin() {
  error.value = ''
  if (!email.value || !password.value) {
    error.value = 'Please fill in all fields.'
    return
  }
  // TODO: wire to Sanctum API
  router.push('/dashboard')
}
</script>

<template>
  <div class="flex min-h-screen items-center justify-center bg-[#0e2a47] p-4">
    <div class="flex w-full max-w-3xl overflow-hidden rounded-2xl shadow-2xl">

      <!-- Left — night scene -->
      <div
        class="relative hidden w-[45%] flex-col items-center justify-between overflow-hidden bg-gradient-to-b from-[#0d2b50] via-[#1a4a7a] to-[#1e6fa8] p-8 md:flex"
      >
        <!-- Stars -->
        <div class="pointer-events-none absolute inset-0">
          <span v-for="i in 40" :key="i"
            class="absolute block rounded-full bg-white opacity-70"
            :style="{
              width: Math.random() * 2.5 + 0.5 + 'px',
              height: Math.random() * 2.5 + 0.5 + 'px',
              top: Math.random() * 65 + '%',
              left: Math.random() * 100 + '%'
            }"
          ></span>
        </div>

        <!-- Moon -->
        <div class="relative mt-4 flex h-28 w-28 items-center justify-center rounded-full bg-white shadow-[0_0_60px_20px_rgba(255,255,255,0.35)]">
          <div class="h-24 w-24 rounded-full bg-[#e8f0f7]"></div>
        </div>

        <!-- Silhouette scene -->
        <div class="relative w-full">
          <svg viewBox="0 0 300 160" xmlns="http://www.w3.org/2000/svg" class="w-full">
            <!-- Hills -->
            <ellipse cx="80" cy="155" rx="130" ry="55" fill="#0d2240"/>
            <ellipse cx="240" cy="160" rx="100" ry="45" fill="#0a1c36"/>
            <!-- Pine trees -->
            <polygon points="30,120 45,80 60,120" fill="#071628"/>
            <polygon points="55,120 73,72 91,120" fill="#071628"/>
            <polygon points="85,125 100,85 115,125" fill="#071628"/>
            <polygon points="200,130 215,88 230,130" fill="#071628"/>
            <polygon points="230,128 248,82 266,128" fill="#071628"/>
            <polygon points="258,132 272,98 286,132" fill="#071628"/>
            <!-- Branch -->
            <path d="M60,70 Q130,50 210,65" stroke="#071628" stroke-width="4" fill="none" stroke-linecap="round"/>
            <!-- Sub-branch -->
            <path d="M170,65 Q185,50 200,40" stroke="#071628" stroke-width="2.5" fill="none" stroke-linecap="round"/>
            <!-- Bird 1 -->
            <ellipse cx="140" cy="57" rx="7" ry="5" fill="#071628"/>
            <path d="M140,52 Q145,46 150,50" stroke="#071628" stroke-width="1.5" fill="none"/>
            <circle cx="147" cy="53" r="3" fill="#071628"/>
            <!-- Bird 2 -->
            <ellipse cx="160" cy="59" rx="6" ry="4.5" fill="#071628"/>
            <path d="M156,55 Q151,49 146,53" stroke="#071628" stroke-width="1.5" fill="none"/>
            <circle cx="153" cy="56" r="3" fill="#071628"/>
            <!-- Ground snow -->
            <ellipse cx="150" cy="158" rx="160" ry="18" fill="#1e6fa8" opacity="0.5"/>
          </svg>
        </div>

        <!-- Text + socials -->
        <div class="relative z-10 mt-2 text-center text-white">
          <h2 class="text-xl font-bold tracking-wide">Welcome Page</h2>
          <p class="mt-2 text-xs leading-relaxed text-white/75">
            Lorem ipsum dolor sit amet, consectetur<br>
            adipiscing elit. Donec pharetra lacinia<br>
            maximus. Integer pulvinar lacus.
          </p>

          <div class="mt-5 flex items-center gap-3 text-white/50">
            <div class="h-px flex-1 bg-white/25"></div>
            <span class="text-[10px] uppercase tracking-widest">Get connected with</span>
            <div class="h-px flex-1 bg-white/25"></div>
          </div>

          <div class="mt-4 flex justify-center gap-3">
            <button type="button" class="flex h-9 w-9 items-center justify-center rounded-full bg-[#1da1f2] text-white transition hover:opacity-90">
              <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="h-4 w-4">
                <path d="M22.46 6c-.77.35-1.6.58-2.46.67a4.28 4.28 0 001.88-2.37 8.59 8.59 0 01-2.72 1.04A4.27 4.27 0 0012 9.77a12.12 12.12 0 01-8.79-4.46 4.27 4.27 0 001.32 5.7 4.24 4.24 0 01-1.94-.53v.05a4.27 4.27 0 003.43 4.19 4.3 4.3 0 01-1.93.07 4.27 4.27 0 003.99 2.97A8.57 8.57 0 012 19.54a12.1 12.1 0 006.56 1.92c7.88 0 12.2-6.53 12.2-12.2l-.01-.56A8.67 8.67 0 0024 6.82a8.5 8.5 0 01-1.54.18z"/>
              </svg>
            </button>
            <button type="button" class="flex h-9 w-9 items-center justify-center rounded-full bg-[#dd4b39] text-white transition hover:opacity-90">
              <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="h-4 w-4">
                <path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm5.01 7h-2.13c-.24 0-.5.31-.5.73v1.54h2.63l-.34 2.65h-2.29V19h-2.76v-6.08H9.5v-2.65h2.12V9.41C11.62 7.38 12.86 6 14.88 6c.96 0 1.94.07 2.13.1V9z"/>
              </svg>
            </button>
            <button type="button" class="flex h-9 w-9 items-center justify-center rounded-full bg-[#3b5998] text-white transition hover:opacity-90">
              <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="h-4 w-4">
                <path d="M20 3H4a1 1 0 00-1 1v16a1 1 0 001 1h8.62v-7h-2.35v-2.69h2.35v-2c0-2.33 1.42-3.6 3.5-3.6 1 0 2.04.07 2.29.11v2.65h-1.57c-1.23 0-1.47.58-1.47 1.44v1.4h2.93l-.38 2.69h-2.55V21H20a1 1 0 001-1V4a1 1 0 00-1-1z"/>
              </svg>
            </button>
          </div>
        </div>
      </div>

      <!-- Right — login form -->
      <div class="flex w-full flex-col bg-white p-8 md:w-[55%]">
        <!-- Tabs -->
        <div class="mb-8 flex rounded-sm border border-[#29a9c5] text-sm font-semibold overflow-hidden">
          <RouterLink
            to="/login"
            class="flex-1 py-2.5 text-center text-[#29a9c5] transition hover:bg-[#f0fbfd]"
            active-class="bg-[#29a9c5] text-white hover:bg-[#29a9c5]"
          >
            Sign In
          </RouterLink>
          <RouterLink
            to="/register"
            class="flex-1 py-2.5 text-center text-[#29a9c5] transition hover:bg-[#f0fbfd]"
            active-class="bg-[#29a9c5] text-white hover:bg-[#29a9c5]"
          >
            Register
          </RouterLink>
        </div>

        <h1 class="mb-6 text-2xl font-bold text-gray-900">Sign In</h1>

        <form class="flex flex-col gap-6" @submit.prevent="handleLogin">
          <div>
            <label class="mb-1 block text-xs font-semibold uppercase tracking-widest text-gray-500" for="login-email">
              Email
            </label>
            <input
              id="login-email"
              v-model="email"
              type="email"
              placeholder="Enter Your Email"
              required
              autocomplete="email"
              class="w-full border-b border-gray-300 bg-transparent py-2 text-sm text-gray-800 placeholder-gray-400 outline-none transition focus:border-[#29a9c5]"
            />
          </div>

          <div>
            <label class="mb-1 block text-xs font-semibold uppercase tracking-widest text-gray-500" for="login-password">
              Password
            </label>
            <input
              id="login-password"
              v-model="password"
              type="password"
              placeholder="••••••••"
              required
              autocomplete="current-password"
              class="w-full border-b border-gray-300 bg-transparent py-2 text-sm text-gray-800 placeholder-gray-400 outline-none transition focus:border-[#29a9c5]"
            />
          </div>

          <p v-if="error" class="text-xs text-red-500">{{ error }}</p>

          <button
            type="submit"
            class="mt-2 w-full rounded-md bg-[#29a9c5] py-3 text-sm font-bold text-white transition hover:bg-[#2199b3]"
          >
            Sign In
          </button>
        </form>

        <p class="mt-6 text-center text-xs text-gray-500">
          Don't have an account?
          <RouterLink to="/register" class="font-semibold text-[#29a9c5] hover:underline">Register</RouterLink>
        </p>
      </div>
    </div>
  </div>
</template>
