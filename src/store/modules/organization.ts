/* eslint-disable no-console */
import { defineStore } from 'pinia'
import { Organization, TOrganization } from '../../entities/index.js'

const apiEndpoint = '/index.php/apps/opencatalogi/api/objects/organization'

interface OrganizationStoreState {
    organizationItem: Organization;
    organizationList: Organization[];
}

export const useOrganizationStore = defineStore('organization', {
	state: () => ({
		organizationItem: null,
		organizationList: [],
	} as OrganizationStoreState),
	actions: {
		setOrganizationItem(organizationItem: TOrganization | Organization) {
			this.organizationItem = organizationItem && new Organization(organizationItem)
			console.log('Active organization item set to ' + organizationItem && organizationItem?.id)
		},
		setOrganizationList(organizationList: TOrganization[] | Organization[]) {
			this.organizationList = organizationList.map(
				(organizationItem) => new Organization(organizationItem),
			)
			console.log('Organization list set to ' + organizationList.length + ' items')
		},
		/* istanbul ignore next */
		async refreshOrganizationList(search: string = null) {
			// @todo this might belong in a service?
			let endpoint = apiEndpoint
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
		/* istanbul ignore next */
		async getAllOrganization() {
			const response = await fetch(
				`${apiEndpoint}`,
				{ method: 'get' },
			)

			const rawData = await response.json()

			const data = rawData.results.map((organization: TOrganization) => new Organization(organization))

			this.organizationList = data

			return { response, data }
		},
		/* istanbul ignore next */
		async getOneOrganization(id: number) {
			if (!id) {
				throw Error('Passed id is falsy')
			}

			const response = await fetch(
				`${apiEndpoint}/${id}`,
				{ method: 'get' },
			)

			const data = new Organization(await response.json())

			this.setOrganizationItem(data)

			return { response, data }
		},
		/* istanbul ignore next */
		async addOrganization(organizationItem: Organization) {
			if (!(organizationItem instanceof Organization)) {
				throw Error('Please pass a Organization item from the Organization class')
			}

			const validateResult = organizationItem.validate()
			if (!validateResult.success) {
				throw Error(validateResult.error.issues[0].message)
			}

			const response = await fetch(apiEndpoint,
				{
					method: 'POST',
					headers: {
						'Content-Type': 'application/json',
					},
					body: JSON.stringify(validateResult.data),
				},
			)

			const data = new Organization(await response.json())

			this.refreshOrganizationList()
			this.setOrganizationItem(data)

			return { response, data }
		},
		/* istanbul ignore next */
		async editOrganization(organizationItem: Organization) {
			if (!(organizationItem instanceof Organization)) {
				throw Error('Please pass a Organization item from the Organization class')
			}

			const validateResult = organizationItem.validate()
			if (!validateResult.success) {
				throw Error(validateResult.error.issues[0].message)
			}

			const response = await fetch(
				`${apiEndpoint}/${organizationItem.id}`,
				{
					method: 'PUT',
					headers: {
						'Content-Type': 'application/json',
					},
					body: JSON.stringify(validateResult.data),
				},
			)

			const data = new Organization(await response.json())

			this.refreshOrganizationList()
			this.setOrganizationItem(data)

			return { response, data }
		},
		async saveOrganization(organizationItem: Organization) {
			if (!(organizationItem instanceof Organization)) {
				throw Error('Please pass a Organization item from the Organization class')
			}

			const validateResult = organizationItem.validate()
			if (!validateResult.success) {
				throw Error(validateResult.error.issues[0].message)
			}

			const createNew = !organizationItem.id
			const endpoint = createNew ? apiEndpoint : `${apiEndpoint}/${organizationItem.id}`
			const method = createNew ? 'POST' : 'PUT'

			console.info(`${createNew ? 'Creating' : 'Updating'} organization`)

			const response = await fetch(
				endpoint,
				{
					method,
					headers: {
						'Content-Type': 'application/json',
					},
					body: JSON.stringify(validateResult.data),
				},
			)

			const data = new Organization(await response.json())

			this.refreshOrganizationList()
			this.setOrganizationItem(data)

			return { response, data }
		},
		/* istanbul ignore next */
		async deleteOrganization(id: number) {
			if (!id) {
				throw Error('Passed id is falsy')
			}

			const response = await fetch(
				`${apiEndpoint}/${id}`,
				{ method: 'DELETE' },
			)

			this.refreshOrganizationList()
			this.setOrganizationItem(null)

			return { response }
		},
	},
})
