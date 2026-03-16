<script setup>
import { RouterLink } from 'vue-router'
</script>

<template>
  <div class="flex min-h-screen items-center justify-center bg-[#0e2a47] p-4 text-white">
    <div class="w-full max-w-md rounded-2xl border border-white/20 bg-white/10 p-6 text-center backdrop-blur">
      <p class="text-sm uppercase tracking-[0.14em] text-white/70">Error</p>
      <h1 class="mt-2 text-3xl font-bold">Page Not Found</h1>
      <p class="mt-3 text-sm text-white/80">The page you requested does not exist.</p>
      <RouterLink
        to="/dashboard"
        class="mt-5 inline-block rounded-full bg-white px-4 py-2 text-sm font-semibold text-[#0e2a47] transition hover:bg-[#f0f0f0]"
      >
        Back to dashboard
      </RouterLink>
    </div>
  </div>
</template>
