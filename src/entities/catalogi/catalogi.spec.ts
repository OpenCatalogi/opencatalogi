/**
 * Test suite for Catalogi entity
 * @module Entities
 * @package
 * @author Ruben Linde
 * @copyright 2024
 * @license AGPL-3.0-or-later
 * @version 1.0.0
 * @see {@link https://github.com/opencatalogi/opencatalogi}
 */

/* eslint-disable no-console */
import { Catalogi } from './catalogi'
import { mockCatalogi, mockCatalogiList } from './catalogi.mock'

describe('Catalogi Store', () => {
	it('create Catalogi entity with full data', () => {
		const catalogi = new Catalogi(mockCatalogi)

		expect(catalogi).toBeInstanceOf(Catalogi)
		expect(catalogi).toEqual(mockCatalogi)

		expect(catalogi.validate().success).toBe(true)
	})

	it('create Catalogi entity with partial data', () => {
		const catalogi = new Catalogi(mockCatalogiList[1])

		expect(catalogi).toBeInstanceOf(Catalogi)
		expect(catalogi).toEqual(mockCatalogiList[1])

		expect(catalogi.validate().success).toBe(true)
	})

	it('create Catalogi entity with falsy data', () => {
		const catalogi = new Catalogi({
			id: '',
			title: '',
			summary: '',
			description: '',
			image: '',
			listed: false,
			organization: '',
			registers: [],
			schemas: [],
			filters: {},
		})

		expect(catalogi).toBeInstanceOf(Catalogi)
		expect(catalogi.validate().success).toBe(false)
	})
})
