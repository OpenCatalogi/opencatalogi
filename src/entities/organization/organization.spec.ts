/* eslint-disable no-console */
import { Organization } from './organization'
import { mockOrganization } from './organization.mock'

describe('Organization Store', () => {
	it('create Organization entity with full data', () => {
		const organization = new Organization(mockOrganization()[0])

		expect(organization).toBeInstanceOf(Organization)
		expect(organization).toEqual(mockOrganization()[0])

		expect(organization.validate().success).toBe(true)
	})

	it('create Organization entity with partial data', () => {
		const organization = new Organization(mockOrganization()[1])

		expect(organization).toBeInstanceOf(Organization)
		expect(organization.id).toBe(mockOrganization()[1].id)
		expect(organization.title).toBe(mockOrganization()[1].title)
		expect(organization.summary).toBe(mockOrganization()[1].summary)
		expect(organization.description).toBe(mockOrganization()[1].description)
		expect(organization.oin).toBe('')
		expect(organization.tooi).toBe('')
		expect(organization.rsin).toBe('')
		expect(organization.pki).toBe('')

		expect(organization.validate().success).toBe(true)
	})

	it('create Organization entity with falsy data', () => {
		const organization = new Organization(mockOrganization()[2])

		expect(organization).toBeInstanceOf(Organization)
		expect(organization).toEqual(mockOrganization()[2])

		expect(organization.validate().success).toBe(false)
	})
})
