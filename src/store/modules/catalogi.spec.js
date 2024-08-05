/* eslint-disable no-console */
import { setActivePinia, createPinia } from 'pinia'

import { useCatalogiStore } from './catalogi.js'
import { Catalogi } from '../../entities/index.js'

describe(
	'Catalogi Store', () => {
		beforeEach(
			() => {
				setActivePinia(createPinia())
			},
		)

		it(
			'sets catalogi item correctly', () => {
				const store = useCatalogiStore()

				store.setCatalogiItem(testData[0])

				expect(store.catalogiItem).toBeInstanceOf(Catalogi)
				expect(store.catalogiItem).toEqual(testData[0])

				expect(store.catalogiItem.validate()).toBe(true)
			},
		)

		it(
			'sets catalogi list correctly', () => {
				const store = useCatalogiStore()

				store.setCatalogiList(testData)

				expect(store.catalogiList).toHaveLength(testData.length)

				// list item 1
				expect(store.catalogiList[0]).toBeInstanceOf(Catalogi)
				expect(store.catalogiList[0]).toEqual(testData[0])

				expect(store.catalogiList[0].validate()).toBe(true)

				// list item 2
				expect(store.catalogiList[1]).toBeInstanceOf(Catalogi)
				expect(store.catalogiList[1].id).toBe(testData[1].id)
				expect(store.catalogiList[1].title).toBe(testData[1].title)
				expect(store.catalogiList[1].summary).toBe(testData[1].summary)
				expect(store.catalogiList[1].description).toBe(testData[1].description)
				expect(store.catalogiList[1].image).toBe('')
				expect(store.catalogiList[1].search).toBe('')

				expect(store.catalogiList[1].validate()).toBe(true)

				// list item 3
				expect(store.catalogiList[2]).toBeInstanceOf(Catalogi)
				expect(store.catalogiList[2].id).toBe(testData[2].id)
				expect(store.catalogiList[2].title).toBe('')
				expect(store.catalogiList[2].summary).toBe(testData[2].summary)
				expect(store.catalogiList[2].description).toBe(testData[2].description)
				expect(store.catalogiList[2].image).toBe(testData[2].image)
				expect(store.catalogiList[2].search).toBe(testData[2].search)

				expect(store.catalogiList[2].validate()).toBe(false) // id, title and summary are required, causing a falsy result
			},
		)
	},
)

const testData = [
	{
		id: '1',
		title: 'Decat',
		summary: 'a short form summary',
		description: 'a really really long description about this catalogus',
		image: 'string',
		search: 'string',
	},
	{
		id: '2',
		title: 'Woo',
		summary: 'a short form summary',
		description: 'a really really long description about this catalogus',
	},
	{
		id: '3',
		title: '',
		summary: 'a short form summary',
		description: 'a really really long description about this catalogus',
		image: 'string',
		search: 'string',
	},
]
