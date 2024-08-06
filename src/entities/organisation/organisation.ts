import { TOrganisation } from './organisation.types'

export class Organisation implements TOrganisation {

	public id: string
	public title: string
	public summary: string
	public description?: string
	public oin?: string
	public tooi?: string
	public rsin?: string
	public pki?: string

	constructor(data: TOrganisation) {
		this.hydrate(data)
	}

	/* istanbul ignore next */ // Jest does not recognize the code coverage of these 2 methods
	private hydrate(data: TOrganisation) {
		this.id = data?.id?.toString() || ''
		// @ts-expect-error data.name is not supposed to exist but you can still get it from the backend, so this is just backwards compatibility
		this.title = data?.title || data?.name || ''
		this.summary = data?.summary || ''
		this.description = data?.description || ''
		this.oin = data?.oin || ''
		this.tooi = data?.tooi || ''
		this.rsin = data?.rsin || ''
		this.pki = data?.pki || ''
	}

	/* istanbul ignore next */
	public validate(): boolean {
		// these have to exist
		if (!this.id || typeof this.id !== 'string') return false
		if (!this.title || typeof this.title !== 'string') return false
		if (!this.summary || typeof this.summary !== 'string') return false
		// these can be optional
		if (typeof this.description !== 'string') return false
		if (typeof this.oin !== 'string') return false
		if (typeof this.tooi !== 'string') return false
		if (typeof this.rsin !== 'string') return false
		if (typeof this.pki !== 'string') return false
		return true
	}

}
