/**
 * Catalogi entity class
 * @module Entities
 * @package
 * @author Ruben Linde
 * @copyright 2024
 * @license AGPL-3.0-or-later
 * @version 1.0.0
 * @see {@link https://github.com/opencatalogi/opencatalogi}
 */

import { SafeParseReturnType, z } from 'zod'
import { TCatalogi } from './catalogi.types'

export class Catalogi implements TCatalogi {

	public id: string
	public title: string
	public summary: string
	public description: string
	public image: string
	public listed: boolean
	public organization: string
	public registers: string[]
	public schemas: string[]
	public filters: Record<string, unknown>

	constructor(data: TCatalogi) {
		this.hydrate(data)
	}

	/* istanbul ignore next */ // Jest does not recognize the code coverage of these 2 methods
	private hydrate(data: TCatalogi): void {
		this.id = data?.id?.toString() || ''
		this.title = data?.title || ''
		this.summary = data?.summary || ''
		this.description = data?.description || ''
		this.image = data?.image || ''
		this.listed = data?.listed || false
		this.organization = data.organization || ''
		this.registers = (Array.isArray(data.registers) && data.registers) || []
		this.schemas = (Array.isArray(data.schemas) && data.schemas) || []
		this.filters = data.filters || {}
	}

	/* istanbul ignore next */
	public validate(): SafeParseReturnType<TCatalogi, unknown> {
		// https://conduction.stoplight.io/docs/open-catalogi/l89lv7ocvq848-create-catalog
		const schema = z.object({
			title: z.string()
				.min(1, 'is verplicht') // .min(1) on a string functionally works the same as a nonEmpty check (SHOULD NOT BE COMBINED WITH .OPTIONAL())
				.max(255, 'kan niet langer dan 255 zijn'),
			summary: z.string().max(255, 'kan niet langer dan 255 zijn'),
			description: z.string().max(2555, 'kan niet langer dan 2555 zijn'),
			image: z.string().max(255, 'kan niet langer dan 255 zijn'),
			listed: z.boolean(),
			organization: z.number().or(z.string()).or(z.null()),
			registers: z.array(z.number().or(z.string())).min(1, 'is verplicht'),
			schemas: z.array(z.number().or(z.string())).min(1, 'is verplicht'),
			filters: z.record(z.unknown()),
		})

		const result = schema.safeParse({
			...this,
		})

		return result
	}

}
