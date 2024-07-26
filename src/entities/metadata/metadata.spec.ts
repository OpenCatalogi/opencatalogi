/* eslint-disable no-console */
import { Metadata } from './metadata'
import { TMetadata } from './metadata.types'

describe('Catalogi Store', () => {
	it('create Metadata entity with full data', () => {
		const metadata = new Metadata(testData[0])

		expect(metadata).toBeInstanceOf(Metadata)
		expect(metadata).toEqual(testData[0])

		expect(metadata.validate()).toBe(true)
	})

	it('create Metadata entity with partial data', () => {
		const metadata = new Metadata(testData[1])

		expect(metadata).toBeInstanceOf(Metadata)
		expect(metadata.id).toBe(testData[1].id)
		expect(metadata.title).toBe(testData[1].title)
		expect(metadata.description).toBe(testData[1].description)
		expect(metadata.required).toEqual([])
		expect(metadata.version).toBe(testData[1].version)
		expect(metadata.properties).toEqual(testData[1].properties)

		expect(metadata.validate()).toBe(true)
	})

	it('create Metadata entity with falsy data', () => {
		const metadata = new Metadata(testData[2])

		expect(metadata).toBeInstanceOf(Metadata)
		expect(metadata).toEqual(testData[2])

		expect(metadata.validate()).toBe(false)
	})
})

const testData: TMetadata[] = [
	{ // full data
		id: '1',
		title: 'Decat',
		description: 'A detailed description about this catalog.',
		version: '1.0.0',
		required: ['summary', 'image'],
		properties: [
			{
				id: 'summary',
				title: 'Summary',
				description: 'A short form summary',
				type: 'string',
				format: 'date',
				pattern: 1,
				default: 'njiofdsf',
				behavior: 'fitness',
				required: true,
				deprecated: false,
				minLength: 10,
				maxLength: 100,
				example: 'This is a summary example.',
				minimum: 1,
				maximum: 5,
				multipleOf: 1,
				exclusiveMin: false,
				exclusiveMax: false,
				minItems: 0,
				maxItems: 5,
			},
			{
				id: 'image',
				title: 'Image URL',
				description: 'URL of the catalog image',
				type: 'string',
				format: 'url',
				pattern: 1,
				default: 'http://example.com/image.jpg',
				behavior: 'hawk tuah',
				required: true,
				deprecated: true,
				minLength: 10,
				maxLength: 100,
				example: 'This is a summary example.',
				minimum: 1,
				maximum: 5,
				multipleOf: 1,
				exclusiveMin: false,
				exclusiveMax: false,
				minItems: 0,
				maxItems: 5,
			},
		],
	},
	{ // partial data
		id: '2',
		title: 'Woo',
		description: 'A detailed description about this catalog.',
		version: '1.0.1',
		properties: [
			{
				id: 'summary',
				title: 'Summary',
				description: 'A short form summary',
				type: 'string',
				minLength: 10,
				maxLength: 100,
				example: 'This is a summary example.',
			},
		],
	},
	{ // invalid data
		id: '3',
		title: '',
		description: 'A detailed description about this catalog.',
		version: '1.0.2',
		required: [],
		properties: [
			{
				id: 'summary',
				title: 'Summary',
				description: 'A short form summary',
				type: 'string',
				minLength: 10,
				maxLength: 100,
				example: 'This is a summary example.',
			},
		],
	},
]
