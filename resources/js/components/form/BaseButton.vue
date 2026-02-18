<template>
    <button
        :type="type"
        :disabled="disabled || loading"
        class="inline-flex items-center justify-center gap-2 rounded-lg px-4 py-2 text-sm font-medium transition disabled:opacity-50 disabled:cursor-not-allowed focus:outline-none focus:ring-2 focus:ring-slate-400 focus:ring-offset-2"
        :class="variantClass"
        @click="$emit('click')"
    >
    <span
        v-if="loading"
        class="inline-block h-4 w-4 rounded-full border-2 border-current border-t-transparent animate-spin"
    />
        <span>
      <slot />
    </span>
    </button>
</template>

<script setup lang="ts">
import { computed } from 'vue'

const props = defineProps<{
    variant?: 'primary' | 'secondary'
    type?: 'button' | 'submit' | 'reset'
    disabled?: boolean
    loading?: boolean
}>()

defineEmits<{ (e: 'click'): void }>()

const variantClass = computed(() => {
    const value = props.variant ?? 'primary'
    return value === 'primary'
        ? ' text-white bg-slate-900 hover:bg-slate-800'
        : ' text-slate-800  bg-white ring-1 ring-slate-200 hover:bg-slate-50'
})
</script>
