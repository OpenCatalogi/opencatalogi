/* eslint-disable no-console */
import { Catalogi } from './catalogi'
import { TCatalogi } from './catalogi.types'

describe('Catalogi Store', () => {
	it('create Catalogi entity with full data', () => {
		const catalogi = new Catalogi(testData[0])

		expect(catalogi).toBeInstanceOf(Catalogi)
		expect(catalogi).toEqual(testData[0])

		expect(catalogi.validate()).toBe(true)
	})

	it('create Catalogi entity with partial data', () => {
		const catalogi = new Catalogi(testData[1])

		expect(catalogi).toBeInstanceOf(Catalogi)
		expect(catalogi.id).toBe(testData[1].id)
		expect(catalogi.title).toBe(testData[1].title)
		expect(catalogi.summary).toBe(testData[1].summary)
		expect(catalogi.description).toBe(testData[1].description)
		expect(catalogi.image).toBe('')
		expect(catalogi.search).toBe('')

		expect(catalogi.validate()).toBe(true)
	})

	it('create Catalogi entity with falsy data', () => {
		const catalogi = new Catalogi(testData[2])

		expect(catalogi).toBeInstanceOf(Catalogi)
		expect(catalogi).toEqual(testData[2])

		expect(catalogi.validate()).toBe(false)
	})
})

const testData: TCatalogi[] = [
	{ // full data
		id: '1',
		title: 'Decat',
		summary: 'a short form summary',
		description: 'a really really long description about this catalogus',
		image: 'string',
		search: 'string',
	},
	{ // partial data
		id: '2',
		title: 'Woo',
		summary: 'a short form summary',
		description: 'a really really long description about this catalogus',
	},
	{ // invalid data
		id: '3',
		title: '',
		summary: 'a short form summary',
		description: 'a really really long description about this catalogus',
		image: 'string',
		search: 'string',
	},
]
