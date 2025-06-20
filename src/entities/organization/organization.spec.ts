/**
 * Organization entity tests
 * @module Entities
 * @package
 * @author Ruben Linde
 * @copyright 2024
 * @license AGPL-3.0-or-later
 * @version 1.0.0
 * @see {@link https://github.com/opencatalogi/opencatalogi}
 */

/* eslint-disable no-console */
import { Organization } from './organization'
import { mockOrganizations } from './organization.mock'

describe('Organization Store', () => {
	it('create Organization entity with full data', () => {
		const organization = new Organization(mockOrganizations()[0])

		expect(organization).toBeInstanceOf(Organization)
		expect(organization).toEqual(mockOrganizations()[0])
		expect(organization.id).toBe(mockOrganizations()[0].id)
		expect(organization.name).toBe(mockOrganizations()[0].name)
		expect(organization.summary).toBe(mockOrganizations()[0].summary)
		expect(organization.description).toBe(mockOrganizations()[0].description)
		expect(organization.oin).toBe(mockOrganizations()[0].oin)
		expect(organization.tooi).toBe(mockOrganizations()[0].tooi)
		expect(organization.rsin).toBe(mockOrganizations()[0].rsin)
		expect(organization.pki).toBe(mockOrganizations()[0].pki)
		expect(organization.image).toBe(mockOrganizations()[0].image)

		expect(organization.validate().success).toBe(true)
	})

	it('create Organization entity with partial data', () => {
		const organization = new Organization(mockOrganizations()[1])

		expect(organization).toBeInstanceOf(Organization)
		expect(organization.id).toBe(mockOrganizations()[1].id)
		expect(organization.name).toBe(mockOrganizations()[1].name)
		expect(organization.summary).toBe(mockOrganizations()[1].summary)
		expect(organization.description).toBe(mockOrganizations()[1].description)
		expect(organization.oin).toBe('')
		expect(organization.tooi).toBe('')
		expect(organization.rsin).toBe('')
		expect(organization.pki).toBe('')
		expect(organization.image).toBe('')

		expect(organization.validate().success).toBe(true)
	})

	it('create Organization entity with falsy data', () => {
		const organization = new Organization(mockOrganizations()[2])

		expect(organization).toBeInstanceOf(Organization)
		expect(organization).toEqual(mockOrganizations()[2])

		expect(organization.validate().success).toBe(false)
	})
})
