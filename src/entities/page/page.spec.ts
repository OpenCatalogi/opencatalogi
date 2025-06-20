/* eslint-disable no-console */
import { Page } from './page'
import { mockPage } from './page.mock'

describe('Page Store', () => {
	it('create Page entity with full data', () => {
		const page = new Page(mockPage()[0])

		expect(page).toBeInstanceOf(Page)
		expect(page).toEqual(mockPage()[0])
		expect(page.uuid).toBe(mockPage()[0].uuid)
		expect(page.title).toBe(mockPage()[0].title)
		expect(page.slug).toBe(mockPage()[0].slug)
		expect(page.contents).toEqual(mockPage()[0].contents)
		expect(page.createdAt).toBe(mockPage()[0].createdAt)
		expect(page.updatedAt).toBe(mockPage()[0].updatedAt)

		expect(page.validate().success).toBe(true)
	})

	it('create Page entity with partial data', () => {
		const page = new Page(mockPage()[1])

		expect(page).toBeInstanceOf(Page)
		expect(page.id).toBe(mockPage()[1].id)
		expect(page.uuid).toBe(mockPage()[1].uuid)
		expect(page.title).toBe(mockPage()[1].title)
		expect(page.slug).toBe(mockPage()[1].slug)
		expect(page.contents).toBeNull()
		expect(page.createdAt).toBe(mockPage()[1].createdAt)
		expect(page.updatedAt).toBe(mockPage()[1].updatedAt)

		expect(page.validate().success).toBe(true)
	})

	it('create Page entity with falsy data', () => {
		const page = new Page(mockPage()[2])

		expect(page).toBeInstanceOf(Page)
		expect(page).toEqual(mockPage()[2])
		expect(page.uuid).toBe(mockPage()[2].uuid)
		expect(page.title).toBe('')
		expect(page.contents).toBeNull()

		expect(page.validate().success).toBe(false)
	})
})
