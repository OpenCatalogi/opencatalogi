import { TPublication } from './publication.types'

export class Publication implements TPublication {

	public id: string
	public title: string
	public summary: string
	public reference?: string
	public description?: string
	public image?: string
	public category?: string
	public portal?: string
	public catalogi?: string
	public metaData?: string
	public publicationDate?: string
	public modified?: string
	public featured?: boolean
	public organization?: object[]
	public data?: object[]
	public attachments?: string[]
	public attachmentCount?: number
	public schema?: string
	public status?: string
	public license?: string
	public themes?: string
	public anonymization?: {
        anonymized?: string
        results?: string
    }

	public language?: {
        code?: string
        level?: string
    }

	constructor(data: TPublication) {
		this.hydrate(data)
	}

	/* istanbul ignore next */ // Jest does not recognize the code coverage of these 2 methods
	private hydrate(data: TPublication) {
		this.id = data.id || ''
		this.title = data.title || ''
		this.summary = data.summary || ''
		this.reference = data.reference || ''
		this.description = data.description || ''
		this.image = data.image || ''
		this.category = data.category || ''
		this.portal = data.portal || ''
		this.catalogi = data.catalogi || ''
		this.metaData = data.metaData || ''
		this.publicationDate = data.publicationDate || ''
		this.modified = data.modified || ''
		this.featured = data.featured || false
		this.organization = data.organization || []
		this.data = data.data || []
		this.attachments = data.attachments || []
		this.attachmentCount = data.attachmentCount || 0
		this.schema = data.schema || ''
		this.status = data.status || ''
		this.license = data.license || ''
		this.themes = data.themes || ''
		this.anonymization = data.anonymization || {}
	}

	/* istanbul ignore next */
	public validate(): boolean {
		// these have to exist
		if (!this.id || typeof this.id !== 'string') return false
		if (!this.title || typeof this.title !== 'string') return false
		if (!this.summary || typeof this.summary !== 'string') return false
		// these can be optional but if they exist, they must be of the right type
		if (!this.reference && typeof this.reference !== 'string') return false
		if (!this.description && typeof this.description !== 'string') return false
		if (!this.image && typeof this.image !== 'string') return false
		if (!this.category && typeof this.category !== 'string') return false
		if (!this.portal && typeof this.portal !== 'string') return false
		if (!this.catalogi && typeof this.catalogi !== 'string') return false
		if (!this.metaData && typeof this.metaData !== 'string') return false
		if (!this.publicationDate && typeof this.publicationDate !== 'string') return false
		if (!this.modified && typeof this.modified !== 'string') return false
		if (!this.featured && typeof this.featured !== 'boolean') return false
		if (!this.organization && !Array.isArray(this.organization)) return false
		if (!this.data && !Array.isArray(this.data)) return false
		if (!this.attachments && !Array.isArray(this.attachments)) return false
		if (!this.attachmentCount && typeof this.attachmentCount !== 'number') return false
		if (!this.schema && typeof this.schema !== 'string') return false
		if (!this.status && typeof this.status !== 'string') return false
		if (!this.license && typeof this.license !== 'string') return false
		if (!this.themes && typeof this.themes !== 'string') return false
		if (!this.anonymization && typeof this.anonymization !== 'object') return false
		return true
	}

}
