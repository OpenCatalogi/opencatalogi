/* eslint-disable @typescript-eslint/no-explicit-any */
import { TCatalogi } from '../'
import { TPublication } from './publication.types'
import { SafeParseReturnType, z } from 'zod'

type TStatus = 'Concept' | 'Published' | 'Withdrawn' | 'Archived' | 'Revised' | 'Rejected'

export class Publication implements TPublication {

	public id: string
	public title: string
	public summary: string
	public description: string
	public reference: string
	public image: string
	public category: string
	public portal: string
	public featured: boolean
	public source: string
	public status: TStatus
	public themes: string[]
	public organization: string
	public data: Record<string, unknown>

	public anonymization: {
        anonymized: boolean
        results: string
    }

	public language: {
        code: string
        level: string
    }

	public published: string | Date
	public modified: string | Date
	public license: string
	public archive: {
        date: string | Date
    }

	public geo: {
        type: 'Point' | 'LineString' | 'Polygon' | 'MultiPoint' | 'MultiLineString' | 'MultiPolygon'
        coordinates: [number, number]
    }

	public '@self'?: object

	public catalog: TCatalogi | any
	public register: number | null
	public schema: number | null

	constructor(data: TPublication) {
		this.hydrate(data)
	}

	/* istanbul ignore next */ // Jest does not recognize the code coverage of these 2 methods
	private hydrate(data: TPublication) {
		this.id = data.id?.toString()
		this.title = data.title || ''
		this.summary = data.summary || ''
		this.description = data.description || ''
		this.reference = data.reference || ''
		this.image = data.image || ''
		this.organization = data.organization || ''
		this.category = data.category || ''
		this.portal = data.portal || ''
		this.featured = (typeof data.featured === 'boolean' && data.featured)
            // backend can send true and false back as "1" and "" (yes. not "0")
            // FIXME: remove once bug is fixed
            || (typeof data.featured === 'string' && !!parseInt(data.featured))
            || false
		this.source = data.source || ''
		this.status = data.status as TStatus || 'Concept'
		this.themes = data.themes || []
		this.data = (!Array.isArray(data.data) && data.data) || {}

		this.anonymization = (!Array.isArray(data.anonymization) && data.anonymization) || {
			anonymized: false,
			results: '',
		}

		this.language = (!Array.isArray(data.language) && data.language) || {
			code: '',
			level: '',
		}

		this.published = data.published || ''
		this.modified = data.modified || ''
		this.license = data.license || ''
		this.archive = (!Array.isArray(data.archive) && data.archive) || {
			date: '',
		}

		this.geo = (!Array.isArray(data.geo) && data.geo) || {
			type: 'Point',
			coordinates: [0, 0],
		}

		this['@self'] = (!Array.isArray(data['@self']) && data['@self']) || {}

		this.catalog = (!Array.isArray(data.catalog) && data.catalog) || {}
		this.register = data.register || null
		this.schema = data.schema || null
	}

	/* istanbul ignore next */
	public validate(): SafeParseReturnType<TPublication, unknown> {
		// https://conduction.stoplight.io/docs/open-catalogi/jcrqsdtnjtx8v-create-publication
		const schema = z.object({
			title: z.string().min(1, 'is verplicht'), // .min(1) on a string functionally works the same as a nonEmpty check (SHOULD NOT BE COMBINED WITH .OPTIONAL())
			summary: z.string().min(1, 'is verplicht'),
			description: z.string(),
			reference: z.string(),
			image: z.string().url('is niet een url').or(z.literal('')), // its either a URL or empty
			category: z.string(),
			portal: z.string().url('is niet een url').or(z.literal('')),
			featured: z.boolean(),
			source: z.string(),
			organization: z.string(),
			status: z.enum(['Concept', 'Published', 'Withdrawn', 'Archived', 'Revised', 'Rejected']),
			themes: z.array(z.union([z.string(), z.number()])),
			data: z.record(z.string(), z.any()),
			anonymization: z.object({
				anonymized: z.boolean(),
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
			published: z.string().datetime({ offset: true }).or(z.literal('')),
			modified: z.string().datetime({ offset: true }).or(z.literal('')),
			license: z.string(),
			archive: z.object({
				date: z.string().datetime().or(z.literal('')),
			}),
			geo: z.object({
				type: z.enum(['Point', 'LineString', 'Polygon', 'MultiPoint', 'MultiLineString', 'MultiPolygon']),
				coordinates: z.tuple([z.number(), z.number()]),
			}),
			'@self': z.object({}).optional(),
			catalog: z.string().or(z.number()),
			register: z.union([z.number(), z.string()]).refine((val) => val.toString().length > 0, 'register is verplicht'),
			schema: z.union([z.number(), z.string()]).refine((val) => val.toString().length > 0, 'schema is verplicht'),
		})

		const result = schema.safeParse({
			...this,
		})

		return result
	}

}
