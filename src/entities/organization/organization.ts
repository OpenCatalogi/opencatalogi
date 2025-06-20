/**
 * Organization entity class
 * @module Entities
 * @package
 * @author Ruben Linde
 * @copyright 2024
 * @license AGPL-3.0-or-later
 * @version 1.0.0
 * @see {@link https://github.com/opencatalogi/opencatalogi}
 */

import { SafeParseReturnType, z } from 'zod'
import { TOrganization } from './organization.types'

/**
 * Organization class representing an organization in the system
 */
export class Organization implements TOrganization {

	/**
	 * Unique identifier of the organization
	 */
	public id: string

	/**
	 * Name of the organization
	 */
	public name: string

	/**
	 * Brief summary of the organization
	 */
	public summary: string

	/**
	 * Detailed description of the organization
	 */
	public description: string

	/**
	 * Organization Identification Number (OIN)
	 */
	public oin: string

	/**
	 * TOOI identifier for the organization
	 */
	public tooi: string

	/**
	 * RSIN number for tax identification
	 */
	public rsin: string

	/**
	 * PKI certificate information
	 */
	public pki: string

	/**
	 * URL to the organization's logo or image
	 */
	public image: string

	/**
	 * Creates a new Organization instance
	 * @param data - Organization data
	 */
	constructor(data: TOrganization) {
		this.hydrate(data)
	}

	/**
	 * Hydrates the organization instance with data
	 * @param data - Organization data to hydrate with
	 * @private
	 */
	private hydrate(data: TOrganization) {
		this.id = data?.id?.toString() || ''
		this.name = data?.name || ''
		this.summary = data?.summary || ''
		this.description = data?.description || ''
		this.oin = data?.oin || ''
		this.tooi = data?.tooi || ''
		this.rsin = data?.rsin || ''
		this.pki = data?.pki || ''
		this.image = data?.image || ''
	}

	/**
	 * Validates the organization data
	 * @return SafeParseReturnType containing validation results
	 */
	public validate(): SafeParseReturnType<TOrganization, unknown> {
		// https://conduction.stoplight.io/docs/open-catalogi/ewlydzkylhygj-create-organization
		const schema = z.object({
			name: z.string().min(1, 'is verplicht'),
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
