/**
 * Listing entity class
 * @module Entities
 * @package
 * @author Ruben Linde
 * @copyright 2024
 * @license AGPL-3.0-or-later
 * @version 1.0.0
 * @see {@link https://github.com/opencatalogi/opencatalogi}
 */

/* eslint-disable @typescript-eslint/no-explicit-any */
import { TOrganization } from '../organization'
import { TListing } from './listing.types'
import { SafeParseReturnType, z } from 'zod'

/**
 * Listing class representing a catalog listing in the system
 */
export class Listing implements TListing {

	public id: string
	public catalogusId: string
	public title: string
	public summary: string
	public description: string
	public search: string
	public directory: string
	public metadata: string[]
	public status: string
	public statusCode: number
	public lastSync: string | Date
	public available: boolean
	public default: boolean
	public organization: string | TOrganization
	public publicationTypes: any[]

	/**
	 * Creates a new Listing instance
	 * @param data - Listing data
	 */
	constructor(data: TListing) {
		this.hydrate(data)
	}

	/**
	 * Hydrates the listing instance with data
	 * @param data - Listing data to hydrate with
	 * @private
	 */
	private hydrate(data: TListing) {
		this.id = data?.id?.toString() || ''
		this.catalogusId = data?.catalogusId || ''
		this.title = data?.title || ''
		this.summary = data?.summary || ''
		this.description = data?.description || ''
		this.search = data?.search || ''
		this.directory = data?.directory || ''
		this.metadata = data?.metadata || []
		this.status = data?.status || ''
		this.statusCode = data?.statusCode || 0
		this.lastSync = data?.lastSync || ''
		this.available = data?.available || true
		this.default = data?.default || false
		this.organization = data?.organization || ''
		this.publicationTypes = data?.publicationTypes || []
	}

	/**
	 * Validates the listing data
	 * @return SafeParseReturnType containing validation results
	 */
	public validate(): SafeParseReturnType<TListing, unknown> {
		// https://conduction.stoplight.io/docs/open-catalogi/8azwyic71djee-create-listing
		const schema = z.object({
			catalogusId: z.string(),
			title: z.string().min(1, 'is verplicht'),
			summary: z.string().min(1, 'is verplicht'),
			description: z.string(),
			search: z.string().url().or(z.literal('')),
			directory: z.string().url().or(z.literal('')),
			metadata: z.string().array(),
			status: z.string(),
			statusCode: z.number().min(200),
			lastSync: z.string().datetime().or(z.literal('')),
			available: z.boolean(),
			default: z.boolean(),
		})

		return schema.safeParse({
			...this,
		})
	}

}
