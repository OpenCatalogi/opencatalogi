/* eslint-disable @typescript-eslint/no-explicit-any */
import { TOrganization } from '../organization'
import { TListing } from './listing.types'
import { SafeParseReturnType, z } from 'zod'

export class Listing implements TListing {

	public id: string
	public catalogusId: string
	public title: string
	public summary: string
	public description: string
	public search: string
	public directory: string
	public metadata: string[]
	public status: string
	public statusCode: number
	public lastSync: string | Date
	public available: boolean
	public default: boolean
	public organization: string|TOrganization
	public publicationTypes: any[]
	constructor(data: TListing) {
		this.hydrate(data)
	}

	/* istanbul ignore next */ // Jest does not recognize the code coverage of these 2 methods
	private hydrate(data: TListing) {
		this.id = data.id?.toString()
		this.catalogusId = data.catalogusId || ''
		this.title = data.title || ''
		this.summary = data.summary || ''
		this.description = data.description || ''
		this.search = data.search || ''
		this.directory = data.directory || ''
		this.metadata = data.metadata || []
		this.status = data.status || ''
		this.statusCode = data.statusCode || 0
		this.lastSync = data.lastSync || ''
		this.available = data.available || true
		this.default = data.default || false
		this.organization = data.organization || ''
		this.publicationTypes = data.publicationTypes || []
	}

	/* istanbul ignore next */
	public validate(): SafeParseReturnType<TListing, unknown> {
		// https://conduction.stoplight.io/docs/open-catalogi/8azwyic71djee-create-listing
		const schema = z.object({
			catalogusId: z.string(),
			title: z.string().min(1), // .min(1) on a string functionally works the same as a nonEmpty check (SHOULD NOT BE COMBINED WITH .OPTIONAL())
			summary: z.string().min(1),
			description: z.string(),
			search: z.string().url().or(z.literal('')),
			directory: z.string().url().or(z.literal('')),
			metadata: z.string().array(),
			status: z.string(),
			statusCode: z.number().min(200),
			lastSync: z.string().datetime().or(z.literal('')),
			available: z.boolean(),
			default: z.boolean(),
		})

		const result = schema.safeParse({
			...this,
		})

		return result
	}

}
