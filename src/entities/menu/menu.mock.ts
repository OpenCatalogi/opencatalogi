import { Menu } from './menu'
import { TMenu } from './menu.types.js'

/**
 * Mock data function that returns an array of menu data objects
 * Used for testing and development purposes
 */
export const mockMenuData = (): TMenu[] => [
	{ // full data
		id: '1',
		uuid: '123e4567-e89b-12d3-a456-426614174000',
		name: 'Main Menu',
		position: 1,
		items: [{ name: 'Home', description: 'Home', slug: '/', icon: 'home', link: '/' }],
		createdAt: new Date().toISOString(),
		updatedAt: new Date().toISOString(),
	},
	// @ts-expect-error -- expected missing items
	{ // partial data
		id: '2',
		uuid: '123e4567-e89b-12d3-a456-426614174001',
		name: 'Footer Menu',
		position: 2,
		createdAt: new Date().toISOString(),
		updatedAt: new Date().toISOString(),
	},
	{ // invalid data
		id: '3',
		uuid: '123e4567-e89b-12d3-a456-426614174002',
		name: '',
		position: -1,
		items: [],
		createdAt: new Date().toISOString(),
		updatedAt: new Date().toISOString(),
	},
]

/**
 * Creates an array of Menu instances from provided data or default mock data
 * @param data Optional array of menu data to convert to Menu instances
 * @return {TMenu[]} Array of Menu instances
 */
export const mockMenu = (data: TMenu[] = mockMenuData()): TMenu[] => data.map(item => new Menu(item))
