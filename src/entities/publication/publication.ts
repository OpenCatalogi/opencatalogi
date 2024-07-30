import { TPublication } from './publication.types'
import { z } from 'zod'

export class Publication implements TPublication {

	public id: string
	public title: string
	public summary: string
	public reference?: string
	public description?: string
	public image?: string
	public category: string
	public portal?: string
	public catalogi?: string
	public metaData?: string
	public publicationDate?: string
	public modified?: string
	public featured?: boolean
	public organization?: {
        type?: string
        $ref?: string
        format?: string
        description?: string
    }

	public data: {
        type?: string
        required?: boolean
    }

	public attachments?: {
        type?: string
        items?: {
            $ref?: string
        }
        format?: string
    }

	public attachmentCount?: number
	public schema: string
	public status?: string
	public license?: {
        type?: string
    }

	public themes?: string[]
	public anonymization?: {
        type?: string
        format?: string
        description?: string
        $ref?: string
    }

	public languageObject?: {
        type?: string
        format?: string
        description?: string
        $ref?: string
    }

	public language?: {
        code?: string
        level?: string
    }

	constructor(data: TPublication) {
		this.hydrate(data)
	}

	/* istanbul ignore next */ // Jest does not recognize the code coverage of these 2 methods
	private hydrate(data: TPublication) {
		this.id = data.id?.toString() || ''
		this.title = data.title || ''
		this.summary = data.summary || ''
		this.reference = data.reference || ''
		this.description = data.description || ''
		this.image = data.image || ''
		this.category = data.category || ''
		this.portal = data.portal || ''
		this.catalogi = data.catalogi || ''
		this.metaData = data.metaData || ''
		this.publicationDate = data.publicationDate || ''
		this.modified = data.modified || ''
		this.featured = data.featured || false
		this.organization = data.organization || {}
		this.data = data.data || {}
		this.attachments = data.attachments || {}
		this.attachmentCount = data.attachmentCount || 0
		this.schema = data.schema || ''
		this.status = data.status || ''
		this.license = data.license || {}
		this.themes = data.themes || []
		this.languageObject = data.languageObject || {}
		this.anonymization = data.anonymization || {}
	}

	/* istanbul ignore next */
	public validate(): boolean {
		// https://conduction.stoplight.io/docs/open-catalogi/9bebd6bf4fe35-publication
		const schema = z.object({
			title: z.string().min(1), // .min(1) on a string functionally works the same as a nonEmpty check (SHOULD NOT BE COMBINED WITH .OPTIONAL())
			summary: z.string().min(1),
			description: z.string().optional(),
			reference: z.string().optional(),
			image: z.string().optional(),
			category: z.string().min(1),
			portal: z.string().optional(),
			featured: z.boolean().optional(),
			organization: z.object({
				type: z.string().optional(),
				$ref: z.string().optional(),
				format: z.string().optional(),
				description: z.string().optional(),
			}).optional(),
			schema: z.string().url().min(1),
			status: z.string().optional(),
			attachments: z.object({
				type: z.string().optional(),
				items: z.object({
					$ref: z.string().optional(),
				}).optional(),
				format: z.string().optional(),
			}).optional(),
			themes: z.string().array().optional(),
			data: z.object({
				type: z.string().optional(),
				required: z.boolean().optional(),
			}),
			anonymization: z.object({
				type: z.string().optional(),
				format: z.string().optional(),
				description: z.string().optional(),
				$ref: z.string().optional(),
			}).optional(),
			languageObject: z.object({
				type: z.string().optional(),
				format: z.string().optional(),
				description: z.string().optional(),
				$ref: z.string().optional(),
			}).optional(),
			published: z.string().datetime().optional(),
			modified: z.string().datetime().optional(),
			license: z.object({
				type: z.string().optional(),
			}).optional(),
		})

		const result = schema.safeParse({ ...this })

		return result.success
	}

}
