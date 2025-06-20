/**
 * Menu entity tests
 * @module Entities
 * @package
 * @author Ruben Linde
 * @copyright 2024
 * @license AGPL-3.0-or-later
 * @version 1.0.0
 * @see {@link https://github.com/opencatalogi/opencatalogi}
 */

/* eslint-disable no-console */
import { Menu } from './menu'
import { mockMenu, mockMinimalMenu } from './menu.mock'
import { TMenu } from './menu.types'

describe('Menu Store', () => {
	it('create Menu entity with full data', () => {
		const menu = new Menu(mockMenu)

		expect(menu).toBeInstanceOf(Menu)
		expect(menu).toEqual(mockMenu)
		expect(menu.uuid).toBe(mockMenu.uuid)
		expect(menu.title).toBe(mockMenu.title)
		expect(menu.position).toBe(mockMenu.position)
		expect(menu.items).toEqual(mockMenu.items)
		expect(menu.createdAt).toBe(mockMenu.createdAt)
		expect(menu.updatedAt).toBe(mockMenu.updatedAt)

		expect(menu.validate().success).toBe(true)
	})

	it('create Menu entity with partial data', () => {
		const menu = new Menu(mockMinimalMenu)

		expect(menu).toBeInstanceOf(Menu)
		expect(menu.id).toBe(mockMinimalMenu.id)
		expect(menu.uuid).toBe(mockMinimalMenu.uuid)
		expect(menu.title).toBe(mockMinimalMenu.title)
		expect(menu.position).toBe(0) // Default position
		expect(menu.items).toBe('[]') // Default empty array
		expect(menu.createdAt).toBe(mockMinimalMenu.createdAt)
		expect(menu.updatedAt).toBe(mockMinimalMenu.updatedAt)

		expect(menu.validate().success).toBe(true)
	})

	it('create Menu entity with falsy data', () => {
		const menu = new Menu({} as TMenu)

		expect(menu).toBeInstanceOf(Menu)
		expect(menu.uuid).toBe('')
		expect(menu.title).toBe('')
		expect(menu.items).toBe('[]')

		expect(menu.validate().success).toBe(false)
	})
})
