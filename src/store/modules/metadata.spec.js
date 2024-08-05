/* eslint-disable no-console */
import { setActivePinia, createPinia } from 'pinia'

import { useMetadataStore } from './metadata.js'

describe(
	'Metadata Store', () => {
		beforeEach(
			() => {
				setActivePinia(createPinia())
			},
		)

		it(
			'sets metadata item correctly', () => {
				const store = useMetadataStore()
				const metadataItem = {
					id: '1',
					name: 'Test metadata name',
					title: 'Test metadata',
					summary: 'This is a test listing',
					description: 'this is a very long description for test listing',
					version: '0.0.1',
					properties: {
						sasds: {
							type: 'string',
							description: 'property description',
							format: 'a format',
							maxDate: '2025-07-13',
							required: false,
							default: false,
							cascadeDelete: false,
							exclusiveMinimum: '2',
						},
					},
				}

				store.setMetaDataItem(metadataItem)

				expect(store.metaDataItem).toEqual(metadataItem)
			},
		)

		it(
			'sets metadata item with string "properties" property', () => {
				const store = useMetadataStore()
				const metadataItem = {
					id: '1',
					name: 'Test metadata name',
					title: 'Test metadata',
					summary: 'This is a test listing',
					description: 'this is a very long description for test listing',
					version: '0.0.1',
					properties: '{"sasds":{"type":"string","description":"property description","format":"a format","maxDate":"2025-07-13","required":false,"default":false,"cascadeDelete":false,"exclusiveMinimum":"2"}}',
				}

				store.setMetaDataItem(metadataItem)

				expect(store.metaDataItem.id).toBe('1')
				expect(store.metaDataItem.name).toBe('Test metadata name')
				expect(store.metaDataItem.title).toBe('Test metadata')
				expect(store.metaDataItem.summary).toBe('This is a test listing')
				expect(store.metaDataItem.description).toBe('this is a very long description for test listing')
				expect(store.metaDataItem.version).toBe('0.0.1')
				// properties
				expect(store.metaDataItem.properties.sasds.type).toBe('string')
				expect(store.metaDataItem.properties.sasds.description).toBe('property description')
				expect(store.metaDataItem.properties.sasds.format).toBe('a format')
				expect(store.metaDataItem.properties.sasds.maxDate).toBe('2025-07-13')
				expect(store.metaDataItem.properties.sasds.required).toBe(false)
				expect(store.metaDataItem.properties.sasds.default).toBe(false)
				expect(store.metaDataItem.properties.sasds.cascadeDelete).toBe(false)
				expect(store.metaDataItem.properties.sasds.exclusiveMinimum).toBe('2')
			},
		)

		it(
			'sets metadata list correctly', () => {
				const store = useMetadataStore()
				const metadataList = [
					{
						id: '1',
						name: 'Test metadata name',
						title: 'Test metadata',
						summary: 'This is a test metadata',
						description: 'this is a very long description for test metadata',
						version: '0.0.1',
						properties: {
							sasds: {
								type: 'string',
								description: 'property description',
								format: 'a format',
								maxDate: '2025-07-13',
								required: false,
								default: false,
								cascadeDelete: false,
								exclusiveMinimum: '2',
							},
							gfdgds: {
								type: 'string',
								description: 'property description',
								format: 'a format',
								maxDate: '2025-07-13',
								required: false,
								default: false,
								cascadeDelete: false,
								exclusiveMinimum: '2',
							},
						},
					},
					{
						id: '2',
						name: 'Test metadata naming',
						title: 'Test metadata aaaa',
						summary: 'This is a test metadata baaa da daaa',
						description: 'this is a very long descriptio-',
						version: '0.0.1',
						properties: {},
					},
				]

				store.setMetaDataList(metadataList)

				expect(store.metaDataList).toHaveLength(metadataList.length)
				store.metaDataList.forEach(
					(item, index) => {
						expect(item).toEqual(metadataList[index])
					},
				)
			},
		)

		it(
			'get metadata property from key', () => {
				const store = useMetadataStore()
				const metadataItem = {
					id: '1',
					name: 'Test metadata name',
					title: 'Test metadata',
					summary: 'This is a test metadata',
					description: 'this is a very long description for test metadata',
					version: '0.0.1',
					properties: {
						sasds: {
							type: 'string',
							description: 'property description',
							format: 'a format',
							maxDate: '2025-07-13',
							$ref: '',
							required: false,
							default: false,
							cascadeDelete: false,
							exclusiveMinimum: '2',
						},
						gfdgds: {
							type: 'string',
							description: 'property description',
							format: 'a format',
							maxDate: '2025-07-13',
							required: false,
							default: false,
							cascadeDelete: false,
							exclusiveMinimum: '2',
						},
					},
				}

				store.setMetaDataItem(metadataItem)
				store.setMetadataDataKey('sasds')

				expect(store.metaDataItem).toEqual(metadataItem)
				expect(store.metadataDataKey).toBe('sasds')

				const properties = store.getMetadataPropertyKeys('sasds')

				expect(properties).toEqual(metadataItem.properties.sasds)
			},
		)
	},
)
