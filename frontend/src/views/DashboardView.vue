<script setup>
import { useRouter } from 'vue-router'

const router = useRouter()

const performanceCards = [
  { label: 'New Questions', value: '148', trend: '+12.4%', state: 'up' },
  { label: 'Responses Today', value: '1,032', trend: '+8.1%', state: 'up' },
  { label: 'Favourites Added', value: '284', trend: '-2.3%', state: 'down' },
  { label: 'Avg. Response Time', value: '1m 24s', trend: '-18.7%', state: 'up' }
]

const recentActivity = [
  {
    title: 'Question approved in "Design Systems"',
    subtitle: 'Reviewed by Sarah M. • 4 minutes ago',
    tag: 'Moderation'
  },
  {
    title: '17 new responses on "Vue reactivity caveats"',
    subtitle: 'Auto-grouped in the feed • 11 minutes ago',
    tag: 'Community'
  },
  {
    title: 'Weekly digest published to subscribers',
    subtitle: '1,204 recipients • 39 minutes ago',
    tag: 'Email'
  },
  {
    title: 'User milestone reached: 10,000 favourites',
    subtitle: 'Global counter crossed target • 1 hour ago',
    tag: 'Growth'
  }
]

const chartHeights = ['35%', '68%', '52%', '81%', '63%', '92%', '74%']
const quickActions = ['Create Question', 'Review Reports', 'Export Responses', 'Manage Users']

function logout() {
  router.push('/login')
}
</script>

<template>
  <div
    class="min-h-screen bg-[radial-gradient(1200px_450px_at_5%_-10%,#e9ded0,transparent_70%),linear-gradient(160deg,#f8f2e9_0%,#f4efe6_58%,#f2e7d9_100%)] px-3 py-4 text-[#24313a] sm:px-4"
  >
    <div class="mx-auto grid w-full max-w-6xl gap-5">

      <!-- Top bar -->
      <div class="flex items-center justify-between">
        <span class="text-sm font-semibold text-[#5b6d79]">Local Mind</span>
        <button
          class="rounded-full border border-[#d0e4ee] bg-white/80 px-4 py-1.5 text-sm font-semibold text-[#366f8c] transition hover:bg-[#eaf4f9]"
          type="button"
          @click="logout"
        >
          Log out
        </button>
      </div>

      <!-- Header hero -->
      <header
        class="rounded-2xl border border-white/30 bg-[linear-gradient(120deg,rgba(54,111,140,0.92),rgba(186,89,53,0.9)),radial-gradient(circle_at_80%_20%,rgba(255,255,255,0.25),transparent_45%)] p-6 text-[#fdfaf2] shadow-[0_16px_40px_rgba(26,58,82,0.1)]"
      >
        <div class="flex flex-col gap-4 sm:flex-row sm:items-end sm:justify-between">
          <div>
            <p class="text-xs uppercase tracking-[0.14em] opacity-90">Operations Center</p>
            <h1 class="mt-1 text-2xl font-bold leading-tight sm:text-3xl">Local Mind Dashboard</h1>
            <p class="mt-2 max-w-2xl text-sm opacity-95 sm:text-base">
              Track your API activity, moderation flow, and community growth in one place.
            </p>
          </div>
          <button
            class="rounded-full bg-[#f7d794] px-4 py-2 text-sm font-bold text-[#2e2b22] transition hover:brightness-95"
            type="button"
          >
            Generate Report
          </button>
        </div>
      </header>

      <!-- Stats -->
      <section aria-label="Performance overview" class="grid gap-4 sm:grid-cols-2 xl:grid-cols-4">
        <article
          v-for="card in performanceCards"
          :key="card.label"
          class="rounded-2xl border border-[#eadfce] bg-white/90 p-4 shadow-[0_16px_40px_rgba(26,58,82,0.08)]"
        >
          <p class="text-sm text-[#5b6d79]">{{ card.label }}</p>
          <p class="mt-1 text-3xl font-bold leading-tight">{{ card.value }}</p>
          <p class="mt-2 text-sm font-semibold" :class="card.state === 'up' ? 'text-emerald-600' : 'text-orange-700'">
            {{ card.trend }}
          </p>
        </article>
      </section>

      <!-- Panels -->
      <section class="grid gap-4 lg:grid-cols-12">
        <article
          class="rounded-2xl border border-[#eadfce] bg-white/90 p-4 shadow-[0_16px_40px_rgba(26,58,82,0.08)] lg:col-span-8"
        >
          <div class="mb-4 flex items-center justify-between gap-3">
            <h2 class="text-base font-bold">Recent Activity</h2>
            <a class="text-sm font-semibold text-[#366f8c] hover:underline" href="#">View all</a>
          </div>
          <ul class="grid gap-3">
            <li
              v-for="item in recentActivity"
              :key="item.title"
              class="flex items-start justify-between gap-3 rounded-xl border border-[#eadfce] bg-[#fdf8ef] p-3"
            >
              <div>
                <p class="font-semibold">{{ item.title }}</p>
                <p class="text-sm text-[#5b6d79]">{{ item.subtitle }}</p>
              </div>
              <span class="shrink-0 rounded-full border border-[#b3d3e3] bg-[#eaf4f9] px-2 py-1 text-xs text-[#27536b]">
                {{ item.tag }}
              </span>
            </li>
          </ul>
        </article>

        <article
          class="rounded-2xl border border-[#eadfce] bg-white/90 p-4 shadow-[0_16px_40px_rgba(26,58,82,0.08)] lg:col-span-4"
        >
          <div class="mb-4 flex items-center justify-between gap-3">
            <h2 class="text-base font-bold">Today at a Glance</h2>
          </div>
          <div class="grid h-44 grid-cols-7 items-end gap-2 rounded-xl border border-[#eadfce] bg-gradient-to-t from-[#f0f6fa] to-[#fbfdff] p-3">
            <span
              v-for="(height, index) in chartHeights"
              :key="`bar-${index}`"
              class="block rounded-t-full rounded-b-md bg-gradient-to-t from-[#ba5935] to-[#d98762]"
              :style="{ height }"
            ></span>
          </div>
          <p class="mt-3 text-sm text-[#5b6d79]">
            Peak engagement happened at <strong>14:00</strong> with a response spike of <strong>+26%</strong>.
          </p>
        </article>

        <article
          class="rounded-2xl border border-[#eadfce] bg-white/90 p-4 shadow-[0_16px_40px_rgba(26,58,82,0.08)] lg:col-span-4"
        >
          <div class="mb-4 flex items-center justify-between gap-3">
            <h2 class="text-base font-bold">Quick Actions</h2>
          </div>
          <div class="grid gap-2">
            <button
              v-for="action in quickActions"
              :key="action"
              type="button"
              class="rounded-lg border border-[#eadfce] bg-[#fdf8ef] px-3 py-2 text-left text-sm font-semibold transition hover:bg-[#f6eee0]"
            >
              {{ action }}
            </button>
          </div>
        </article>
      </section>

    </div>
  </div>
</template>
