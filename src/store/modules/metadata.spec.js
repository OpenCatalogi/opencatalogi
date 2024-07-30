/* eslint-disable no-console */
import { setActivePinia, createPinia } from 'pinia'

import { useMetadataStore } from './metadata.js'
import { Metadata } from '../../entities/index.js'

describe('Metadata Store', () => {
	beforeEach(() => {
		setActivePinia(createPinia())
	})

	it('sets metadata item correctly', () => {
		const store = useMetadataStore()

		store.setMetaDataItem(testData[0])

		expect(store.metaDataItem).toBeInstanceOf(Metadata)
		expect(store.metaDataItem).toEqual(testData[0])
		expect(store.metaDataItem.validate()).toBe(true)

		store.setMetaDataItem(testData[1])

		expect(store.metaDataItem).toBeInstanceOf(Metadata)
		expect(store.metaDataItem).not.toEqual(testData[1])
		expect(store.metaDataItem.validate()).toBe(true)

		store.setMetaDataItem(testData[2])

		expect(store.metaDataItem).toBeInstanceOf(Metadata)
		expect(store.metaDataItem).toEqual(testData[2])
		expect(store.metaDataItem.validate()).toBe(false)
	})

	it('sets metadata list correctly', () => {
		const store = useMetadataStore()

		store.setMetaDataList(testData)

		expect(store.metaDataList).toHaveLength(testData.length)

		expect(store.metaDataList[0]).toBeInstanceOf(Metadata)
		expect(store.metaDataList[0]).toEqual(testData[0])
		expect(store.metaDataList[0].validate()).toBe(true)

		expect(store.metaDataList[1]).toBeInstanceOf(Metadata)
		expect(store.metaDataList[1]).not.toEqual(testData[1])
		expect(store.metaDataList[1].validate()).toBe(true)

		expect(store.metaDataList[2]).toBeInstanceOf(Metadata)
		expect(store.metaDataList[2]).toEqual(testData[2])
		expect(store.metaDataList[2].validate()).toBe(false)
	})

	it('get metadata property from key', () => {
		const store = useMetadataStore()

		store.setMetaDataItem(testData[0])
		store.setMetadataDataKey('test')

		expect(store.metaDataItem).toEqual(testData[0])
		expect(store.metadataDataKey).toBe('test')

		const properties = store.getMetadataPropertyKeys('test')

		expect(properties).toEqual(testData[0].properties.test)
	})
})

const testData = [
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
		id: '2',
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
		id: '3',
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
