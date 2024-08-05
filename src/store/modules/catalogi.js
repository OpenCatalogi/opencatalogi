/* eslint-disable no-console */
import { defineStore } from 'pinia'
import { Catalogi } from '../../entities/index.js'

export const useCatalogiStore = defineStore('catalogi', {
	state: () => ({
		catalogiItem: false,
		catalogiList: [],
	}),
	actions: {
		setCatalogiItem(catalogiItem) {
			this.catalogiItem = catalogiItem && new Catalogi(catalogiItem)
			console.log('Active catalog item set to ' + catalogiItem && catalogiItem?.id)
		},
		setCatalogiList(catalogiList) {
			this.catalogiList = catalogiList.map(
				(catalogiItem) => new Catalogi(catalogiItem),
			)
			console.log('Catalogi list set to ' + catalogiList.length + ' item')
		},
		async refreshCatalogiList(search = null) {
			// @todo this might belong in a service?
			let endpoint = '/index.php/apps/opencatalogi/api/catalogi'
			if (search !== null && search !== '') {
				endpoint = endpoint + '?_search=' + search
			}
			return fetch(endpoint, {
				method: 'GET',
			})
				.then((response) => {
					response.json().then((data) => {
						this.catalogiList = data.results.map(
							(catalogiItem) => new Catalogi(catalogiItem),
						)
					})
				})
				.catch((err) => {
					console.error(err)
				})
		},
	},
})
