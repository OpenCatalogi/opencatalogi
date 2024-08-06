import { TConfiguration } from './configuration.types'

export class Configuration implements TConfiguration {

	public useElastic?: boolean
	public useMongo?: boolean

	constructor(data: TConfiguration) {
		this.hydrate(data)
	}

	/* istanbul ignore next */ // Jest does not recognize the code coverage of these 2 methods
	private hydrate(data: TConfiguration) {
		this.useElastic = data?.useElastic || false
		this.useMongo = data?.useMongo || false
	}

	/* istanbul ignore next */
	public validate(): boolean {
		// these have to exist
		if (!this.useElastic || typeof this.useElastic !== 'boolean') return false
		if (!this.useMongo || typeof this.useMongo !== 'boolean') return false
		return true
	}

}
