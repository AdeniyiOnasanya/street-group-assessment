import type { ParseHomeownersPayload } from '../types/homeowner'

export const parseHomeownersCsv = async (file: File): Promise<ParseHomeownersPayload> => {
    const formData = new FormData()
    formData.append('csv', file)

    let response: Response
    try {
        response = await fetch('/api/homeowners/parse', {
            method: 'POST',
            body: formData,
        })
    } catch (error) {
        return {
            ok: false,
            message: error instanceof Error ? error.message : String(error)
        }
    }

    const payload = await response.json().catch(() => null)

    if (!response.ok) {
        return {
            ok: false,
            message: payload?.message ?? `Request failed (${response.status})`,
        }
    }

    if (!Array.isArray(payload)) {
        return {
            ok: false,
            message: 'Unexpected response format from server.'
        }
    }

    return { ok: true, data: payload }
}
