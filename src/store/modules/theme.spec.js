/* eslint-disable no-console */
import { setActivePinia, createPinia } from 'pinia'

import { useThemeStore } from './theme.js'
import { Theme } from '../../entities/index.js'

describe('Metadata Store', () => {
	beforeEach(() => {
		setActivePinia(createPinia())
	})

	it('sets publication item correctly', () => {
		const store = useThemeStore()

		store.setThemeItem(testData[0])

		expect(store.themeItem).toBeInstanceOf(Theme)
		expect(store.themeItem).toEqual(testData[0])
		expect(store.themeItem.validate()).toBe(true)

		store.setThemeItem(testData[1])

		expect(store.themeItem).toBeInstanceOf(Theme)
		expect(store.themeItem).not.toEqual(testData[1])
		expect(store.themeItem.validate()).toBe(true)

		store.setThemeItem(testData[2])

		expect(store.themeItem).toBeInstanceOf(Theme)
		expect(store.themeItem).toEqual(testData[2])
		expect(store.themeItem.validate()).toBe(false)
	})

	it('sets publication list correctly', () => {
		const store = useThemeStore()

		store.setThemeList(testData)

		expect(store.themeList).toHaveLength(testData.length)

		expect(store.themeList[0]).toBeInstanceOf(Theme)
		expect(store.themeList[0]).toEqual(testData[0])
		expect(store.themeList[0].validate()).toBe(true)

		expect(store.themeList[1]).toBeInstanceOf(Theme)
		expect(store.themeList[1]).not.toEqual(testData[1])
		expect(store.themeList[1].validate()).toBe(true)

		expect(store.themeList[2]).toBeInstanceOf(Theme)
		expect(store.themeList[2]).toEqual(testData[2])
		expect(store.themeList[2].validate()).toBe(false)
	})
})

const testData = [
	{ // full data
		id: '1',
		reference: 'ref1',
		title: 'test 1',
		summary: 'a short form summary',
		description: 'a really really long description about this catalogus',
		image: 'https://example.com/image.jpg',
		category: 'category1',
		portal: 'portal1',
		catalogi: 'catalogi1',
		metaData: 'meta1',
		publicationDate: '2024-01-01',
		modified: '2024-01-02',
		featured: true,
		organization: [{ name: 'Org1' }],
		data: [{ key: 'value1' }],
		attachments: ['attachment1'],
		attachmentCount: 1,
		schema: 'schema1',
		status: 'status1',
		license: 'MIT',
		themes: 'theme1',
		anonymization: { anonymized: 'yes', results: 'success' },
		language: { code: 'en', level: 'native' },
	},
	{ // partial data
		id: '2',
		reference: 'ref2',
		title: 'test 2',
		summary: 'a short form summary',
		description: 'a really really long description about this catalogus',
		image: 'https://example.com/image.jpg',
		category: 'category2',
		portal: 'portal2',
		catalogi: 'catalogi2',
		metaData: 'meta2',
		publicationDate: '2024-01-01',
		modified: '2024-01-02',
		featured: true,
		organization: [{ name: 'Org1' }],
		data: [{ key: 'value1' }],
		attachments: ['attachment1'],
		attachmentCount: 1,

		themes: 'theme1',
		anonymization: { anonymized: 'yes', results: 'success' },
		language: { code: 'en', level: 'native' },
	},
	{ // invalid data
		id: '3',
		reference: 'ref3',
		title: '',
		summary: 'a short form summary',
		description: 'a really really long description about this catalogus',
		image: 'https://example.com/image.jpg',
		category: 'category3',
		portal: 'portal3',
		catalogi: 'catalogi3',
		metaData: 'meta3',
		publicationDate: '2024-01-01',
		modified: '2024-01-02',
		featured: true,
		organization: [{ name: 'Org1' }],
		data: [{ key: 'value1' }],
		attachments: ['attachment1'],
		attachmentCount: 1,
		schema: 'schema1',
		status: 'status1',
		license: 'MIT',
		themes: 'theme1',
		anonymization: { anonymized: 'yes', results: 'success' },
		language: { code: 'en', level: 'native' },
	},
]
