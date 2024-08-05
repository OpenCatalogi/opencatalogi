/* eslint-disable no-console */
import { setActivePinia, createPinia } from 'pinia'

import { useDirectoryStore } from './directory.js'
import { Listing } from '../../entities/index.js'

describe('Directory Store', () => {
	beforeEach(() => {
		setActivePinia(createPinia())
	})

	it('sets listing item correctly', () => {
		const store = useDirectoryStore()

		store.setListingItem(testData[0])

		expect(store.listingItem).toBeInstanceOf(Listing)
		expect(store.listingItem).toEqual(testData[0])
	})

	it('sets listings list correctly', () => {
		const store = useDirectoryStore()

		store.setListingList(testData)

		expect(store.listingList).toHaveLength(testData.length)

		// list item 1
		expect(store.listingList[0]).toBeInstanceOf(Listing)
		expect(store.listingList[0]).toEqual(testData[0])

		expect(store.listingList[0].validate()).toBe(true)

		// list item 2
		expect(store.listingList[1]).toBeInstanceOf(Listing)
		expect(store.listingList[1].id).toBe(testData[1].id)
		expect(store.listingList[1].title).toBe(testData[1].title)
		expect(store.listingList[1].summary).toBe(testData[1].summary)
		expect(store.listingList[1].description).toBe(testData[1].description)
		expect(store.listingList[1].search).toBe('')
		expect(store.listingList[1].directory).toBe(testData[1].directory)
		expect(store.listingList[1].metadata).toBe('')
		expect(store.listingList[1].status).toBe('')
		expect(store.listingList[1].lastSync).toBe(testData[1].lastSync)
		expect(store.listingList[1].default).toBe(testData[1].default)
		expect(store.listingList[1].available).toBe(testData[1].available)

		expect(store.listingList[1].validate()).toBe(true)

		// list item 3
		expect(store.listingList[2]).toBeInstanceOf(Listing)
		expect(store.listingList[2].id).toBe(testData[2].id)
		expect(store.listingList[2].title).toBe('')
		expect(store.listingList[2].summary).toBe(testData[2].summary)
		expect(store.listingList[2].description).toBe(testData[2].description)
		expect(store.listingList[2].search).toBe(testData[2].search)
		expect(store.listingList[2].directory).toBe(testData[2].directory)
		expect(store.listingList[2].metadata).toBe(testData[2].metadata)
		expect(store.listingList[2].status).toBe(testData[2].status)
		expect(store.listingList[2].lastSync).toBe(testData[2].lastSync)
		expect(store.listingList[2].default).toBe(testData[2].default)
		expect(store.listingList[2].available).toBe(testData[2].available)

		expect(store.listingList[2].validate()).toBe(false)
	})
})

const testData = [
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
