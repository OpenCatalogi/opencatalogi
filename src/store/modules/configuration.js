/* eslint-disable no-console */
import { Configuration } from '../../entities/index.js'
import { defineStore } from 'pinia'

export const useConfigurationStore = defineStore('configuration', {
	state: () => ({
		configurationItem: false,
		configurationList: [],
	}),
	actions: {
		setConfigurationItem(catalogiItem) {
			this.catalogiItem = catalogiItem && new Configuration(catalogiItem)
			console.log('Active catalog item set to ' + catalogiItem && catalogiItem?.id)
		},
		async refreshConfiguration(search = null) {
			// @todo this might belong in a service?
			let endpoint = '/index.php/apps/opencatalogi/api/configuration'
			if (search !== null && search !== '') {
				endpoint = endpoint + '?_search=' + search
			}
			return fetch(endpoint, {
				method: 'GET',
			})
				.then((response) => {
					response.json().then((data) => {
						this.setConfigurationItem(data)
					})
				})
				.catch((err) => {
					console.error(err)
				})
		},
	},
})
