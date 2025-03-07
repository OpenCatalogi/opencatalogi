/* eslint-disable no-console */
import { defineStore } from 'pinia'
import { Configuration, TConfiguration } from '../../entities/index.js'

interface ConfigurationStoreState {
    configurationItem: Configuration;
    configurationList: Configuration[];
}

export const useConfigurationStore = defineStore('configuration', {
	state: () => ({
		configurationItem: null,
		configurationList: [],
	} as ConfigurationStoreState),
	actions: {
		setConfigurationItem(configurationItem: TConfiguration) {
			this.configurationItem = configurationItem && new Configuration(configurationItem)
			console.log('Active configuration item set to ' + configurationItem)
		},
		/* istanbul ignore next */ // ignore this for Jest until moved into a service
		async refreshConfiguration(search: string = null) {
			// @todo this might belong in a service?
			let endpoint = '/index.php/apps/opencatalogi/api/configuration'
			if (search !== null && search !== '') {
				endpoint = endpoint + '?_search=' + search
			}
			return fetch(
				endpoint, {
					method: 'GET',
				},
			)
				.then((response) => {
					response.json().then(
						(data) => {
							this.setConfigurationItem(data)
						},
					)
				})
				.catch(
					(err) => {
						console.error(err)
					},
				)
		},
	},
})
