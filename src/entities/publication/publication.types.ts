// TODO: double check this type for correct properties and optionals when stoplight updates - https://conduction.stoplight.io/docs/open-catalogi/fee989a9c8e3f-publication

export type TPublication = {
    id: string
	title: string
	summary: string
	description?: string
	reference?: string
	image?: string
	category: string
    catalogi: string
    metadata: string
	portal?: string
	featured?: boolean
	organization?: {
        type?: string
        $ref?: string
        format?: string
        description?: string
    }
	schema?: string
	status?: string
	attachments?: {
        type?: string
        items?: {
            $ref?: string
        }
        format?: string
    }
    attachmentCount?: number
    themes?: string[]
	data?: {
        type?: string
        required?: boolean
    }
	anonymization?: {
        type?: string
        $ref?: string
        format?: string
        description?: string
    }
    languageObject?: {
        type?: string
        $ref?: string
        format?: string
        description?: string
    }
    publicationDate?: string
	modified?: string
	license?: {
        type?: string
    }
}
