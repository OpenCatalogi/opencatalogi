/* eslint-disable no-console */
import { Metadata } from './metadata'
import { TMetadata } from './metadata.types'

describe('Metadata entity', () => {
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
		expect(metadata.title).toBe('')
		expect(metadata.description).toBe(testData[1].description)
		expect(metadata.required).toEqual(testData[1].required)
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
		title: 'Test metadata',
		description: 'this is a very long description for test metadata',
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
	},
	{ // partial data
		id: '1',
		title: 'Test metadata',
		description: 'this is a very long description for test metadata',
		version: '0.0.1',
		properties: {
			test: {
				title: 'test prop',
				description: 'a long description',
				type: 'string',
				behavior: 'silly',
				required: false,
				exclusiveMin: false,
				exclusiveMax: false,
				minItems: 0,
				maxItems: 6,
			},
		},
	},
	{ // invalid data
		id: '1',
		title: '', // cannot be empty
		description: 'this is a very long description for test metadata',
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
	},
]
