/* eslint-disable no-console */
import { setActivePinia, createPinia } from 'pinia'

import { useCatalogiStore } from './catalogi.js'
import { Catalogi } from '../../entities/index.js'

describe('Catalogi Store', () => {
	beforeEach(() => {
		setActivePinia(createPinia())
	})

	it('sets catalogi item correctly', () => {
		const store = useCatalogiStore()
		const catalogiItem = {
			id: '1',
			name: 'Test Catalogi',
			summary: 'This is a test catalogi',
			_schema: 'Test Schema',
			_id: 'Test ID',
		}

		store.setCatalogiItem(catalogiItem)

		expect(store.catalogiItem).toBeInstanceOf(Catalogi)
		expect(store.catalogiItem.id).toBe(catalogiItem.id)
		expect(store.catalogiItem.name).toBe(catalogiItem.name)
		expect(store.catalogiItem.summary).toBe(catalogiItem.summary)
		expect(store.catalogiItem._schema).toBe(catalogiItem._schema)
		expect(store.catalogiItem._id).toBe(catalogiItem._id)
	})

	it('sets catalogi list correctly', () => {
		const store = useCatalogiStore()
		const catalogiList = [
			{
				id: '1',
				name: 'Test Catalogi 1',
				summary: 'This is test catalogi 1',
				_schema: 'Test Schema 1',
				_id: 'Test ID 1',
			},
			{
				id: '2',
				name: 'Test Catalogi 2',
				summary: 'This is test catalogi 2',
				_schema: 'Test Schema 2',
				_id: 'Test ID 2',
			},
		]

		store.setCatalogiList(catalogiList)

		expect(store.catalogiList).toHaveLength(catalogiList.length)
		store.catalogiList.forEach((item, index) => {
			expect(item).toBeInstanceOf(Catalogi)
			expect(item.id).toBe(catalogiList[index].id)
			expect(item.name).toBe(catalogiList[index].name)
			expect(item.summary).toBe(catalogiList[index].summary)
			expect(item._schema).toBe(catalogiList[index]._schema)
			expect(item._id).toBe(catalogiList[index]._id)
		})
	})
})
