/* eslint-disable no-console */
import { Page, TPage } from '../../entities/index.js'
import { defineStore } from 'pinia'

const apiEndpoint = '/index.php/apps/opencatalogi/api/objects/page'

interface Options {
    /**
     * Do not save the received item to the store, this can be enabled if API calls get run in a loop
     */
    doNotSetStore?: boolean
}

interface PageStoreState {
    pageItem: Page;
    pageList: Page[];
    contentId: string;
}

export const usePageStore = defineStore('page', {
	state: () => ({
		pageItem: null,
		pageList: [],
		contentId: null,
	} as PageStoreState),
	actions: {
		setPageItem(pageItem: Page | TPage) {
			this.pageItem = pageItem && new Page(pageItem as TPage)
			console.log('Active page item set to ' + pageItem && pageItem?.id)
		},
		setPageList(pageList: Page[] | TPage[]) {
			this.pageList = pageList.map(
				(pageItem) => new Page(pageItem as TPage),
			)
			console.log('Page list set to ' + pageList.length + ' items')
		},
		/* istanbul ignore next */ // ignore this for Jest until moved into a service
		async refreshPageList(search: string = null) {
			// @todo this might belong in a service?
			let endpoint = apiEndpoint
			if (search !== null && search !== '') {
				endpoint = endpoint + '?_search=' + search
			}
			const response = await fetch(
				endpoint, {
					method: 'GET',
				},
			)
			const rawData = (await response.json()).results
			const data = rawData.map((pageItem: TPage) => new Page(pageItem))

			this.setPageList(data)

			return { response, data }
		},
		/* istanbul ignore next */
		async getAllPages(options: Options = {}) {
			const response = await fetch(
				`${apiEndpoint}`,
				{ method: 'get' },
			)

			const rawData = await response.json()

			const data = rawData.results.map((pageItem: TPage) => new Page(pageItem))

			options.doNotSetStore !== true && this.setPageList(data)

			return { response, data }
		},
		/* istanbul ignore next */
		async getOnePage(id: number, options: Options = {}) {
			if (!id) {
				throw Error('Passed id is falsy')
			}

			const response = await fetch(
				`${apiEndpoint}/${id}`,
				{ method: 'get' },
			)

			const data = new Page(await response.json())

			options.doNotSetStore !== true && this.setPageItem(data)

			return { response, data }
		},
		/* istanbul ignore next */
		async savePage(pageItem: Page) {
			if (!(pageItem instanceof Page)) {
				throw Error('Please pass a Page item from the Page class')
			}

			const validateResult = pageItem.validate()
			if (!validateResult.success) {
				throw Error(validateResult.error.issues[0].message)
			}

			const isEdit = !!pageItem.id
			const endpoint = isEdit ? `${apiEndpoint}/${pageItem.id}` : apiEndpoint

			const method = isEdit ? 'PUT' : 'POST'

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

			const data = new Page(await response.json())

			this.refreshPageList()
			this.setPageItem(data)

			return { response, data }
		},
		/* istanbul ignore next */
		async deletePage(id: number) {
			if (!id) {
				throw Error('Passed id is falsy')
			}

			const response = await fetch(
				`${apiEndpoint}/${id}`,
				{ method: 'DELETE' },
			)

			this.refreshPageList()
			this.setPageItem(null)

			return { response }
		},
	},
})
