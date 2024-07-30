import { TAttachment } from './attachment.types'
import { z } from 'zod'

export class Attachment implements TAttachment {

	public id: string
	public reference?: string
	public title: string
	public summary: string
	public description?: string
	public labels?: string[]
	public accessURL?: string
	public downloadURL?: string
	public type?: string
	public extension?: string
	public size?: number
	public anonymization?: {
        anonymized?: boolean
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
		this.id = data.id?.toString() || ''
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
			title: z.string().min(25).max(255), // .min(1) on a string functionally works the same as a nonEmpty check (SHOULD NOT BE COMBINED WITH .OPTIONAL())
			summary: z.string().min(50).max(2500),
			description: z.string().max(2500).optional(),
			reference: z.string().max(255).optional(),
			labels: z.string().array().optional(),
			accessURL: z.string().url().optional(),
			downloadURL: z.string().url().optional(),
			type: z.string().optional(),
			extension: z.string().optional(),
			size: z.number().optional(),
			anonymization: z.object({
				anonymized: z.boolean().optional(),
				results: z.string().max(2500).optional(),
			}).optional(),
			language: z.object({
				// this regex checks if the code has either 2 or 3 characters per group, and the -aaa after the first is optional
				code: z.string()
					.max(7)
					.regex(/([a-z]{2,3})(-[a-z]{2,3})?/g, 'language code is not a valid ISO 639-1 code (e.g. en-us)')
					.optional(),
				level: z.string().max(2).regex(/([a-zA-Z][0-9])/g, 'language level is not a valid level (e.g. A1)'),
			}).optional(),
			versionOf: z.string().uuid().optional(),
			hash: z.string().max(255).optional(),
			published: z.string().optional(),
			modified: z.string().optional(),
			license: z.string().optional(),
		})

		const result = schema.safeParse({ ...this })

		return result.success
	}

}
