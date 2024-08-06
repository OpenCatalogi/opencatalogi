/* eslint-disable no-console */
import { Organisation } from '../../entities/index.js'
import { defineStore } from 'pinia'

export const useOrganisationStore = defineStore('organisation', {
	state: () => ({
		organisationItem: false,
		organisationList: [],
	}),
	actions: {
		setOrganisationItem(organisationItem) {
			this.organisationItem = organisationItem && new Organisation(organisationItem)
			console.log('Active theme item set to ' + organisationItem && organisationItem?.id)
		},
		setOrganisationList(organisationList) {
			this.organisationList = organisationList.map(
				(organisationItem) => new Organisation(organisationItem),
			)
			console.log('Organisation list set to ' + organisationList.length + ' items')
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
						this.organisationList = data.results.map(
							(organisationItem) => new Organisation(organisationItem),
						)
					})
				})
				.catch((err) => {
					console.error(err)
				})
		},
	},
})
