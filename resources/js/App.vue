<template>
    <div class="min-h-screen bg-slate-50">
        <div class="max-w-3xl mx-auto px-4 py-10 space-y-6">
            <header class="text-center">
                <div class="text-2xl font-semibold text-slate-900">Homeowner Name Extractor</div>
                <p class="mt-2 text-sm text-slate-600">
                    Upload a CSV with a homeowner names and view the formatted JSON output.
                </p>
            </header>

            <section class="rounded-2xl  shadow-sm ring-1 ring-slate-200 p-6">
                <div class="flex flex-col sm:flex-row sm:items-center gap-3">
                    <FilePicker
                        class="flex-1"
                        accept=".csv,text/csv"
                        :selectedName="file?.name"
                        @selected="onFileSelected"
                    />

                    <div class="flex items-center gap-2">
                        <BaseButton
                            variant="primary"
                            :disabled="!file"
                            :loading="loading"
                            type="button"
                            @click="submit"
                        >
                            {{ loading ? 'Uploading...' : 'Upload' }}
                        </BaseButton>

                        <BaseButton
                            variant="secondary"
                            :disabled="homeowners.length === 0"
                            type="button"
                            @click="copyJson"
                        >
                            Copy JSON
                        </BaseButton>
                    </div>
                </div>

                <div class="mt-4 space-y-3">
                    <Alert v-if="error" type="error" :message="error" />
                    <Alert v-if="success" type="success" :message="success" />
                </div>

                <div v-if="loading" class="mt-4 text-sm text-slate-600 flex items-center gap-2">
                    <span class="inline-block h-4 w-4 rounded-full border-2 border-slate-300 border-t-slate-900 animate-spin"></span>
                    Uploading and parsing homeowners…
                </div>
            </section>

            <section v-if="homeowners.length" class="space-y-3">
                <div class="flex items-center justify-between flex-wrap gap-3">
                    <h2 class="text-sm font-semibold text-slate-900">Parsed JSON output</h2>
                    <p class="text-sm text-slate-600">
                        <span class="font-semibold">{{ homeowners.length }}</span> homeowners
                    </p>
                </div>

                <JsonViewer :data="homeowners" />
            </section>
        </div>
    </div>
</template>

<script setup lang="ts">
import { ref } from 'vue'
import type { Homeowner } from './types/homeowner'
import { parseHomeownersCsv } from './api/homeowner'

import Alert from './components/Alert.vue'
import BaseButton from './components/form/BaseButton.vue'
import FilePicker from './components/form/FilePicker.vue'
import JsonViewer from './components/JsonViewer.vue'

const file = ref<File | null>(null)
const loading = ref(false)
const error = ref('')
const success = ref('')
const homeowners = ref<Homeowner[]>([])

const resetMessages = () => {
    error.value = ''
    success.value = ''
}

const onFileSelected = (selected: File | null) => {
    file.value = selected
    homeowners.value = []
    resetMessages()
}

const submit= async () => {
    if (!file.value || loading.value) return

    loading.value = true
    homeowners.value = []
    resetMessages()

    const payload = await parseHomeownersCsv(file.value)

    loading.value = false

    if (!payload.ok) {
        error.value = payload.message
        return
    }

    homeowners.value = payload.data
    success.value = `Parsed ${payload.data.length} homeowners successfully.`
}

const copyJson= async () => {
    try {
        await navigator.clipboard.writeText(JSON.stringify(homeowners.value, null, 2))
        success.value = 'Copied JSON to the clipboard.'
        error.value = ''
    } catch {
        error.value = 'Failed to copy to the clipboard in this browser.'
    }
}
</script>
