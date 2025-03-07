import { Organization } from './organization'
import { TOrganization } from './organization.types'

export const mockOrganizationData = (): TOrganization[] => [
	{ // full data
		id: '1',
		title: 'Decat',
		summary: 'a short form summary',
		description: 'a really really long description about this organization',
		oin: '00000001836472635000',
		tooi: '7843432',
		rsin: '827342654',
		pki: '543573424',
		image: '',
	},
	{
		id: '2',
		title: 'Woo',
		summary: 'a short form summary',
		description: 'a really really long description about this organization',
		oin: '',
		tooi: '',
		rsin: '',
		pki: '',
		image: '',
	},
	{ // invalid data
		id: '3',
		title: '',
		summary: 'a short form summary',
		description: 'a really really long description about this organization',
		oin: '5435',
		tooi: '5435',
		rsin: '54',
		pki: '6565',
		image: '',
	},
]

export const mockOrganization = (data: TOrganization[] = mockOrganizationData()): TOrganization[] => data.map(item => new Organization(item))
