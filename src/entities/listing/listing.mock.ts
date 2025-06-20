/**
 * Listing mock data for testing
 * @module Entities
 * @package
 * @author Ruben Linde
 * @copyright 2024
 * @license AGPL-3.0-or-later
 * @version 1.0.0
 * @see {@link https://github.com/opencatalogi/opencatalogi}
 */

import { Listing } from './listing'
import { TListing } from './listing.types'

/**
 * Mock listing data for testing purposes
 * @return {TListing[]} Array of mock listing data
 */
export const mockListingData = (): TListing[] => [
	{
		id: '1',
		catalogusId: '24',
		title: 'Test Listing',
		summary: 'A test listing',
		description: 'This is a test listing for testing purposes',
		search: 'https://example.com/search',
		directory: 'https://example.com/directory',
		metadata: ['test', 'metadata'],
		status: 'active',
		statusCode: 200,
		lastSync: new Date().toISOString(),
		available: true,
		default: true,
		organization: '1',
		publicationTypes: [],
	},
	{
		id: '2',
		catalogusId: '24',
		title: 'Minimal Listing',
		summary: 'A minimal test listing',
		description: '',
		search: '',
		directory: '',
		metadata: [],
		status: 'inactive',
		statusCode: 200,
		lastSync: '',
		available: false,
		default: false,
		organization: '1',
		publicationTypes: [],
	},
]

/**
 * Creates Listing instances from mock data
 * @param {TListing[]} data Optional mock data to use instead of default
 * @return {Listing[]} Array of Listing instances
 */
export const mockListings = (data: TListing[] = mockListingData()): Listing[] =>
	data.map(item => new Listing(item))
