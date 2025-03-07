/* eslint-disable no-console */
import { createPinia, setActivePinia } from 'pinia'

import { mockOrganization, Organization } from '../../entities/index.js'
import { useOrganizationStore } from './organization'

describe('Organization Store', () => {
	beforeEach(() => {
		setActivePinia(createPinia())
	})

	it('sets organization item correctly', () => {
		const store = useOrganizationStore()

		store.setOrganizationItem(mockOrganization()[0])

		expect(store.organizationItem).toBeInstanceOf(Organization)
		expect(store.organizationItem).toEqual(mockOrganization()[0])
		expect(store.organizationItem.validate().success).toBe(true)

		store.setOrganizationItem(mockOrganization()[1])

		expect(store.organizationItem).toBeInstanceOf(Organization)
		expect(store.organizationItem).toEqual(mockOrganization()[1])
		expect(store.organizationItem.validate().success).toBe(true)

		store.setOrganizationItem(mockOrganization()[2])

		expect(store.organizationItem).toBeInstanceOf(Organization)
		expect(store.organizationItem).toEqual(mockOrganization()[2])
		expect(store.organizationItem.validate().success).toBe(false)
	})

	it('sets organization list correctly', () => {
		const store = useOrganizationStore()

		store.setOrganizationList(mockOrganization())

		expect(store.organizationList).toHaveLength(mockOrganization().length)

		expect(store.organizationList[0]).toBeInstanceOf(Organization)
		expect(store.organizationList[0]).toEqual(mockOrganization()[0])
		expect(store.organizationList[0].validate().success).toBe(true)

		expect(store.organizationList[1]).toBeInstanceOf(Organization)
		expect(store.organizationList[1]).toEqual(mockOrganization()[1])
		expect(store.organizationList[1].validate().success).toBe(true)

		expect(store.organizationList[2]).toBeInstanceOf(Organization)
		expect(store.organizationList[2]).toEqual(mockOrganization()[2])
		expect(store.organizationList[2].validate().success).toBe(false)
	})
})
