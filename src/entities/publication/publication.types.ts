// TODO: double check this type for correct properties and optionals when stoplight updates - https://conduction.stoplight.io/docs/open-catalogi/fee989a9c8e3f-publication

export type TPublication = {
    id: string
	title: string
	summary: string
	description?: string
	reference?: string
	image?: string
	category?: string
	portal?: string
	catalogi?: string
	metaData?: string
	publicationDate?: string
	modified?: string
	featured?: boolean
	organization?: object[]
	data?: object[]
	attachments?: string[]
	attachmentCount?: number
	schema?: string
	status?: string
	license?: string
	themes?: string
	anonymization?: {
        anonymized?: string
        results?: string
    }
	language?: {
        code?: string
        level?: string
    }
}
