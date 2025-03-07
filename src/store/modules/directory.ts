/* eslint-disable no-console */
import { defineStore } from 'pinia'
import { Listing, TListing } from '../../entities/index.js'

interface DirectoryStoreState {
    listingItem: Listing;
    listingList: Listing[];
}

const apiEndpoint = '/index.php/apps/opencatalogi/api/directory'

export const useDirectoryStore = defineStore('directory', {
	state: () => ({
		listingItem: null,
		listingList: [],
	} as DirectoryStoreState),
	actions: {
		setListingItem(listingItem: TListing) {
			this.listingItem = listingItem && new Listing(listingItem)
			console.log('Active directory item set to ' + listingItem.id)
		},
		setListingList(listingList: TListing[]) {
			this.listingList = listingList.map(
				(listingItem: TListing) => new Listing(listingItem),
			)
			console.log('Active directory item set to ' + listingList.length)
		},
		/* istanbul ignore next */
		async refreshListingList(search: string = null) {
			// @todo this might belong in a service?
			let endpoint = '/index.php/apps/opencatalogi/api/directory'
			if (search !== null && search !== '') {
				endpoint = endpoint + '?_search=' + search
			}
			return fetch(endpoint, { method: 'GET' })
				.then((response) => {
					response.json()
						.then((data) => {
							this.listingList = data.results.map(
								(listingItem: TListing) => {
									listingItem.organization = listingItem?.organization ? listingItem.organization : null
									return new Listing(listingItem)
								},
							)
						})
				})
				.catch((err) => {
					console.error(err)
				})
		},
		/* istanbul ignore next */
		async getOneListing(id: number) {
			if (!id) {
				throw Error('Passed id is falsy')
			}

			const response = await fetch(
				`${apiEndpoint}/${id}`,
				{ method: 'get' },
			)

			const data = new Listing(await response.json())

			this.setListingItem(data)

			return { response, data }
		},
		/* istanbul ignore next */
		async deleteListing(id: number) {
			if (!id) {
				throw Error('Passed id is falsy')
			}

			const response = await fetch(
				`${apiEndpoint}/${id}`,
				{ method: 'DELETE' },
			)

			this.refreshListingList()
			this.setListingItem(null)

			return { response }
		},
	},
})
