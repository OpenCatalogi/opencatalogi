import { Organization } from './organization'
import { TOrganization } from './organization.types'

export const mockOrganizationData = (): TOrganization[] => [
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
	// @ts-expect-error -- missing oin, tooi, rsin and pki properties
	{
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

export const mockOrganization = (data: TOrganization[] = mockOrganizationData()): TOrganization[] => data.map(item => new Organization(item))
