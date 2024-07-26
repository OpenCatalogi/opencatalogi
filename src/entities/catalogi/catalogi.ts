import { TCatalogi } from './catalogi.types'

export class Catalogi implements TCatalogi {

	public id: string
	public title: string
	public summary: string
	public description?: string
	public image?: string
	public search?: string

	constructor(data: TCatalogi) {
		this.hydrate(data)
	}

	/* istanbul ignore next */ // Jest does not recognize the code coverage of these 2 methods
	private hydrate(data: TCatalogi) {
		this.id = data?.id || ''
		// @ts-expect-error data.name is not supposed to exist but you can still get it from the backend, so this is just backwards compatibility
		this.title = data?.title || data?.name || ''
		this.summary = data?.summary || ''
		this.description = data?.description || ''
		this.image = data?.image || ''
		this.search = data?.search || ''
	}

	/* istanbul ignore next */
	public validate(): boolean {
		// these have to exist
		if (!this.id || typeof this.id !== 'string') return false
		if (!this.title || typeof this.title !== 'string') return false
		if (!this.summary || typeof this.summary !== 'string') return false
		// these can be optional
		if (typeof this.description !== 'string') return false
		if (typeof this.image !== 'string') return false
		if (typeof this.search !== 'string') return false
		return true
	}

}
