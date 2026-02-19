<template>
    <div class="flex items-center gap-3 flex-wrap">
        <input
            class="block cursor-pointer w-full file:py-2 file:px-3 file:mr-4 file:rounded-lg file:border-0 file:bg-slate-100 file:text-slate-900 sm:w-auto text-sm  hover:file:bg-slate-200 border border-default-medium rounded "
            type="file"
            :accept="accept"
            @change="onChange"
        />
        <div v-if="selectedName" class="text-xs text-slate-500">Selected file name: {{ selectedName }}</div>
    </div>
</template>

<script setup lang="ts">

defineProps<{
    accept?: string
    selectedName?: string
}>()

const emit = defineEmits<{
    (e: 'selected', file: File | null): void
}>()

function onChange(e: Event) {
    const input = e.target as HTMLInputElement
    emit('selected', input.files?.[0] ?? null)
}
</script>
