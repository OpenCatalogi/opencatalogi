// TODO: double check this type for correct properties and optionals when stoplight updates - https://conduction.stoplight.io/docs/open-catalogi/fee989a9c8e3f-publication

export type TPublication = {
    id: string
	title: string
	summary: string
	description?: string
	reference?: string
	image?: string
	category: string
	portal?: string
	catalogi?: string
	metaData?: string
	featured?: boolean
	organization?: {
        type?: string
        $ref?: string
        format?: string
        description?: string
    }
    schema: string
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
    data: {
        type?: string
        required?: boolean
    }
	anonymization?: {
        type?: string
        format?: string
        description?: string
        $ref?: string
    }
    languageObject?: {
        type?: string
        format?: string
        description?: string
        $ref?: string
    }
    publicationDate?: string
	modified?: string
    license?: {
        type?: string
    }
}
