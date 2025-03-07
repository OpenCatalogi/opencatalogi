import { SafeParseReturnType, z } from 'zod'
import { TOrganization } from './organization.types'

export class Organization implements TOrganization {

	public id: string
	public title: string
	public summary: string
	public description: string
	public oin: string
	public tooi: string
	public rsin: string
	public pki: string
	public image: string

	constructor(data: TOrganization) {
		this.hydrate(data)
	}

	/* istanbul ignore next */ // Jest does not recognize the code coverage of these 2 methods
	private hydrate(data: TOrganization) {
		this.id = data?.id?.toString() || ''
		this.title = data?.title || ''
		this.summary = data?.summary || ''
		this.description = data?.description || ''
		this.oin = data?.oin || ''
		this.tooi = data?.tooi || ''
		this.rsin = data?.rsin || ''
		this.pki = data?.pki || ''
		this.image = data?.image || ''
	}

	/* istanbul ignore next */
	public validate(): SafeParseReturnType<TOrganization, unknown> {
		// https://conduction.stoplight.io/docs/open-catalogi/ewlydzkylhygj-create-organization
		const schema = z.object({
			title: z.string().min(1, 'is verplicht'),
			summary: z.string().min(1, 'is verplicht'),
			description: z.string(),
			// Regex could be wrong since there is no clear public information on the format of any of these.
			oin: z.string().regex(/^0000000\d{10}000$/, 'is niet een geldige OIN nummer').or(z.literal('')),
			tooi: z.string().regex(/^\w{2,}\d{4}$/, 'is niet een geldige TOOI nummer').or(z.literal('')),
			rsin: z.string().regex(/^\d{9}$/, 'is niet een geldige RSIN nummer').or(z.literal('')),
			pki: z.string().regex(/^\d{1,}$/, 'is niet een geldige PKI nummer').or(z.literal('')),
			image: z.string(),
		})

		const result = schema.safeParse({
			...this,
		})

		return result
	}

}
