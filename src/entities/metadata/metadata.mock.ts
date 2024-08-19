import { Metadata } from './metadata'
import { TMetadata } from './metadata.types'

export const mockMetadataData = (): TMetadata[] => [
	{ // full data
		id: '1',
		title: 'Test metadata',
		description: 'this is a very long description for test metadata',
		source: 'http://testurl.com',
		version: '0.0.1',
		required: ['test'],
		properties: {
			test: {
				title: 'test prop',
				description: 'a long description',
				type: 'string',
				format: 'date',
				pattern: 1,
				default: 'true',
				behavior: 'silly',
				required: false,
				deprecated: false,
				minLength: 5,
				maxLength: 6,
				example: 'gooby example',
				minimum: 1,
				maximum: 3,
				multipleOf: 1,
				exclusiveMin: false,
				exclusiveMax: false,
				minItems: 0,
				maxItems: 6,
			},
			gfdgds: {
				title: 'gfdgds prop',
				description: 'property description',
				type: 'string',
				format: 'uuid',
				pattern: 2,
				default: 'false',
				behavior: 'goofy perchance',
				required: false,
				deprecated: false,
				minLength: 5.5,
				maxLength: 5.11,
				example: 'bazinga',
				minimum: 1,
				maximum: 2,
				multipleOf: 1,
				exclusiveMin: true,
				exclusiveMax: false,
				minItems: 1,
				maxItems: 7,
			},
		},
		archive: {
			valuation: 'b',
			class: 1,
		},
	},
	{ // partial data
		id: '2',
		title: 'Test metadata',
		description: 'this is a very long description for test metadata',
		source: 'http://testurl.com',
		version: '',
		required: [],
		properties: {},
		archive: {
			valuation: 'b',
			class: 1,
		},
	},
	{ // invalid data
		id: '1',
		title: 'Test metadata',
		description: 'this is a very long description for test metadata',
		version: '0.0.1',
		// @ts-expect-error -- required is array
		required: 'test',
		properties: {
			test: {
				title: 'test prop',
				description: 'a long description',
				type: 'string',
				format: 'date',
				pattern: 1,
				default: 'true',
				behavior: 'silly',
				required: false,
				deprecated: false,
				minLength: 5,
				maxLength: 6,
				example: 'gooby example',
				minimum: 1,
				maximum: 3,
				multipleOf: 1,
				exclusiveMin: false,
				exclusiveMax: false,
				minItems: 0,
				maxItems: 6,
			},
			gfdgds: {
				title: 'gfdgds prop',
				description: 'property description',
				type: 'string',
				format: 'uuid',
				pattern: 2,
				default: 'false',
				behavior: 'goofy perchance',
				required: false,
				deprecated: false,
				minLength: 5.5,
				maxLength: 5.11,
				example: 'bazinga',
				minimum: 1,
				maximum: 2,
				multipleOf: 1,
				exclusiveMin: true,
				exclusiveMax: false,
				minItems: 1,
				maxItems: 7,
			},
		},
		archive: {
			valuation: 'b',
			class: 1,
		},
	},
]

export const mockMetadata = (data: TMetadata[] = mockMetadataData()): TMetadata[] => data.map(item => new Metadata(item))
