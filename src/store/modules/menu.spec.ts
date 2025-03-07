/* eslint-disable no-console */
import { setActivePinia, createPinia } from 'pinia'

import { useMenuStore } from './menu.js'
import { Menu, mockMenu } from '../../entities/index.js'

/**
 * Test suite for Menu Store functionality
 */
describe('Menu Store', () => {
	beforeEach(() => {
		setActivePinia(createPinia())
	})

	it('sets menu item correctly', () => {
		const store = useMenuStore()

		store.setMenuItem(mockMenu()[0])

		// Verify the menu item is set and is correct type
		expect(store.menuItem).toBeInstanceOf(Menu)
		expect(store.menuItem).toEqual(mockMenu()[0])

		// Validate the menu item data
		expect(store.menuItem.validate().success).toBe(true)
	})

	it('sets menu list correctly', () => {
		const store = useMenuStore()

		store.setMenuList(mockMenu())

		// Verify list length matches mock data
		expect(store.menuList).toHaveLength(mockMenu().length)

		// Test first menu item
		expect(store.menuList[0]).toBeInstanceOf(Menu)
		expect(store.menuList[0]).toEqual(mockMenu()[0])
		expect(store.menuList[0].validate().success).toBe(true)

		// Test second menu item
		expect(store.menuList[1]).toBeInstanceOf(Menu)
		expect(store.menuList[1]).toEqual(mockMenu()[1])
		expect(store.menuList[1].validate().success).toBe(true)

		// Test third menu item
		expect(store.menuList[2]).toBeInstanceOf(Menu)
		expect(store.menuList[2]).toEqual(mockMenu()[2])
		expect(store.menuList[2].validate().success).toBe(false)
	})
})
