import { TAttachment } from '../attachment'
import { TPublication } from './publication.types'
import { z } from 'zod'

export class Publication implements TPublication {

	public id: string
	public title: string
	public summary: string
	public description?: string
	public reference?: string
	public image?: string
	public category: string
	public catalogi: string
	public metaData: string
	public portal?: string
	public featured?: boolean
	public organization?: object[]
	public data?: object[]
	public attachments?: TAttachment[]
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

	public published?: string | Date
	public modified?: string | Date
	public license?: string

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
		this.catalogi = data.catalogi || ''
		// @ts-expect-error -- for backwards compatibility metaData will be used if metadata cannot be found
		this.metaData = (data.metadata ?? data.metaData) || ''
		this.portal = data.portal || ''
		this.catalogi = data.catalogi || ''
		this.metaData = data.metaData || ''
		this.featured = (typeof data.featured === 'boolean' && data.featured)
            // backend can send true and false back as "1" and "" (yes. not "0")
            // FIXME: remove once bug is fixed
            || (typeof data.featured === 'string' && !!parseInt(data.featured))
            || false
		this.organization = (!Array.isArray(data.organization) && data.organization) || {}
		this.schema = data.schema || ''
		this.status = data.status || ''
		this.attachments = (!Array.isArray(data.attachments) && data.attachments) || {}
		this.attachmentCount = data.attachmentCount || 0
		this.themes = data.themes || []
		this.data = (!Array.isArray(data.data) && data.data) || {}
		this.anonymization = (!Array.isArray(data.anonymization) && data.anonymization) || {}
		this.languageObject = (!Array.isArray(data.languageObject) && data.languageObject) || {}
		this.published = new Date(data.published) || ''
		this.modified = new Date(data.modified) || ''
		this.license = data.license || ''
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
