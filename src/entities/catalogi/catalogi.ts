import { TCatalogi } from './catalogi.types'

export class Catalogi {

	public id: string
	public name: string
	public summary: string
	public description: string
	public image: string
	public search: string

	constructor(data: TCatalogi) {
		this.hydrate(data)
	}

	private hydrate(data: TCatalogi) {
		this.id = data?.id || ''
		this.name = data?.name || ''
		this.summary = data?.summary || ''
		this.description = data?.description || ''
		this.image = data?.image || ''
		this.search = data?.search || ''
	}

}
