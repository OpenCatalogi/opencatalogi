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
			this.catalogiItem = new Catalogi(catalogiItem)
			console.log('Active catalog item set to ' + catalogiItem.id)
		},
		setCatalogiList(catalogiList) {
			this.catalogiList = catalogiList.map(
				(catalogiItem) => new Catalogi(catalogiItem),
			)
			console.log('Catalogi list set to ' + catalogiList.length + ' item')
		},
		refreshCatalogiList() {
			// @todo this might belong in a service?
			fetch('/index.php/apps/opencatalogi/api/catalogi', {
				method: 'GET',
			})
				.then((response) => {
					response.json().then((data) => {
						this.catalogiList = data.results.map(
							(catalogiItem) =>
								new Catalogi(catalogiItem),
						)
					})
				})
				.catch((err) => {
					console.error(err)
				})
		},
	},
})
