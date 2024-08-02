import { TAttachment } from './attachment.types'
import { z } from 'zod'

export class Attachment implements TAttachment {

	public id: string
	public reference: string
	public title: string
	public summary: string
	public description: string
	public labels: string[]
	public accessURL: string
	public downloadURL: string
	public type: string
	public extension: string
	public size: number
	public anonymization: {
        anonymized: boolean
        results: string
    }

	public language: {
        code: string
        level: string
    }

	public versionOf: string
	public hash: string
	public published: string
	public modified: string
	public license: string

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
		this.anonymization = (!Array.isArray(data.anonymization) && data.anonymization) || {
			anonymized: false,
			results: '',
		}

		this.language = (!Array.isArray(data.language) && data.language) || {
			code: '',
			level: '',
		}

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
			reference: z.string().max(255),
			title: z.string().min(1).max(255), // .min(1) on a string functionally works the same as a nonEmpty check (SHOULD NOT BE COMBINED WITH .OPTIONAL())
			summary: z.string().min(1).max(255),
			description: z.string().max(2555),
			labels: z.string().array(),
			accessURL: z.string().url(),
			downloadURL: z.string().url(),
			type: z.string(),
			extension: z.string().optional(), // .optional() is used in this context for read-only properties, so we can validate it when POSTing
			size: z.string().optional(),
			anonymization: z.object({
				anonymized: z.boolean(),
				results: z.string().max(2500),
			}),
			language: z.object({
				// this regex checks if the code has either 2 or 3 characters per group, and the -aaa after the first is optional
				code: z.string()
					.max(7)
					.regex(/([a-z]{2,3})(-[a-z]{2,3})?/g, 'language code is not a valid ISO 639-1 code (e.g. en-us)'),
				level: z.string().max(2).regex(/([a-zA-Z][0-9])/g, 'language level is not a valid level (e.g. A1)'),
			}),
			versionOf: z.string().uuid(),
			hash: z.string().optional(),
			published: z.string(),
			modified: z.string().optional(),
			license: z.string(),
		})

		const result = schema.safeParse({ ...this })

		return result.success
	}

}
