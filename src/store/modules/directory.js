/* eslint-disable no-console */
import { defineStore } from 'pinia'
import { Listing } from '../../entities/index.js'

export const useDirectoryStore = defineStore(
	'directory', {
		state: () => ({
			listingItem: false,
			listingList: [],
		}),
		actions: {
			setListingItem(listingItem) {
				this.listingItem = listingItem && new Listing(listingItem)
				console.log('Active directory item set to ' + listingItem.id)
			},
			setListingList(listingList) {
				this.listingList = listingList.map(
					(listingItem) => new Listing(listingItem),
				)
				console.log('Active directory item set to ' + listingList.length)
			},
			async refreshListingList(search = null) {
				// @todo this might belong in a service?
				let endpoint = '/index.php/apps/opencatalogi/api/directory'
				if (search !== null && search !== '') {
					endpoint = endpoint + '?_search=' + search
				}
				return fetch(
					endpoint, {
						method: 'GET',
					},
				)
					.then(
						(response) => {
							response.json().then(
								(data) => {
									this.listingList = data.results.map(
										(listingItem) => {
											listingItem.organisation = listingItem?.organisation ? listingItem.organisation : null
											return new Listing(listingItem)
										},
									)
								},
							)
						},
					)
					.catch(
						(err) => {
							console.error(err)
						},
					)
			},
		},
		setListingList(listingList) {
			this.listingList = listingList.map(
				(listingItem) => new Listing(listingItem),
			)
			console.log('Active directory item set to ' + listingList.length)
		},
		/* istanbul ignore next */ // ignore this for Jest until moved into a service
		async refreshListingList(search = null) {
			// @todo this might belong in a service?
			let endpoint = '/index.php/apps/opencatalogi/api/directory'
			if (search !== null && search !== '') {
				endpoint = endpoint + '?_search=' + search
			}
			return fetch(endpoint, {
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
)
