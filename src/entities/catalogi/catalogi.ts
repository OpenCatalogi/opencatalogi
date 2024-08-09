import { SafeParseReturnType, z } from 'zod'
import { TCatalogi } from './catalogi.types'

export class Catalogi implements TCatalogi {

	public id: string
	public title: string
	public summary: string
	public description: string
	public image: string
	public listed: boolean
	public organisation: {
        id: string
        title: string
        summary: string
        description: string
        oin: string
        tooi: string
        rsin: string
        pki: string
    }

	public metadata: string[]

	constructor(data: TCatalogi) {
		this.hydrate(data)
	}

	/* istanbul ignore next */ // Jest does not recognize the code coverage of these 2 methods
	private hydrate(data: TCatalogi) {
		this.id = data?.id?.toString() || ''
		this.title = data?.title || ''
		this.summary = data?.summary || ''
		this.description = data?.description || ''
		this.image = data?.image || ''
		this.listed = data?.listed || false
		this.organisation = data.organisation || {
			id: '',
			title: '',
			summary: '',
			description: '',
			oin: '',
			tooi: '',
			rsin: '',
			pki: '',
		}
		this.metadata = (Array.isArray(data.metadata) && data.metadata) || []
	}

	/* istanbul ignore next */
	public validate(): SafeParseReturnType<TCatalogi, unknown> {
		// https://conduction.stoplight.io/docs/open-catalogi/8azwyic71djee-create-listing
		const schema = z.object({
			title: z.string().min(1).max(255), // .min(1) on a string functionally works the same as a nonEmpty check (SHOULD NOT BE COMBINED WITH .OPTIONAL())
			summary: z.string().min(1).max(255),
			description: z.string().max(2555),
			image: z.string().max(255),
			listed: z.boolean(),
			organisation: z.object({
				title: z.string(),
				summary: z.string(),
				description: z.string(),
				oin: z.string(),
				tooi: z.string(),
				rsin: z.string(),
				pki: z.string(),
			}),
			metadata: z.string().array(),
		})

		const result = schema.safeParse({
			...this,
		})

		return result
	}

}
