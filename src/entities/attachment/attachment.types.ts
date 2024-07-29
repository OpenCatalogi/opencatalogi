export type TAttachment = {
    id: string
	title: string
	summary: string
	description?: string
	reference?: string
	labels?: string[]
	accessURL?: string
	downloadURL?: string
	type?: string
	extension?: string
	size?: number
	anonymization?: {
        anonymized?: boolean
        results?: string
    }
	language?: {
        code?: string
        level?: string
    }
    versionOf?: string
    hash?: string
    published?: string
    modified?: string
    license?: string
}
