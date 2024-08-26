import { SafeParseReturnType, z } from 'zod'
import { TMetadata } from './metadata.types'

export class Metadata implements TMetadata {

	public id: string
	public title: string
	public description: string
	public summary: string
	public version: string
	public required: string[]
	public properties: Record<string, {
        title: string
        description: string
        type: 'string' | 'number' | 'integer' | 'object' | 'array' | 'boolean' | 'dictionary'
        format: 'date' | 'time' | 'duration' | 'date-time' | 'url' | 'uri' | 'uuid' | 'email' | 'idn-email' | 'hostname' | 'idn-hostname' | 'ipv4' | 'ipv6' | 'uri-reference' | 'iri' | 'iri-reference' | 'uri-template' | 'json-pointer' | 'regex' | 'binary' | 'byte' | 'password' | 'rsin' | 'kvk' | 'bsn' | 'oidn' | 'telephone'
        pattern: number
        default: string
        behavior: string
        required: boolean
        deprecated: boolean
        minLength: number
        maxLength: number
        example: string
        minimum: number
        maximum: number
        multipleOf: number
        exclusiveMin: boolean
        exclusiveMax: boolean
        minItems: number
        maxItems: number
    }>

	public archive: {
        valuation: 'b' | 'v' | 'n'
        class: 1 | 2 | 3 | 4 | 5
    }

	public source: string

	constructor(data: TMetadata) {
		this.hydrate(data)
	}

	/* istanbul ignore next */ // Jest does not recognize the code coverage of these 2 methods
	private hydrate(data: TMetadata) {
		this.id = data?.id?.toString() || ''
		this.title = data?.title || ''
		this.description = data?.description || ''
		this.summary = data?.summary || ''
		this.version = data?.version || ''
		this.required = data?.required || []
		// backend (PHP) doesn't know objects so it will return an array if empty
		this.properties = (!Array.isArray(data?.properties) && data?.properties) || {}
		this.archive = (!Array.isArray(data?.archive) && data?.archive) || {
			valuation: 'n',
			class: 1,
		}
		this.source = data?.source || ''

		// convert null's to the respective default value from predefined list of props
		Object.keys(this.properties).forEach(obj => {
			const defaultPropertiesProps = {
				minimum: 0,
				maximum: 0,
				multipleOf: 0,
				minItems: 0,
				maxItems: 0,
				minLength: 0,
				maxLength: 0,
			} as Pick<TMetadata['properties'][0], 'minimum' | 'maximum' | 'multipleOf' | 'minItems' | 'maxItems' | 'minLength' | 'maxLength'>

			Object.keys(defaultPropertiesProps).forEach((key: keyof typeof defaultPropertiesProps) => {
				if (this.properties[obj][key] === null) {
					this.properties[obj][key] = defaultPropertiesProps[key]
				}
			})
		})
	}

	/* istanbul ignore next */
	public validate(): SafeParseReturnType<TMetadata, unknown> {
		// https://conduction.stoplight.io/docs/open-catalogi/5og7tj13bkzj5-create-metadata
		const propertiesDataSchema = z.object({
			title: z.string().min(1),
			description: z.string(),
			type: z.enum(['string', 'number', 'integer', 'object', 'array', 'boolean', 'dictionary']),
			format: z.enum(['date', 'time', 'duration', 'date-time', 'url', 'uri', 'uuid', 'email', 'idn-email', 'hostname', 'idn-hostname', 'ipv4', 'ipv6', 'uri-reference', 'iri', 'iri-reference', 'uri-template', 'json-pointer', 'regex', 'binary', 'byte', 'password', 'rsin', 'kvk', 'bsn', 'oidn', 'telephone'])
				.or(z.literal('')),
			pattern: z.number(),
			default: z.string(),
			behavior: z.string(),
			required: z.boolean(),
			deprecated: z.boolean(),
			minLength: z.number(),
			maxLength: z.number(),
			example: z.string(),
			minimum: z.number(),
			maximum: z.number(),
			multipleOf: z.number(),
			exclusiveMin: z.boolean(),
			exclusiveMax: z.boolean(),
			minItems: z.number(),
			maxItems: z.number(),
		})

		const schema = z.object({
			title: z.string().min(1), // .min(1) on a string functionally works the same as a nonEmpty check (SHOULD NOT BE COMBINED WITH .OPTIONAL())
			description: z.string(),
			summary: z.string(),
			version: z.string(),
			required: z.string().array(),
			properties: z.record(propertiesDataSchema), // z.record allows for any amount of any keys, with specific type for value validation
			archive: z.object({
				valuation: z.enum(['b', 'v', 'n']),
				class: z.number().refine((data: number) => {
					return [1, 2, 3, 4, 5].includes(data)
				}),
			}),
			source: z.string().min(1).url(),
		})

		const result = schema.safeParse({
			...this,
		})

		return result
	}

}
