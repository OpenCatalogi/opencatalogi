/* eslint-disable no-console */
import { defineStore } from 'pinia'
import { Listing } from '../../entities/index.js'

export const useDirectoryStore = defineStore('directory', {
	state: () => ({
		listingItem: false,
		listingList: [],
	}),
	actions: {
		setListingItem(listingItem) {
			if (listingItem instanceof Listing) this.listingItem = listingItem
			else this.listingItem = createListingItem(listingItem)
			console.log('Active directory item set to ' + listingItem.id)
		},
		setListingList(listingList) {
			this.listingList = listingList.map(
				(listingItem) => createListingItem(listingItem),
			)
			console.log('Active directory item set to ' + listingList.length)
		},
		refreshListingList() {
			// @todo this might belong in a service?
			fetch('/index.php/apps/opencatalogi/api/directory', {
				method: 'GET',
			})
				.then((response) => {
					response.json().then((data) => {
						this.listingList = data.results.map(
							(listingItem) => createListingItem(listingItem),
						)
					})
				})
				.catch((err) => {
					console.error(err)
				})
		},
	},
})

const createListingItem = (listingItem) => {
	return new Listing(
		listingItem.id,
		listingItem.title,
		listingItem.summary,
		listingItem.description,
		listingItem.search,
		listingItem.directory,
		listingItem.metadata,
		listingItem.status,
		listingItem.lastSync,
		listingItem.default,
		listingItem.available,
		listingItem._schema,
		listingItem._id,
	)
}
