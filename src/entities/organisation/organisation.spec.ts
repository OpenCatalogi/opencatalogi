/* eslint-disable no-console */
import { Organisation } from './organisation'
import { TOrganisation } from './organisation.types'

describe('Organisation Store', () => {
	it('create Organisation entity with full data', () => {
		const organisation = new Organisation(testData[0])

		expect(organisation).toBeInstanceOf(Organisation)
		expect(organisation).toEqual(testData[0])

		expect(organisation.validate()).toBe(true)
	})

	it('create Organisation entity with partial data', () => {
		const organisation = new Organisation(testData[1])

		expect(organisation).toBeInstanceOf(Organisation)
		expect(organisation.id).toBe(testData[1].id)
		expect(organisation.title).toBe(testData[1].title)
		expect(organisation.summary).toBe(testData[1].summary)
		expect(organisation.description).toBe(testData[1].description)
		expect(organisation.oin).toBe('')
		expect(organisation.tooi).toBe('')
		expect(organisation.rsin).toBe('')
		expect(organisation.pki).toBe('')

		expect(organisation.validate()).toBe(true)
	})

	it('create Catalogi entity with falsy data', () => {
		const organisation = new Organisation(testData[2])

		expect(organisation).toBeInstanceOf(Organisation)
		expect(organisation).toEqual(testData[2])

		expect(organisation.validate()).toBe(false)
	})
})

const testData: TOrganisation[] = [
	{ // full data
		id: '1',
		title: 'Decat',
		summary: 'a short form summary',
		description: 'a really really long description about this organisation',
		oin: 'string',
		tooi: 'string',
		rsin: 'string',
		pki: 'string',
	},
	{ // partial data
		id: '2',
		title: 'Woo',
		summary: 'a short form summary',
		description: 'a really really long description about this organisation',
	},
	{ // invalid data
		id: '3',
		title: '',
		summary: 'a short form summary',
		description: 'a really really long description about this organisation',
		oin: 'string',
		tooi: 'string',
		rsin: 'string',
		pki: 'string',
	},
]
