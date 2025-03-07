/* eslint-disable no-console */
import { setActivePinia, createPinia } from 'pinia'

import { usePageStore } from './page.js'
import { mockPage, Page } from '../../entities/index.js'

describe('Page Store', () => {
	beforeEach(() => {
		setActivePinia(createPinia())
	})

	it('sets page item correctly', () => {
		const store = usePageStore()

		store.setPageItem(mockPage()[0])

		expect(store.pageItem).toBeInstanceOf(Page)
		expect(store.pageItem).toEqual(mockPage()[0])
		expect(store.pageItem.validate().success).toBe(true)

		store.setPageItem(mockPage()[1])

		expect(store.pageItem).toBeInstanceOf(Page)
		expect(store.pageItem).toEqual(mockPage()[1])
		expect(store.pageItem.validate().success).toBe(true)

		store.setPageItem(mockPage()[2])

		expect(store.pageItem).toBeInstanceOf(Page)
		expect(store.pageItem).toEqual(mockPage()[2])
		expect(store.pageItem.validate().success).toBe(false)
	})

	it('sets page list correctly', () => {
		const store = usePageStore()

		store.setPageList(mockPage())

		expect(store.pageList).toHaveLength(mockPage().length)

		expect(store.pageList[0]).toBeInstanceOf(Page)
		expect(store.pageList[0]).toEqual(mockPage()[0])
		expect(store.pageList[0].validate().success).toBe(true)

		expect(store.pageList[1]).toBeInstanceOf(Page)
		expect(store.pageList[1]).toEqual(mockPage()[1])
		expect(store.pageList[1].validate().success).toBe(true)

		expect(store.pageList[2]).toBeInstanceOf(Page)
		expect(store.pageList[2]).toEqual(mockPage()[2])
		expect(store.pageList[2].validate().success).toBe(false)
	})
})
