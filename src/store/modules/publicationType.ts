/* eslint-disable no-console */
import { defineStore } from 'pinia'
import { PublicationType, TPublicationType } from '../../entities/index.js'

const apiEndpoint = '/index.php/apps/opencatalogi/api/objects/publicationType'

interface PublicationTypeStoreState {
    publicationTypeItem: PublicationType;
    publicationTypeList: PublicationType[];
    publicationTypeDataKey: string;
}

export const usePublicationTypeStore = defineStore('publicationType', {
	state: () => ({
		publicationTypeItem: null,
		publicationTypeList: [],
		publicationTypeDataKey: null,
	} as PublicationTypeStoreState),
	actions: {
		setPublicationTypeItem(publicationTypeItem: TPublicationType) {
			// for backward compatibility
			if (!!publicationTypeItem && typeof publicationTypeItem?.properties === 'string') {
				publicationTypeItem.properties = JSON.parse(publicationTypeItem.properties)
			}

			this.publicationTypeItem = publicationTypeItem && new PublicationType(publicationTypeItem)

			console.log('Active publication type object set to ' + publicationTypeItem && publicationTypeItem?.id)
		},
		setPublicationTypeList(publicationTypeList: TPublicationType[]) {
			this.publicationTypeList = publicationTypeList.map(
				(publicationTypeItem) => new PublicationType(publicationTypeItem),
			)
			console.log('Active publication type lest set')
		},
		setPublicationTypeDataKey(publicationTypeDataKey: string) {
			this.publicationTypeDataKey = publicationTypeDataKey
			console.log('Active publication type data key set to ' + publicationTypeDataKey)
		},
		/* istanbul ignore next */ // ignore this for Jest until moved into a service
		async refreshPublicationTypeList(search: string = null) {
			// @todo this might belong in a service?
			let endpoint = apiEndpoint
			if (search !== null && search !== '') {
				endpoint = endpoint + '?_search=' + search
			}
			return fetch(endpoint, { method: 'GET' })
				.then((response) => {
					response.json().then((data) => {
						this.setPublicationTypeList(data.results)
					})
				})
				.catch((err) => {
					console.error(err)
					return err
				})
		},
		/* istanbul ignore next */
		async getAllPublicationTypes() {
			const response = await fetch(
				`${apiEndpoint}`,
				{ method: 'get' },
			)

			const rawData = await response.json()

			const data = rawData.results.map((item: TPublicationType) => new PublicationType(item))

			this.publicationTypeList = data

			return { response, data }
		},
		/* istanbul ignore next */
		async getOnePublicationType(id: number) {
			if (!id) {
				throw Error('Passed id is falsy')
			}

			const response = await fetch(
				`${apiEndpoint}/${id}`,
				{ method: 'get' },
			)

			const data = new PublicationType(await response.json())

			this.setPublicationTypeItem(data)

			return { response, data }
		},
		/* istanbul ignore next */
		async addPublicationType(publicationTypeItem: PublicationType) {
			if (!(publicationTypeItem instanceof PublicationType)) {
				throw Error('Please pass a PublicationType item from the PublicationType class')
			}

			const validateResult = publicationTypeItem.validate()
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

			const data = new PublicationType(await response.json())

			this.refreshPublicationTypeList()
			this.setPublicationTypeItem(data)

			return { response, data }
		},
		/* istanbul ignore next */
		async editPublicationType(publicationTypeItem: PublicationType) {
			if (!(publicationTypeItem instanceof PublicationType)) {
				throw Error('Please pass a PublicationType item from the PublicationType class')
			}

			const validateResult = publicationTypeItem.validate()
			if (!validateResult.success) {
				throw Error(validateResult.error.issues[0].message)
			}

			const response = await fetch(`${apiEndpoint}/${publicationTypeItem.id}`,
				{
					method: 'PUT',
					headers: {
						'Content-Type': 'application/json',
					},
					body: JSON.stringify(validateResult.data),
				},
			)

			const data = new PublicationType(await response.json())

			this.refreshPublicationTypeList()
			this.setPublicationTypeItem(data)

			return { response, data }
		},
		/* istanbul ignore next */
		async deletePublicationType(id: number) {
			if (!id) {
				throw Error('Passed id is falsy')
			}

			const response = await fetch(
				`${apiEndpoint}/${id}`,
				{ method: 'DELETE' },
			)

			this.refreshPublicationTypeList()
			this.setPublicationTypeItem(null)

			return { response }
		},
	},
},
)
