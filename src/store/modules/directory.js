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
			this.listingItem = new Listing(listingItem)
			console.log('Active directory item set to ' + listingItem.id)
		},
		setListingList(listingList) {
			this.listingList = listingList.map(
				(listingItem) => new Listing(listingItem),
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
							(listingItem) => new Listing(listingItem),
						)
					})
				})
				.catch((err) => {
					console.error(err)
				})
		},
	},
})
