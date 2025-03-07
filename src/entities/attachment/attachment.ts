import { TAttachment } from './attachment.types'
import { SafeParseReturnType, z } from 'zod'

type TStatus = 'Concept' | 'Published' | 'Withdrawn' | 'Archived' | 'Revised' | 'Rejected'

export class Attachment implements TAttachment {

	public id: string
	public reference: string
	public title: string
	public summary: string
	public description: string
	public labels: string[]
	public accessUrl: string
	public downloadUrl: string
	public status: TStatus
	public type: string
	public extension: string
	public size: string
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
	public published: string | Date | null
	public modified: string | Date
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
		this.accessUrl = data.accessUrl || ''
		this.downloadUrl = data.downloadUrl || ''
		this.status = data.status as TStatus || 'Concept'
		this.type = data.type || ''
		this.extension = data.extension || ''
		this.size = data.size || ''
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
		this.published = data.published || null
		this.modified = data.modified || ''
		this.license = data.license || ''
	}

	/* istanbul ignore next */
	public validate(): SafeParseReturnType<TAttachment, unknown> {
		// https://conduction.stoplight.io/docs/open-catalogi/lsigtx7cafbr7-create-attachment
		const schema = z.object({
			reference: z.string().max(255, 'kan niet langer dan 255 zijn'),
			title: z.string().max(255, 'kan niet langer dan 255 zijn'), // .min(1) on a string functionally works the same as a nonEmpty check (SHOULD NOT BE COMBINED WITH .OPTIONAL())
			summary: z.string().max(255, 'kan niet langer dan 255 zijn'),
			description: z.string().max(2555, 'kan niet langer dan 2555 zijn'),
			labels: z.string().array(),
			accessUrl: z.string().url('is niet een url').or(z.literal('')),
			downloadUrl: z.string().url('is niet een url').or(z.literal('')),
			status: z.enum(['Concept', 'Published', 'Withdrawn', 'Archived', 'Revised', 'Rejected']),
			type: z.string(),
			anonymization: z.object({
				anonymized: z.boolean().or(z.enum(['true', 'false'])), // because the backend turns booleans into strings for some stupid reason
				results: z.string().max(2500, 'kan niet langer dan 2500 zijn'),
			}),
			language: z.object({
				// this regex checks if the code has either 2 or 3 characters per group, and the -aaa after the first is optional
				code: z.string()
					.regex(/^([a-z]{2,3})(-[a-z]{2,3})?$/g, 'is niet een geldige ISO 639-1 code (e.g. en-us)')
					.or(z.literal('')),
				level: z.string()
					.regex(/^(A|B|C)(1|2)$/g, 'is niet een geldige CEFRL level (e.g. A1)')
					.or(z.literal('')),
			}),
			versionOf: z.string(),
			published: z.string().datetime({ offset: true, message: 'is niet een geldige date-time' }).or(z.null()),
			license: z.string(),
		})

		const result = schema.safeParse({
			...this,
		})

		return result
	}

}
