import { SafeParseReturnType, z } from 'zod'
import { TPublicationType } from './publicationType.types'
export class PublicationType implements TPublicationType {

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
        pattern: string
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

	constructor(data: TPublicationType) {
		this.hydrate(data)
	}

	/* istanbul ignore next */ // Jest does not recognize the code coverage of these 2 methods
	private hydrate(data: TPublicationType) {
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
			} as Pick<TPublicationType['properties'][0], 'minimum' | 'maximum' | 'multipleOf' | 'minItems' | 'maxItems' | 'minLength' | 'maxLength'>

			Object.keys(defaultPropertiesProps).forEach((key: keyof typeof defaultPropertiesProps) => {
				if (this.properties[obj][key] === null) {
					this.properties[obj][key] = defaultPropertiesProps[key]
				}
			})
		})
	}

	/* istanbul ignore next */
	public validate(): SafeParseReturnType<TPublicationType, unknown> {
		// https://conduction.stoplight.io/docs/open-catalogi/5og7tj13bkzj5-create-metadata
		const propertiesDataSchema = z.object({
			title: z.string().min(1, 'is verplicht'),
			description: z.string(),
			type: z.enum(['string', 'number', 'integer', 'object', 'array', 'boolean', 'dictionary']),
			format: z.enum(['date', 'time', 'duration', 'date-time', 'url', 'uri', 'uuid', 'email', 'idn-email', 'hostname', 'idn-hostname', 'ipv4', 'ipv6', 'uri-reference', 'iri', 'iri-reference', 'uri-template', 'json-pointer', 'regex', 'binary', 'byte', 'password', 'rsin', 'kvk', 'bsn', 'oidn', 'telephone'])
				.or(z.literal('')).or(z.null()), // in practice I have found this being able to be both '' and null
			pattern: z.string(),
			default: z.union([z.string(), z.boolean()]),
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
			title: z.string().min(1, 'is verplicht'), // .min(1) on a string functionally works the same as a nonEmpty check (SHOULD NOT BE COMBINED WITH .OPTIONAL())
			description: z.string(),
			summary: z.string().min(1, 'is verplicht'),
			version: z.string(),
			required: z.string().array(),
			properties: z.record(propertiesDataSchema), // z.record allows for any amount of any keys, with specific type for value validation
			archive: z.object({
				valuation: z.enum(['b', 'v', 'n'], { message: "kan alleen 'b', 'v', of 'n' zijn" }),
				class: z.number().refine((data: number) => {
					return [1, 2, 3, 4, 5].includes(data)
				}, 'kan alleen 1, 2, 3, 4 of 5 zijn'),
			}),
			source: z.string().url('is niet een url').or(z.literal('')),
		})

		const result = schema.safeParse({
			...this,
		})

		return result
	}

}
