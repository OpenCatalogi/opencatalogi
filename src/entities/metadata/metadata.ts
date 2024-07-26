import { TMetadata } from './metadata.types'
import { z } from 'zod'

export class Metadata implements TMetadata {

	public id: string
	public title: string
	public description?: string
	public version?: string
	public required?: string[]

	public properties: {
        id: string
        title: string
        description?: string
        type?: 'string' | 'number' | 'integer' | 'object' | 'array' | 'boolean' | 'dictionary'
        format?: 'date' | 'time' | 'duration' | 'date-time' | 'url' | 'uri' | 'uuid' | 'email' | 'idn-email' | 'hostname' | 'idn-hostname' | 'ipv4' | 'ipv6' | 'uri-reference' | 'iri' | 'iri-reference' | 'uri-template' | 'json-pointer' | 'regex' | 'binary' | 'byte' | 'password' | 'rsin' | 'kvk' | 'bsn' | 'oidn' | 'telephone'
        pattern?: number
        default?: string
        behavior?: string
        required?: boolean
        deprecated?: boolean
        minLength?: number
        maxLength?: number
        example?: string
        minimum?: number
        maximum?: number
        multipleOf?: number
        exclusiveMin?: boolean
        exclusiveMax?: boolean
        minItems?: number
        maxItems?: number
    }[]

	constructor(data: TMetadata) {
		this.hydrate(data)
	}

	/* istanbul ignore next */ // Jest does not recognize the code coverage of these 2 methods
	private hydrate(data: TMetadata) {
		this.id = data?.id || ''
		this.title = data?.title || ''
		this.description = data?.description || ''
		this.version = data?.version || ''
		this.required = data?.required || []
		this.properties = data?.properties || []
	}

	/* istanbul ignore next */
	public validate(): boolean {
		const schema = z.object({
			id: z.string(),
			title: z.string(),
			description: z.string().optional(),
			version: z.string().optional(),
			required: z.string().array().optional(),
			properties: z.object({
				id: z.string(),
				title: z.string(),
			}).array().optional(),
		})

		// these have to exist
		if (!this.id || typeof this.id !== 'string') return false
		if (!this.title || typeof this.title !== 'string') return false
		// these can be optional
		if (typeof this.description !== 'string') return false
		if (typeof this.version !== 'string') return false
		if (!Array.isArray(this.required)) return false
		if (!Array.isArray(this.properties)) return false
		if (this.properties.length > 0 && this.properties.some((item) => Array.isArray(item) || typeof item !== 'object')) return false
		return true
	}

}
