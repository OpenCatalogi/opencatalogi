import { TListing } from './listing.types'

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
		this.id = data?.id || ''
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
		// these have to exist
		if (!this.id || typeof this.id !== 'string') return false
		if (!this.title || typeof this.title !== 'string') return false
		if (!this.summary || typeof this.summary !== 'string') return false
		// these can be optional
		if (typeof this.description !== 'string') return false
		if (typeof this.search !== 'string') return false
		if (typeof this.directory !== 'string') return false
		if (typeof this.metadata !== 'string') return false
		if (typeof this.status !== 'string') return false
		if (typeof this.lastSync !== 'string') return false
		if (typeof this.default !== 'string') return false
		if (typeof this.available !== 'string') return false
		return true
	}

}
