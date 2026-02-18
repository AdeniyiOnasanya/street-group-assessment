<template>
    <div
        class="rounded px-4 py-2 text-sm whitespace-pre-wrap"
        :class="type === 'error' ? 'bg-red-50 text-red-700 ring-1 ring-red-200' : 'bg-green-50 text-green-700 ring-1 ring-green-200'">
        {{ message }}
    </div>
</template>

<script setup lang="ts">
defineProps<{
    type: 'error' | 'success'
    message: string
}>()
</script>
