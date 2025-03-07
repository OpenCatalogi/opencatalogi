/* eslint-disable no-console */
import { Theme, TTheme } from '../../entities/index.js'
import { defineStore } from 'pinia'

const apiEndpoint = '/index.php/apps/opencatalogi/api/objects/theme'

interface Options {
    /**
     * Do not save the received item to the store, this can be enabled if API calls get run in a loop
     */
    doNotSetStore?: boolean
}

interface ThemeStoreState {
    themeItem: Theme;
    themeList: Theme[];
}

export const useThemeStore = defineStore('theme', {
	state: () => ({
		themeItem: null,
		themeList: [],
	} as ThemeStoreState),
	actions: {
		setThemeItem(themeItem: Theme | TTheme) {
			this.themeItem = themeItem && new Theme(themeItem)
			console.log('Active theme item set to ' + themeItem && themeItem?.id)
		},
		setThemeList(themeList: Theme[] | TTheme[]) {
			this.themeList = themeList.map(
				(themeItem) => new Theme(themeItem),
			)
			console.log('Theme list set to ' + themeList.length + ' items')
		},
		/* istanbul ignore next */ // ignore this for Jest until moved into a service
		async refreshThemeList(search: string = null) {
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
			const data = rawData.map((themeItem: TTheme) => new Theme(themeItem))

			this.setThemeList(data)

			return { response, data }
		},
		/* istanbul ignore next */
		async getAllThemes(options: Options = {}) {
			const response = await fetch(
				`${apiEndpoint}`,
				{ method: 'get' },
			)

			const rawData = await response.json()

			const data = rawData.results.map((themeItem: TTheme) => new Theme(themeItem))

			options.doNotSetStore !== true && this.setThemeList(data)

			return { response, data }
		},
		/* istanbul ignore next */
		async getOneTheme(id: number, options: Options = {}) {
			if (!id) {
				throw Error('Passed id is falsy')
			}

			const response = await fetch(
				`${apiEndpoint}/${id}`,
				{ method: 'get' },
			)

			const data = new Theme(await response.json())

			options.doNotSetStore !== true && this.setThemeItem(data)

			return { response, data }
		},
		/* istanbul ignore next */
		async addTheme(item: Theme) {
			if (!(item instanceof Theme)) {
				throw Error('Please pass a Theme item from the Theme class')
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

			const data = new Theme(await response.json())

			this.refreshThemeList()
			this.setThemeItem(data)

			return { response, data }
		},
		/* istanbul ignore next */
		async editTheme(themeItem: Theme) {
			if (!(themeItem instanceof Theme)) {
				throw Error('Please pass a Theme item from the Theme class')
			}

			const validateResult = themeItem.validate()
			if (!validateResult.success) {
				throw Error(validateResult.error.issues[0].message)
			}

			const response = await fetch(
				`${apiEndpoint}/${themeItem.id}`,
				{
					method: 'PUT',
					headers: {
						'Content-Type': 'application/json',
					},
					body: JSON.stringify(validateResult.data),
				},
			)

			const data = new Theme(await response.json())

			this.refreshThemeList()
			this.setThemeItem(data)

			return { response, data }
		},
		/* istanbul ignore next */
		async deleteTheme(id: number) {
			if (!id) {
				throw Error('Passed id is falsy')
			}

			const response = await fetch(
				`${apiEndpoint}/${id}`,
				{ method: 'DELETE' },
			)

			this.refreshThemeList()
			this.setThemeItem(null)

			return { response }
		},
	},
})
