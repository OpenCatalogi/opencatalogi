/* eslint-disable no-console */
import { Theme } from './theme'
import { TTheme } from './theme.types'

describe('Theme Store', () => {
	it('create Theme entity with full data', () => {
		const theme = new Theme(testData[0])

		expect(theme).toBeInstanceOf(Theme)
		expect(theme).toEqual(testData[0])

		expect(theme.validate()).toBe(true)
	})

	it('create Theme entity with partial data', () => {
		const theme = new Theme(testData[1])

		expect(theme).toBeInstanceOf(Theme)
		expect(theme.id).toBe(testData[1].id)
		expect(theme.title).toBe(testData[1].title)
		expect(theme.summary).toBe(testData[1].summary)
		expect(theme.description).toBe(testData[1].description)
		expect(theme.image).toBe('')

		expect(theme.validate()).toBe(true)
	})

	it('create Theme entity with falsy data', () => {
		const theme = new Theme(testData[2])

		expect(theme).toBeInstanceOf(Theme)
		expect(theme).toEqual(testData[2])

		expect(theme.validate()).toBe(false)
	})
})

const testData: TTheme[] = [
	{ // full data
		id: '1',
		title: 'Decat',
		summary: 'a short form summary',
		description: 'a really really long description about this Theme',
		image: 'string',
	},
	{ // partial data
		id: '2',
		title: 'Woo',
		summary: 'a short form summary',
		description: 'a really really long description about this Theme',
	},
	{ // invalid data
		id: '3',
		title: '',
		summary: 'a short form summary',
		description: 'a really really long description about this Theme',
		image: 'string',
	},
]
