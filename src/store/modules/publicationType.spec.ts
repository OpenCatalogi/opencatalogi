/* eslint-disable no-console */
import { setActivePinia, createPinia } from 'pinia'

import { usePublicationTypeStore } from './publicationType'
import { PublicationType, mockPublicationType } from '../../entities/index.js'

describe('Publication Type Store', () => {
	beforeEach(() => {
		setActivePinia(createPinia())
	})

	it('sets publication type item correctly', () => {
		const store = usePublicationTypeStore()

		store.setPublicationTypeItem(mockPublicationType()[0])

		expect(store.publicationTypeItem).toBeInstanceOf(PublicationType)
		expect(store.publicationTypeItem).toEqual(mockPublicationType()[0])

		expect(store.publicationTypeItem.validate().success).toBe(true)
	})

	it('sets publication type item with string "properties" property', () => {
		const store = usePublicationTypeStore()

		// stringify json data
		const mockData = mockPublicationType()[0]
		// @ts-expect-error -- this is for the off chance that properties is a string
		mockData.properties = JSON.stringify(mockData.properties)

		store.setPublicationTypeItem(mockData)

		expect(store.publicationTypeItem).toBeInstanceOf(PublicationType)
		expect(store.publicationTypeItem).toEqual(mockData)

		expect(store.publicationTypeItem.validate().success).toBe(true)
	})

	it('sets publication type list correctly', () => {
		const store = usePublicationTypeStore()

		store.setPublicationTypeList(mockPublicationType())

		expect(store.publicationTypeList[0]).toBeInstanceOf(PublicationType)
		expect(store.publicationTypeList[0]).toEqual(mockPublicationType()[0])

		expect(store.publicationTypeList[0].validate().success).toBe(true)
	})

	it('get publication type property from key', () => {
		const store = usePublicationTypeStore()

		store.setPublicationTypeItem(mockPublicationType()[0])
		store.setPublicationTypeDataKey('test')

		expect(store.publicationTypeItem).toEqual(mockPublicationType()[0])
		expect(store.publicationTypeDataKey).toBe('test')
	})
})
