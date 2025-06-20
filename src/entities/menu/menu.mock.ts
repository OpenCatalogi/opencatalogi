/**
 * Menu mock data
 * @module Entities
 * @package
 * @author Ruben Linde
 * @copyright 2024
 * @license AGPL-3.0-or-later
 * @version 1.0.0
 * @see {@link https://github.com/opencatalogi/opencatalogi}
 */

import { TMenuItem, TMenu } from './menu.types'

/**
 * Mock menu item data
 */
export const mockMenuItem: TMenuItem = {
	title: 'Home',
	slug: 'home',
	link: '/',
	description: 'Home page',
	icon: 'home',
	items: [],
}

/**
 * Mock menu data
 */
export const mockMenu: TMenu = {
	id: '1',
	uuid: '123e4567-e89b-12d3-a456-426614174000',
	title: 'Main Menu',
	position: 1,
	items: [mockMenuItem],
	createdAt: new Date().toISOString(),
	updatedAt: new Date().toISOString(),
}

/**
 * Mock minimal menu data
 */
export const mockMinimalMenu: TMenu = {
	id: '1',
	uuid: '123e4567-e89b-12d3-a456-426614174001',
	title: 'Main Menu',
	position: 1,
	items: [],
	createdAt: new Date().toISOString(),
	updatedAt: new Date().toISOString(),
}
