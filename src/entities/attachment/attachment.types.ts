export type TAttachment = {
    id: string
	reference?: string
	title: string
	summary: string
	description?: string
	labels?: object[]
	accessURL?: string
	downloadURL?: string
	type?: string
	extension?: string
	size?: number
	anonymization?: {
        anonymized?: string
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
