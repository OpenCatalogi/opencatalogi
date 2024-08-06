/* eslint-disable no-console */
import { Organization } from '../../entities/index.js'
import { defineStore } from 'pinia'

export const useOrganizationStore = defineStore('organization', {
	state: () => ({
		organizationItem: false,
		organizationList: [],
	}),
	actions: {
		setOrganizationItem(organizationItem) {
			this.organizationItem = organizationItem && new Organization(organizationItem)
			console.log('Active organization item set to ' + organizationItem && organizationItem?.id)
		},
		setOrganizationList(organizationList) {
			this.organizationList = organizationList.map(
				(organizationItem) => new Organization(organizationItem),
			)
			console.log('Organization list set to ' + organizationList.length + ' items')
		},
		/* istanbul ignore next */ // ignore this for Jest until moved into a service
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
						this.setOrganizationList(data.results)
					})
				})
				.catch((err) => {
					console.error(err)
				})
		},
	},
})
