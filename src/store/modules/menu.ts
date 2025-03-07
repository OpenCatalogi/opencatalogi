/* eslint-disable no-console */
import { defineStore } from 'pinia'
import { Menu, TMenu } from '../../entities/index.js'

const apiEndpoint = '/index.php/apps/opencatalogi/api/objects/menu'

interface Options {
    /**
     * Do not save the received item to the store, this can be enabled if API calls get run in a loop
     */
    doNotSetStore?: boolean
}

interface MenuStoreState {
    menuItem: Menu;
    menuList: Menu[];
    menuItemsItemId: number;
}

/**
 * Store for managing menu data
 */
export const useMenuStore = defineStore('menu', {
	state: () => ({
		menuItem: null,
		menuList: [],
		menuItemsItemId: null,
	} as MenuStoreState),
	actions: {
		/**
		 * Set the active menu item
		 * @param menuItem - Menu item to set as active
		 */
		setMenuItem(menuItem: TMenu) {
			this.menuItem = menuItem && new Menu(menuItem)
			console.log('Active menu item set to ' + menuItem && menuItem?.id)
		},

		/**
		 * Set the list of menu items
		 * @param menuList - Array of menu items to set
		 */
		setMenuList(menuList: TMenu[]) {
			this.menuList = menuList.map(
				(menuItem) => new Menu(menuItem),
			)
			console.log('Menu list set to ' + menuList.length + ' items')
		},

		/* istanbul ignore next */
		async refreshMenuList(search: string = null) {
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
						this.setMenuList(data.results)
					})
				})
				.catch((err) => {
					console.error(err)
				})
		},

		/* istanbul ignore next */
		async getAllMenus(options: Options = {}) {
			const response = await fetch(
				`${apiEndpoint}`,
				{ method: 'get' },
			)

			const rawData = await response.json()

			const data = rawData.results.map((menuItem: TMenu) => new Menu(menuItem))

			options.doNotSetStore !== true && this.setMenuList(data)

			return { response, data }
		},

		/* istanbul ignore next */
		async getOneMenu(id: number, options: Options = {}) {
			if (!id) {
				return { response: null, data: null }
			}

			const response = await fetch(
				`${apiEndpoint}/${id}`,
				{ method: 'get' },
			)

			const data = new Menu(await response.json())

			options.doNotSetStore !== true && this.setMenuItem(data)

			return { response, data }
		},

		// Create or save a mapping from store
		async saveMenu(item: Menu) {
			if (!(item instanceof Menu)) {
				throw Error('Please pass a Menu item from the Menu class')
			}

			const validateResult = item.validate()
			if (!validateResult.success) {
				throw Error(validateResult.error.issues[0].message)
			}

			console.info('Saving mapping...')

			const isNewMenu = !item.id
			const endpoint = isNewMenu
				? apiEndpoint
				: `${apiEndpoint}/${item.id}`
			const method = isNewMenu ? 'POST' : 'PUT'

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

			const data = await response.json()
			const entity = new Menu(data)

			this.refreshMenuList()
			this.setMenuItem(entity)

			return { response, data, entity }
		},

		/* istanbul ignore next */
		async addMenu(item: Menu) {
			if (!(item instanceof Menu)) {
				throw Error('Please pass a Menu item from the Menu class')
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

			const data = new Menu(await response.json())

			this.refreshMenuList()
			this.setMenuItem(data)

			return { response, data }
		},

		/* istanbul ignore next */
		async editMenu(menuItem: Menu) {
			if (!(menuItem instanceof Menu)) {
				throw Error('Please pass a Menu item from the Menu class')
			}

			const validateResult = menuItem.validate()
			if (!validateResult.success) {
				throw Error(validateResult.error.issues[0].message)
			}

			const response = await fetch(
				`${apiEndpoint}/${menuItem.id}`,
				{
					method: 'PUT',
					headers: {
						'Content-Type': 'application/json',
					},
					body: JSON.stringify(validateResult.data),
				},
			)

			const data = new Menu(await response.json())

			this.refreshMenuList()
			this.setMenuItem(data)

			return { response, data }
		},

		/* istanbul ignore next */
		async deleteMenu(id: number) {
			if (!id) {
				throw Error('Passed id is falsy')
			}

			const response = await fetch(
				`${apiEndpoint}/${id}`,
				{ method: 'DELETE' },
			)

			this.refreshMenuList()
			this.setMenuItem(null)

			return { response }
		},
	},
})
