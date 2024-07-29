import { TCatalogi } from './catalogi.types'
import { z } from 'zod'

export class Catalogi implements TCatalogi {

	public id: string
	public title: string
	public summary: string
	public description?: string
	public image?: string
	public search?: string

	constructor(data: TCatalogi) {
		this.hydrate(data)
	}

	/* istanbul ignore next */ // Jest does not recognize the code coverage of these 2 methods
	private hydrate(data: TCatalogi) {
		this.id = data?.id?.toString() || ''
		// @ts-expect-error data.name is not supposed to exist but you can still get it from the backend, so this is just backwards compatibility
		this.title = data?.title || data?.name || ''
		this.summary = data?.summary || ''
		this.description = data?.description || ''
		this.image = data?.image || ''
		this.search = data?.search || ''
	}

	/* istanbul ignore next */
	public validate(): boolean {
		// https://conduction.stoplight.io/docs/open-catalogi/pk8bsjw0539dv-catalogue
		const schema = z.object({
			title: z.string().min(25).max(255),
			summary: z.string().min(50).max(255),
			description: z.string().max(2500).optional(),
			image: z.union([
				z.string().url(),
				// check if string is base64 image
				z.string().regex(/^(data:image\/[a-z+.-]{1,};base64,){1}([0-9a-zA-Z+/]{4})*(([0-9a-zA-Z+/]{2}==)|([0-9a-zA-Z+/]{3}=))?$/g, 'image property is not base64 image string'),
			]).optional(),
			search: z.string().optional(),
		})

		const result = schema.safeParse({ ...this })

		return result.success
	}

}
