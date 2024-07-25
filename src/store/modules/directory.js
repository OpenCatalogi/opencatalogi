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
			// To prevent forms etc from braking we alway use a default/skeleton object
			const listingDefault = {
				title: '',
				summary: '',
				status: '',
				lastSync: '',
			}
			this.listingItem = { ...listingDefault, ...listingItem }
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
