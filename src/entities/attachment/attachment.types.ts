export type TAttachment = {
    id: string
	title: string
	summary: string
	description: string
	reference: string
	labels: string[]
	accessURL: string
	downloadURL: string
	type: string
	readonly extension: string
	readonly size: number
	anonymization: {
        anonymized: boolean
        results: string
    }
	language: {
        code: string
        level: string
    }
    versionOf: string
    readonly hash: string
    published: string
    readonly modified: string
    license: string
}
