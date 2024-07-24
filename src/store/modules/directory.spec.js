/* eslint-disable no-console */
import { setActivePinia, createPinia } from 'pinia'

import { useDirectoryStore } from './directory.js'

describe('Directory Store', () => {
	beforeEach(() => {
		setActivePinia(createPinia())
	})

	it('sets listing item correctly', () => {
		const store = useDirectoryStore()
		const listingItem = {
			id: '1',
			title: 'Test Listing',
			summary: 'This is a test listing',
			description: 'this is a very long description for test listing',
			search: 'search',
			directory: 'a directory',
			metadata: 'this is a metadata',
			status: '200',
			lastSync: '2024-07-23',
			default: 'true',
			available: 'true',
		}

		store.setListingItem(listingItem)

		expect(store.listingItem).toEqual(listingItem)
	})

	it('sets listings list correctly', () => {
		const store = useDirectoryStore()
		const listingList = [
			{
				id: '1',
				title: 'Test Listing',
				summary: 'This is a test listing',
				description: 'this is a very long description for test listing',
				search: 'search',
				directory: 'a directory',
				metadata: 'this is a metadata',
				status: '200',
				lastSync: '2024-07-23',
				default: 'true',
				available: 'true',
			},
			{
				id: '2',
				title: 'Test Listing fewsfwes',
				summary: 'This is a test listing gfsa',
				description: 'this is a very long description for test listing gdfgsdfg',
				search: 'search sf hd',
				directory: 'a directory hfgrfds',
				metadata: 'this is a metadata hggsf',
				status: '500',
				lastSync: '2024-06-12',
				default: 'true',
				available: 'false',
			},
		]

		store.setListingList(listingList)

		expect(store.listingList).toHaveLength(listingList.length)
		store.listingList.forEach((item, index) => {
			expect(item).toEqual(listingList[index])
		})
	})
})
