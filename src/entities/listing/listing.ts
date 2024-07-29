import { TListing } from './listing.types'
import { z } from 'zod'

export class Listing implements TListing {

	public id: string
	public title: string
	public summary: string
	public description?: string
	public search?: string
	public directory?: string
	public metadata?: string
	public status?: string
	public lastSync?: string
	public default?: string
	public available?: string

	constructor(data: TListing) {
		this.hydrate(data)
	}

	/* istanbul ignore next */ // Jest does not recognize the code coverage of these 2 methods
	private hydrate(data: TListing) {
		this.id = data?.id?.toString() || ''
		this.title = data?.title || ''
		this.summary = data?.summary || ''
		this.description = data?.description || ''
		this.search = data?.search || ''
		this.directory = data?.directory || ''
		this.metadata = data?.metadata || ''
		this.status = data?.status || ''
		this.lastSync = data?.lastSync || ''
		this.default = data?.default || ''
		this.available = data?.available || ''
	}

	/* istanbul ignore next */
	public validate(): boolean {
		// https://conduction.stoplight.io/docs/open-catalogi/etf6dier69xua-listing
		const schema = z.object({
			title: z.string().min(1),
			summary: z.string().optional(),
			description: z.string().optional(),
			search: z.string().url().optional(),
			directory: z.string().url().optional(),
			metadata: z.string().optional(),
			status: z.string().optional(),
			lastSync: z.string().optional(),
			default: z.string().optional(),
			available: z.string().optional(),
		})

		const result = schema.safeParse({ ...this })

		return result.success
	}

}
