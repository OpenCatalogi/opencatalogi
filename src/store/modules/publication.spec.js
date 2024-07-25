/* eslint-disable no-console */
import { setActivePinia, createPinia } from 'pinia'

import { usePublicationStore } from './publication.js'

describe('Metadata Store', () => {
	beforeEach(() => {
		setActivePinia(createPinia())
	})

	it('sets publication item correctly', () => {
		const store = usePublicationStore()

		store.setPublicationItem(testData[0])

		expect(store.publicationItem).toEqual(testData[0])
	})

	it('sets publication list correctly', () => {
		const store = usePublicationStore()

		store.setPublicationList(testData)

		expect(store.publicationList).toEqual(testData)
	})

	// TODO: fix this
	it('set publication data.data property key correctly', () => {
		const store = usePublicationStore()

		store.setPublicationItem(testData[0])
		store.setPublicationDataKey('contactPoint')

		expect(store.publicationItem).toEqual(testData[0])
		expect(store.publicationDataKey).toBe('contactPoint')
	})

	// TODO: fix this
	it('set attachment item correctly', () => {
		const store = usePublicationStore()

		store.setAttachmentItem(testData[0].attachments[0])

		expect(store.attachmentItem.id).toBe(testData[0].attachments[0].id)
		expect(store.attachmentItem.reference).toBe('') // some of these are '' because its not in the test data
		expect(store.attachmentItem.title).toBe(testData[0].attachments[0].title)
		expect(store.attachmentItem.summary).toBe('')
		expect(store.attachmentItem.description).toBe(testData[0].attachments[0].description)
		expect(store.attachmentItem.labels).toEqual([])
		expect(store.attachmentItem.accessURL).toBe(testData[0].attachments[0].accessURL)
		expect(store.attachmentItem.downloadURL).toBe(testData[0].attachments[0].downloadURL)
		expect(store.attachmentItem.type).toBe(testData[0].attachments[0].type)
		expect(store.attachmentItem.extension).toBe('')
		expect(store.attachmentItem.size).toBe(0)
		// attachmentItem.anonymization
		expect(store.attachmentItem.anonymization.anonymized).toBe(false)
		expect(store.attachmentItem.anonymization.results).toBe('')
		// attachmentItem.language
		expect(store.attachmentItem.language.code).toBe('')
		expect(store.attachmentItem.language.level).toBe('')
		expect(store.attachmentItem.version_of).toBe(false)
		expect(store.attachmentItem.hash).toBe(false)
		expect(store.attachmentItem.published).toBe(testData[0].attachments[0].published)
		expect(store.attachmentItem.modified).toBe(testData[0].attachments[0].modified)
		expect(store.attachmentItem.license).toBe(testData[0].attachments[0].license)
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
