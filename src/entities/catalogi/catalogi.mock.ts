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
		organization: '2',
		publicationTypes: ['1', '3'],
	},
	{
		id: '2',
		title: 'Woo',
		summary: 'a short form summary',
		description: 'a really really long description about this catalogus',
		image: '',
		listed: false,
		organization: '2',
		publicationTypes: [],
	},
	{
		id: '3',
		title: 'Foo',
		summary: 'a short form summary',
		description: 'a really really long description about this catalogus',
		image: 'string',
		// @ts-expect-error -- listed needs to be a boolean
		listed: 0.2,
		organization: null,
		publicationTypes: ['1', '3'],
	},
]

export const mockCatalogi = (data: TCatalogi[] = mockCatalogiData()): TCatalogi[] => data.map(item => new Catalogi(item))
