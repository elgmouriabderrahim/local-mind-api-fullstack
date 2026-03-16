<script setup>
import { ref } from 'vue'
import { RouterLink, useRouter } from 'vue-router'
import { register } from '../services/api'
import { setAuthSession } from '../services/auth'

const router = useRouter()

const fullName = ref('')
const email = ref('')
const password = ref('')
const agreed = ref(false)
const error = ref('')
const submitting = ref(false)

async function handleRegister() {
  error.value = ''

  if (!fullName.value || !email.value || !password.value) {
    error.value = 'Please fill in all fields.'
    return
  }

  if (!agreed.value) {
    error.value = 'You must agree to the Terms of Service.'
    return
  }

  submitting.value = true

  try {
    const result = await register({
      full_name: fullName.value,
      email: email.value,
      password: password.value,
      password_confirmation: password.value
    })

    setAuthSession(result.token, result.user)
    await router.push('/dashboard')
  } catch (err) {
    error.value = err instanceof Error ? err.message : 'Registration failed.'
  } finally {
    submitting.value = false
  }
}
</script>

<template>
  <div class="flex min-h-screen items-center justify-center bg-[#0e2a47] p-4">
    <div class="flex w-full max-w-3xl overflow-hidden rounded-2xl shadow-2xl">
      <div
        class="relative hidden w-[45%] flex-col items-center justify-between overflow-hidden bg-gradient-to-b from-[#0d2b50] via-[#1a4a7a] to-[#1e6fa8] p-8 md:flex"
      >
        <div class="pointer-events-none absolute inset-0">
          <span
            v-for="i in 40"
            :key="i"
            class="absolute block h-[2px] w-[2px] rounded-full bg-white opacity-70"
            :style="{
              top: `${(i * 11) % 70}%`,
              left: `${(i * 19) % 100}%`
            }"
          ></span>
        </div>

        <div class="relative mt-4 flex h-28 w-28 items-center justify-center rounded-full bg-white shadow-[0_0_60px_20px_rgba(255,255,255,0.35)]">
          <div class="h-24 w-24 rounded-full bg-[#e8f0f7]"></div>
        </div>

        <div class="relative w-full">
          <svg viewBox="0 0 300 160" xmlns="http://www.w3.org/2000/svg" class="w-full">
            <ellipse cx="80" cy="155" rx="130" ry="55" fill="#0d2240"/>
            <ellipse cx="240" cy="160" rx="100" ry="45" fill="#0a1c36"/>
            <polygon points="30,120 45,80 60,120" fill="#071628"/>
            <polygon points="55,120 73,72 91,120" fill="#071628"/>
            <polygon points="85,125 100,85 115,125" fill="#071628"/>
            <polygon points="200,130 215,88 230,130" fill="#071628"/>
            <polygon points="230,128 248,82 266,128" fill="#071628"/>
            <polygon points="258,132 272,98 286,132" fill="#071628"/>
            <path d="M60,70 Q130,50 210,65" stroke="#071628" stroke-width="4" fill="none" stroke-linecap="round"/>
            <path d="M170,65 Q185,50 200,40" stroke="#071628" stroke-width="2.5" fill="none" stroke-linecap="round"/>
            <ellipse cx="140" cy="57" rx="7" ry="5" fill="#071628"/>
            <path d="M140,52 Q145,46 150,50" stroke="#071628" stroke-width="1.5" fill="none"/>
            <circle cx="147" cy="53" r="3" fill="#071628"/>
            <ellipse cx="160" cy="59" rx="6" ry="4.5" fill="#071628"/>
            <path d="M156,55 Q151,49 146,53" stroke="#071628" stroke-width="1.5" fill="none"/>
            <circle cx="153" cy="56" r="3" fill="#071628"/>
            <ellipse cx="150" cy="158" rx="160" ry="18" fill="#1e6fa8" opacity="0.5"/>
          </svg>
        </div>

        <div class="relative z-10 mt-2 text-center text-white">
          <h2 class="text-xl font-bold tracking-wide">Welcome Page</h2>
          <p class="mt-2 text-xs leading-relaxed text-white/75">
            Create your account and start
            managing your local mind feed.
          </p>
        </div>
      </div>

      <div class="flex w-full flex-col bg-white p-8 md:w-[55%]">
        <div class="mb-8 flex overflow-hidden rounded-sm border border-[#29a9c5] text-sm font-semibold">
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

        <h1 class="mb-6 text-2xl font-bold text-gray-900">Register</h1>

        <form class="flex flex-col gap-5" @submit.prevent="handleRegister">
          <div>
            <label class="mb-1 block text-xs font-semibold uppercase tracking-widest text-gray-500" for="reg-name">
              Full Name
            </label>
            <input
              id="reg-name"
              v-model="fullName"
              type="text"
              placeholder="Enter Your Full Name"
              required
              autocomplete="name"
              class="w-full border-b border-gray-300 bg-transparent py-2 text-sm text-gray-800 placeholder-gray-400 outline-none transition focus:border-[#29a9c5]"
            />
          </div>

          <div>
            <label class="mb-1 block text-xs font-semibold uppercase tracking-widest text-gray-500" for="reg-email">
              Email
            </label>
            <input
              id="reg-email"
              v-model="email"
              type="email"
              placeholder="Enter Your Email"
              required
              autocomplete="email"
              class="w-full border-b border-gray-300 bg-transparent py-2 text-sm text-gray-800 placeholder-gray-400 outline-none transition focus:border-[#29a9c5]"
            />
          </div>

          <div>
            <label class="mb-1 block text-xs font-semibold uppercase tracking-widest text-gray-500" for="reg-password">
              Password
            </label>
            <input
              id="reg-password"
              v-model="password"
              type="password"
              placeholder="••••••••"
              required
              autocomplete="new-password"
              class="w-full border-b border-gray-300 bg-transparent py-2 text-sm text-gray-800 placeholder-gray-400 outline-none transition focus:border-[#29a9c5]"
            />
          </div>

          <label class="flex cursor-pointer items-start gap-3">
            <input
              v-model="agreed"
              type="checkbox"
              class="mt-0.5 h-4 w-4 cursor-pointer accent-[#29a9c5]"
            />
            <span class="text-xs leading-relaxed text-gray-600">
              I agree to the
              <a href="#" class="font-semibold text-[#29a9c5] hover:underline">Terms of service</a>
            </span>
          </label>

          <p v-if="error" class="rounded border border-red-200 bg-red-50 px-3 py-2 text-xs text-red-600">
            {{ error }}
          </p>

          <button
            type="submit"
            :disabled="submitting"
            class="mt-1 w-full rounded-md bg-[#29a9c5] py-3 text-sm font-bold text-white transition hover:bg-[#2199b3] disabled:cursor-not-allowed disabled:opacity-70"
          >
            {{ submitting ? 'Creating account...' : 'Sign Up' }}
          </button>
        </form>

        <p class="mt-6 text-center text-xs text-gray-500">
          Already have an account?
          <RouterLink to="/login" class="font-semibold text-[#29a9c5] hover:underline">Sign In</RouterLink>
        </p>
      </div>
    </div>
  </div>
</template>
