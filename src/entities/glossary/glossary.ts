/**
 * Glossary entity class
 * @module Entities
 * @package
 * @author Ruben Linde
 * @copyright 2024
 * @license AGPL-3.0-or-later
 * @version 1.0.0
 * @see {@link https://github.com/opencatalogi/opencatalogi}
 */

import { SafeParseReturnType, z } from 'zod'
import { TGlossary } from './glossary.types'

export class Glossary implements TGlossary {

	public id: string
	public title: string
	public summary: string
	public description: string
	public externalLink: string
	public keywords: string[]

	constructor(data: TGlossary) {
		this.hydrate(data)
	}

	private hydrate(data: TGlossary) {
		this.id = data?.id?.toString() || ''
		this.title = data?.title || ''
		this.summary = data?.summary || ''
		this.description = data?.description || ''
		this.externalLink = data?.externalLink || ''
		this.keywords = (Array.isArray(data.keywords) && data.keywords) || []
	}

	public validate(): SafeParseReturnType<TGlossary, unknown> {
		const schema = z.object({
			title: z.string()
				.min(1, 'is verplicht')
				.max(255, 'kan niet langer dan 255 zijn'),
			summary: z.string().max(255, 'kan niet langer dan 255 zijn'),
			description: z.string().max(2555, 'kan niet langer dan 2555 zijn'),
			externalLink: z.string().url('moet een geldige URL zijn').max(255, 'kan niet langer dan 255 zijn'),
			keywords: z.string().array(),
		})

		return schema.safeParse({
			...this,
		})
	}

}
