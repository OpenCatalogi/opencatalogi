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
		this.id = data?.id?.toString() || ''
		this.title = data?.title || ''
		this.description = data?.description || ''
		this.version = data?.version || ''
		this.required = data?.required || []
		this.properties = data?.properties || []
	}

	/* istanbul ignore next */
	public validate(): boolean {
		// https://conduction.stoplight.io/docs/open-catalogi/92e81a078982b-metadata
		const schema = z.object({
			title: z.string().min(1), // .min(1) on a string functionally works the same as a nonEmpty check (SHOULD NOT BE COMBINED WITH .OPTIONAL())
			description: z.string().optional(),
			version: z.string().optional(),
			required: z.string().array().optional(),
			properties: z.object({
				title: z.string().min(1),
				description: z.string().optional(),
				type: z.string().optional(),
				format: z.string().optional(),
				pattern: z.number().optional(),
				default: z.string().optional(),
				behavior: z.string().optional(),
				required: z.boolean().optional(),
				deprecated: z.boolean().optional(),
				minLength: z.number().optional(),
				maxLength: z.number().optional(),
				example: z.string().optional(),
				minimum: z.number().optional(),
				maximum: z.number().optional(),
				multipleOf: z.number().optional(),
				exclusiveMin: z.boolean().optional(),
				exclusiveMax: z.boolean().optional(),
				minItems: z.number().optional(),
				maxItems: z.number().optional(),
			}).array().optional(),
		})

		const result = schema.safeParse({
			id: this.id,
			title: this.title,
			description: this.description,
			version: this.version,
			required: this.required,
			properties: this.properties,
		})

		return result.success
	}

}
