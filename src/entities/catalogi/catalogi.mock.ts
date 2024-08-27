import { Catalogi } from './catalogi'
import { TCatalogi } from './catalogi.types'

export const mockCatalogiData = (): TCatalogi[] => [
	{
		id: '1',
		title: 'Decat',
		summary: 'a short form summary',
		description: 'a really really long description about this catalogus',
		image: 'string',
		listed: false,
		organisation: {
			id: '1',
			title: 'Decat',
			summary: 'a short form summary',
			description: 'a really really long description about this organisation',
			oin: 'string',
			tooi: 'string',
			rsin: 'string',
			pki: 'string',
		},
		metadata: ['1', '3'],
	},
	{
		id: '2',
		title: 'Woo',
		summary: 'a short form summary',
		description: 'a really really long description about this catalogus',
		image: '',
		listed: false,
		organisation: {
			id: '1',
			title: 'Decat',
			summary: 'a short form summary',
			description: 'a really really long description about this organisation',
			oin: 'string',
			tooi: 'string',
			rsin: 'string',
			pki: 'string',
		},
		metadata: [],
	},
	{
		id: '3',
		title: 'Foo',
		summary: 'a short form summary',
		description: 'a really really long description about this catalogus',
		image: 'string',
		// @ts-expect-error -- listed needs to be a boolean
		listed: 0.2,
		organisation: null,
		metadata: ['1', '3'],
	},
]

export const mockCatalogi = (data: TCatalogi[] = mockCatalogiData()): TCatalogi[] => data.map(item => new Catalogi(item))
