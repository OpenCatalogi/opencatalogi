/**
 * Mock data for Catalogi entity testing
 * @module Entities
 * @package
 * @author Ruben Linde
 * @copyright 2024
 * @license AGPL-3.0-or-later
 * @version 1.0.0
 * @see {@link https://github.com/opencatalogi/opencatalogi}
 */

import { TCatalogi } from './catalogi.types'

export const mockCatalogi: TCatalogi = {
	id: '1',
	title: 'Test Catalogi',
	summary: 'A test catalogi',
	description: 'This is a test catalogi',
	image: 'test.jpg',
	listed: true,
	organization: '1',
	registers: ['register1', 'register2'],
	schemas: ['schema1', 'schema2'],
	filters: {
		field1: 'value1',
		field2: 'value2',
	},
}

export const mockCatalogiList: TCatalogi[] = [
	mockCatalogi,
	{
		id: '2',
		title: 'Another Catalogi',
		summary: 'Another test catalogi',
		description: 'This is another test catalogi',
		image: 'test2.jpg',
		listed: false,
		organization: '2',
		registers: ['register3'],
		schemas: ['schema3'],
		filters: {
			field3: 'value3',
		},
	},
]
