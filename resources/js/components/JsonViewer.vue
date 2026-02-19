<template>
    <section class="rounded-2xl bg-slate-900 text-slate-50 ring-1 ring-slate-800 p-4 overflow-auto">
        <header class="mb-3 flex items-center justify-between gap-3">
            <h3 class="text-sm font-semibold">Parsed output (JSON)</h3>

            <span class="text-xs text-slate-300">
        {{ countLabel }}
      </span>
        </header>

        <pre class="text-xs whitespace-pre">{{ pretty }}</pre>
    </section>
</template>

<script setup lang="ts">
import { computed } from 'vue'
import type { Homeowner } from '../types/homeowner'

const props = defineProps<{ data: Homeowner[] }>()
const pretty = computed(() => JSON.stringify(props.data, null, 2))

const countLabel = computed(() => {
    const count = props.data.length

    return count === 1 ? '1 record' : `${count} records`
})
</script>
