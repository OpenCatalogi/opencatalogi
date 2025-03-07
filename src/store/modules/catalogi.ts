/* eslint-disable no-console */
import { defineStore } from 'pinia'
import { Catalogi, TCatalogi } from '../../entities/index.js'

const apiEndpoint = '/index.php/apps/opencatalogi/api/objects/catalog'

interface Options {
    /**
     * Do not save the received item to the store, this can be enabled if API calls get run in a loop
     */
    doNotSetStore?: boolean
}

interface CatalogiStoreState {
    catalogiItem: Catalogi;
    catalogiList: Catalogi[];
}

export const useCatalogiStore = defineStore('catalogi', {
	state: () => ({
		catalogiItem: null,
		catalogiList: [],
	} as CatalogiStoreState),
	actions: {
		setCatalogiItem(catalogiItem: TCatalogi) {
			this.catalogiItem = catalogiItem && new Catalogi(catalogiItem)
			console.log('Active catalog item set to ' + catalogiItem && catalogiItem?.id)
		},
		setCatalogiList(catalogiList: TCatalogi[]) {
			this.catalogiList = catalogiList.map(
				(catalogiItem) => new Catalogi(catalogiItem),
			)
			console.log('Catalogi list set to ' + catalogiList.length + ' item')
		},
		/* istanbul ignore next */
		async refreshCatalogiList(search: string = null) {
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
						this.setCatalogiList(data.results)
					})
				})
				.catch((err) => {
					console.error(err)
				})
		},
		/* istanbul ignore next */
		async getAllCatalogi(options: Options = {}) {
			const response = await fetch(
				`${apiEndpoint}`,
				{ method: 'get' },
			)

			const rawData = await response.json()

			const data = rawData.results.map((catalogiItem: TCatalogi) => new Catalogi(catalogiItem))

			options.doNotSetStore !== true && this.setCatalogiList(data)

			return { response, data }
		},
		/* istanbul ignore next */
		async getOneCatalogi(id: number, options: Options = {}) {
			if (!id) {
				// throw Error('Passed id is falsy')
				return { response: null, data: null }
			}

			const response = await fetch(
				`${apiEndpoint}/${id}`,
				{ method: 'get' },
			)

			const data = new Catalogi(await response.json())

			options.doNotSetStore !== true && this.setCatalogiItem(data)

			return { response, data }
		},
		/* istanbul ignore next */
		async addCatalogi(item: Catalogi) {
			if (!(item instanceof Catalogi)) {
				throw Error('Please pass a Catalogi item from the Catalogi class')
			}

			const validateResult = item.validate()
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

			const data = new Catalogi(await response.json())

			this.refreshCatalogiList()
			this.setCatalogiItem(data)

			return { response, data }
		},
		/* istanbul ignore next */
		async editCatalogi(catalogiItem: Catalogi) {
			if (!(catalogiItem instanceof Catalogi)) {
				throw Error('Please pass a Catalogi item from the Catalogi class')
			}

			const validateResult = catalogiItem.validate()
			if (!validateResult.success) {
				throw Error(validateResult.error.issues[0].message)
			}

			const response = await fetch(
				`${apiEndpoint}/${catalogiItem.id}`,
				{
					method: 'PUT',
					headers: {
						'Content-Type': 'application/json',
					},
					body: JSON.stringify(validateResult.data),
				},
			)

			const data = new Catalogi(await response.json())

			this.refreshCatalogiList()
			this.setCatalogiItem(data)

			return { response, data }
		},
		/* istanbul ignore next */
		async deleteCatalogi(id: number) {
			if (!id) {
				throw Error('Passed id is falsy')
			}

			const response = await fetch(
				`${apiEndpoint}/${id}`,
				{ method: 'DELETE' },
			)

			this.refreshCatalogiList()
			this.setCatalogiItem(null)

			return { response }
		},
	},
})
