export interface Homeowner{
    title: string
    first_name: string |  null
    last_name: string
    initial: string | null
}

export type ParseHomeownersPayload =
    | { ok: true; data: Homeowner[] }
    | { ok: false; message: string }

