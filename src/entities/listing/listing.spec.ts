/* eslint-disable no-console */
import { Listing } from './listing'
import { TListing } from './listing.types'

describe('Listing Store', () => {
	it('create Listing entity with full data', () => {
		const listing = new Listing(testData[0])

		expect(listing).toBeInstanceOf(Listing)
		expect(listing).toEqual(testData[0])

		expect(listing.validate()).toBe(true)
	})

	it('create Listing entity with partial data', () => {
		const listing = new Listing(testData[1])

		expect(listing).toBeInstanceOf(Listing)
		expect(listing.id).toBe(testData[1].id)
		expect(listing.title).toBe(testData[1].title)
		expect(listing.summary).toBe(testData[1].summary)
		expect(listing.description).toBe(testData[1].description)
		expect(listing.search).toBe('')
		expect(listing.directory).toBe(testData[1].directory)
		expect(listing.metadata).toBe('')
		expect(listing.status).toBe('')
		expect(listing.lastSync).toBe(testData[1].lastSync)
		expect(listing.default).toBe(testData[1].default)
		expect(listing.available).toBe(testData[1].available)

		expect(listing.validate()).toBe(true)
	})

	it('create Listing entity with falsy data', () => {
		const listing = new Listing(testData[2])

		expect(listing).toBeInstanceOf(Listing)
		expect(listing).toEqual(testData[2])

		expect(listing.validate()).toBe(false)
	})
})

const testData: TListing[] = [
	{ // full data
		id: '1',
		title: 'test 1',
		summary: 'a short form summary',
		description: 'a really really long description about this catalogus',
		search: 'string',
		directory: 'string',
		metadata: 'string',
		status: 'active',
		lastSync: '2024-07-25T00:00:00Z',
		default: 'no',
		available: 'yes',
	},
	{ // partial data
		id: '2',
		title: 'test 2',
		summary: 'a short form summary',
		description: 'a really really long description about this catalogus',
		directory: 'string',
		lastSync: '2024-07-25T00:00:00Z',
		default: 'yes',
		available: 'no',
	},
	{ // invalid data
		id: '3',
		title: '',
		summary: 'a short form summary',
		description: 'a really really long description about this catalogus',
		search: 'string',
		directory: 'string',
		metadata: 'string',
		status: 'pending',
		lastSync: '2024-07-25T00:00:00Z',
		default: 'no',
		available: 'yes',
	},
]
