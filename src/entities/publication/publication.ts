import { TPublication } from './publication.types'

export class Publication implements TPublication {

	public id: string
	public title: string
	public summary: string
	public description?: string
	public reference?: string
	public image?: string
	public category: string
	public catalogi: string
	public metaData: string
	public portal?: string
	public featured?: boolean
	public organization?: {
        type?: string
        $ref?: string
        format?: string
        description?: string
    }

	public schema?: string
	public status?: string
	public attachments?: {
        type?: string
        items?: {
            $ref?: string
        }
        format?: string
    }

	public attachmentCount?: number
	public themes?: string[]
	public data?: {
        type?: string
        required?: boolean
    }

	public anonymization?: {
        type?: string
        $ref?: string
        format?: string
        description?: string
    }

	public languageObject?: {
        type?: string
        $ref?: string
        format?: string
        description?: string
    }

	public published?: string | Date
	public modified?: string | Date
	public license?: string

	constructor(data: TPublication) {
		this.hydrate(data)
	}

	/* istanbul ignore next */ // Jest does not recognize the code coverage of these 2 methods
	private hydrate(data: TPublication) {
		this.id = data.id?.toString() || ''
		this.title = data.title || ''
		this.summary = data.summary || ''
		this.reference = data.reference || ''
		this.description = data.description || ''
		this.image = data.image || ''
		this.category = data.category || ''
		this.catalogi = data.catalogi || ''
		// @ts-expect-error -- for backwards compatibility metaData will be used if metadata cannot be found
		this.metaData = (data.metadata ?? data.metaData) || ''
		this.portal = data.portal || ''
		this.featured = (typeof data.featured === 'boolean' && data.featured)
            // backend can send true and false back as "1" and "" (yes. not "0")
            // FIXME: remove once bug is fixed
            || (typeof data.featured === 'string' && !!parseInt(data.featured))
            || false
		this.organization = (!Array.isArray(data.organization) && data.organization) || {}
		this.schema = data.schema || ''
		this.status = data.status || ''
		this.attachments = (!Array.isArray(data.attachments) && data.attachments) || {}
		this.attachmentCount = data.attachmentCount || 0
		this.themes = data.themes || []
		this.data = (!Array.isArray(data.data) && data.data) || {}
		this.anonymization = (!Array.isArray(data.anonymization) && data.anonymization) || {}
		this.languageObject = (!Array.isArray(data.languageObject) && data.languageObject) || {}
		this.published = new Date(data.published) || ''
		this.modified = new Date(data.modified) || ''
		this.license = data.license || ''
	}

	/* istanbul ignore next */
	public validate(): boolean {
		// TODO: change this over to Zod schema, already exists on 'feature/DIMOC-101/entities' (requires slight modification)
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
		if (!this.published && typeof this.published !== 'string') return false
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
