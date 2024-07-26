import { TAttachment } from './attachment.types'
import { z } from 'zod'

export class Attachment implements TAttachment {

	public id: string
	public reference?: string
	public title: string
	public summary: string
	public description?: string
	public labels?: object[]
	public accessURL?: string
	public downloadURL?: string
	public type?: string
	public extension?: string
	public size?: number
	public anonymization?: {
        anonymized?: string
        results?: string
    }

	public language?: {
        code?: string
        level?: string
    }

	public versionOf?: string
	public hash?: string
	public published?: string
	public modified?: string
	public license?: string

	constructor(data: TAttachment) {
		this.hydrate(data)
	}

	/* istanbul ignore next */ // Jest does not recognize the code coverage of these 2 methods
	private hydrate(data: TAttachment) {
		this.id = data.id || ''
		this.reference = data.reference || ''
		this.title = data.title || ''
		this.summary = data.summary || ''
		this.description = data.description || ''
		this.labels = data.labels || []
		this.accessURL = data.accessURL || ''
		this.downloadURL = data.downloadURL || ''
		this.type = data.type || ''
		this.extension = data.extension || ''
		this.size = data.size || 0
		this.anonymization = data.anonymization || {}
		this.language = data.language || {}
		this.versionOf = data.versionOf || ''
		this.hash = data.hash || ''
		this.published = data.published || ''
		this.modified = data.modified || ''
		this.license = data.license || ''
	}

	/* istanbul ignore next */
	public validate(): boolean {
		// https://conduction.stoplight.io/docs/open-catalogi/9zm7p6fnazuod-attachment
		const schema = z.object({
			title: z.string().min(1), // .min(1) on a string functionally works the same as a nonEmpty check (SHOULD NOT BE COMBINED WITH .OPTIONAL())
			summary: z.string().min(1),
			description: z.string().optional(),
			reference: z.string().optional(),
			required: z.string().array().optional(),
			properties: z.object({
				id: z.string().min(1),
				title: z.string().min(1),
				description: z.string().optional(),
				type: z.string().optional(),
				format: z.string().optional(),
				pattern: z.number().optional(),
				default: z.string().optional(),
				behavior: z.string().optional(),
				required: z.boolean().optional(),
				deprecated: z.boolean().optional(),
				minLength: z.number().optional(),
				maxLength: z.number().optional(),
				example: z.string().optional(),
				minimum: z.number().optional(),
				maximum: z.number().optional(),
				multipleOf: z.number().optional(),
				exclusiveMin: z.boolean().optional(),
				exclusiveMax: z.boolean().optional(),
				minItems: z.number().optional(),
				maxItems: z.number().optional(),
			}).array().optional(),
		})

		const result = schema.safeParse({
			id: this.id,
			title: this.title,
			description: this.description,
			// version: this.version,
			// required: this.required,
			// properties: this.properties,
		})

		return result.success
	}

}
