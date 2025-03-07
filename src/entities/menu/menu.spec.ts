/* eslint-disable no-console */
import { Menu } from './menu'
import { mockMenu } from './menu.mock'

describe('Menu Store', () => {
	it('create Menu entity with full data', () => {
		const menu = new Menu(mockMenu()[0])

		expect(menu).toBeInstanceOf(Menu)
		expect(menu).toEqual(mockMenu()[0])
		expect(menu.uuid).toBe(mockMenu()[0].uuid)
		expect(menu.name).toBe(mockMenu()[0].name)
		expect(menu.position).toBe(mockMenu()[0].position)
		expect(menu.items).toEqual(mockMenu()[0].items)
		expect(menu.createdAt).toBe(mockMenu()[0].createdAt)
		expect(menu.updatedAt).toBe(mockMenu()[0].updatedAt)

		expect(menu.validate().success).toBe(true)
	})

	it('create Menu entity with partial data', () => {
		const menu = new Menu(mockMenu()[1])

		expect(menu).toBeInstanceOf(Menu)
		expect(menu.id).toBe(mockMenu()[1].id)
		expect(menu.uuid).toBe(mockMenu()[1].uuid)
		expect(menu.name).toBe(mockMenu()[1].name)
		expect(menu.position).toBe(0) // Default position
		expect(menu.items).toBe('[]') // Default empty array
		expect(menu.createdAt).toBe(mockMenu()[1].createdAt)
		expect(menu.updatedAt).toBe(mockMenu()[1].updatedAt)

		expect(menu.validate().success).toBe(true)
	})

	it('create Menu entity with falsy data', () => {
		const menu = new Menu(mockMenu()[2])

		expect(menu).toBeInstanceOf(Menu)
		expect(menu).toEqual(mockMenu()[2])
		expect(menu.uuid).toBe(mockMenu()[2].uuid)
		expect(menu.name).toBe('')
		expect(menu.items).toBe('[]')

		expect(menu.validate().success).toBe(false)
	})
})
