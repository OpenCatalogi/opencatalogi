import { TAttachment } from './attachment.types'

export class Attachment implements TAttachment {

	public id: string
	public reference?: string
	public title: string
	public summary: string
	public description?: string
	public labels?: object[]
	public accessURL?: string
	public downloadURL?: string
	public type?: string
	public extension?: string
	public size?: number
	public anonymization?: {
        anonymized?: string
        results?: string
    }

	public language?: {
        code?: string
        level?: string
    }

	public versionOf?: string
	public hash?: string
	public published?: string
	public modified?: string
	public license?: string

	constructor(data: TAttachment) {
		this.hydrate(data)
	}

	/* istanbul ignore next */ // Jest does not recognize the code coverage of these 2 methods
	private hydrate(data: TAttachment) {
		this.id = data.id || ''
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
		this.anonymization = data.anonymization || {}
		this.language = data.language || {}
		this.versionOf = data.versionOf || ''
		this.hash = data.hash || ''
		this.published = data.published || ''
		this.modified = data.modified || ''
		this.license = data.license || ''
	}

	/* istanbul ignore next */
	public validate(): boolean {
		// these have to exist
		if (!this.id || typeof this.id !== 'string') return false
		if (!this.title || typeof this.title !== 'string') return false
		if (!this.summary || typeof this.summary !== 'string') return false
		// these can be optional but if they exist, they must be of the right type
		if (this.description !== undefined && typeof this.description !== 'string') return false
		if (this.reference !== undefined && typeof this.reference !== 'string') return false
		if (this.labels !== undefined && !Array.isArray(this.labels)) return false
		if (this.accessURL !== undefined && typeof this.accessURL !== 'string') return false
		if (this.downloadURL !== undefined && typeof this.downloadURL !== 'string') return false
		if (this.type !== undefined && typeof this.type !== 'string') return false
		if (this.extension !== undefined && typeof this.extension !== 'string') return false
		if (this.size !== undefined && typeof this.size !== 'number') return false
		if (this.anonymization !== undefined && typeof this.anonymization !== 'object') return false
		if (this.language !== undefined && typeof this.language !== 'object') return false
		if (this.versionOf !== undefined && typeof this.versionOf !== 'string') return false
		if (this.hash !== undefined && typeof this.hash !== 'string') return false
		if (this.published !== undefined && typeof this.published !== 'string') return false
		if (this.modified !== undefined && typeof this.modified !== 'string') return false
		if (this.license !== undefined && typeof this.license !== 'string') return false
		return true
	}

}
