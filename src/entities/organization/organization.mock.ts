/**
 * Organization mock data for testing
 * @module Entities
 * @package
 * @author Ruben Linde
 * @copyright 2024
 * @license AGPL-3.0-or-later
 * @version 1.0.0
 * @see {@link https://github.com/opencatalogi/opencatalogi}
 */

import { Organization } from './organization'
import { TOrganization } from './organization.types'

/**
 * Mock organization data for testing
 * @return {TOrganization[]} Array of mock organization data
 */
export const mockOrganizationData = (): TOrganization[] => [
	{
		id: '1',
		name: 'Test Organization',
		summary: 'A test organization',
		description: 'This is a test organization for development purposes',
		oin: '00001234567890123456',
		tooi: '00001234567890123456',
		rsin: '00001234567890123456',
		pki: '00001234567890123456',
		image: 'https://example.com/image.jpg',
	},
	{
		id: '2',
		name: 'Minimal Organization',
		summary: '',
		description: '',
		oin: '',
		tooi: '',
		rsin: '',
		pki: '',
		image: '',
	},
]

/**
 * Creates Organization instances from mock data
 * @param {TOrganization[]} data Optional mock data to use instead of default
 * @return {Organization[]} Array of Organization instances
 */
export const mockOrganizations = (data: TOrganization[] = mockOrganizationData()): Organization[] =>
	data.map(item => new Organization(item))
