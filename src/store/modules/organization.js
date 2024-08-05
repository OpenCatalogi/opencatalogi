/* eslint-disable no-console */
import { Organisation } from '../../entities/index.js'
import { defineStore } from 'pinia'

export const useOrganizationStore = defineStore('organization', {
	state: () => ({
		organizationItem: false,
		organizationList: [],
	}),
	actions: {
		setCOrganisationItem(organizationItem) {
			this.organizationItem = organizationItem && new Organisation(organizationItem)
			console.log('Active theme item set to ' + organizationItem && organizationItem?.id)
		},
		setOrganisationList(organizationList) {
			this.organizationList = organizationList.map(
				(organizationItem) => new Organisation(organizationItem),
			)
			console.log('Organisation list set to ' + organizationList.length + ' items')
		},
		async refreshOrganisationList(search = null) {
			// @todo this might belong in a service?
			let endpoint = '/index.php/apps/opencatalogi/api/organisations'
			if (search !== null && search !== '') {
				endpoint = endpoint + '?_search=' + search
			}
			return fetch(endpoint, {
				method: 'GET',
			})
				.then((response) => {
					response.json().then((data) => {
						this.organizationList = data.results.map(
							(organizationItem) => new Organisation(organizationItem),
						)
					})
				})
				.catch((err) => {
					console.error(err)
				})
		},
	},
})
