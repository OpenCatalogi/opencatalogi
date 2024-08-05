/* eslint-disable no-console */
import { setActivePinia, createPinia } from 'pinia'

import { usePublicationStore } from './publication.js'
import { Attachment, Publication } from '../../entities/index.js'

describe(
	'Metadata Store', () => {
		beforeEach(
			() => {
				setActivePinia(createPinia())
			},
		)

		it(
			'sets publication item correctly', () => {
				const store = usePublicationStore()

				store.setPublicationItem(testData[0])

				expect(store.publicationItem).toBeInstanceOf(Publication)
				expect(store.publicationItem).toEqual(testData[0])
				expect(store.publicationItem.validate()).toBe(true)

				store.setPublicationItem(testData[1])

				expect(store.publicationItem).toBeInstanceOf(Publication)
				expect(store.publicationItem).not.toEqual(testData[1])
				expect(store.publicationItem.validate()).toBe(true)

				store.setPublicationItem(testData[2])

				expect(store.publicationItem).toBeInstanceOf(Publication)
				expect(store.publicationItem).toEqual(testData[2])
				expect(store.publicationItem.validate()).toBe(false)
			},
		)

		it(
			'sets publication list correctly', () => {
				const store = usePublicationStore()

				store.setPublicationList(testData)

				expect(store.publicationList).toHaveLength(testData.length)

				expect(store.publicationList[0]).toBeInstanceOf(Publication)
				expect(store.publicationList[0]).toEqual(testData[0])
				expect(store.publicationList[0].validate()).toBe(true)

				expect(store.publicationList[1]).toBeInstanceOf(Publication)
				expect(store.publicationList[1]).not.toEqual(testData[1])
				expect(store.publicationList[1].validate()).toBe(true)

				expect(store.publicationList[2]).toBeInstanceOf(Publication)
				expect(store.publicationList[2]).toEqual(testData[2])
				expect(store.publicationList[2].validate()).toBe(false)
			},
		)

		// TODO: fix this
		it(
			'set publication data.data property key correctly', () => {
				const store = usePublicationStore()

				store.setPublicationDataKey('contactPoint')

				expect(store.publicationDataKey).toBe('contactPoint')
			},
		)

		it(
			'set attachment item correctly', () => {
				const store = usePublicationStore()

				store.setAttachmentItem(attachmentTestData[0])

				expect(store.attachmentItem).toBeInstanceOf(Attachment)
				expect(store.attachmentItem).toEqual(attachmentTestData[0])
				expect(store.attachmentItem.validate()).toBe(true)

				store.setAttachmentItem(attachmentTestData[1])

				expect(store.attachmentItem).toBeInstanceOf(Attachment)
				expect(store.attachmentItem).not.toEqual(attachmentTestData[1])
				expect(store.attachmentItem.validate()).toBe(true)

				store.setAttachmentItem(attachmentTestData[2])

				expect(store.attachmentItem).toBeInstanceOf(Attachment)
				expect(store.attachmentItem).toEqual(attachmentTestData[2])
				expect(store.attachmentItem.validate()).toBe(false)
			},
		)
	},
)

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

const attachmentTestData = [
	{ // full data
		id: '9044ab1e-cf5a-490a-be74-6be7a0c48a5f',
		reference: 'ref1',
		title: 'test 1',
		summary: 'a short form summary',
		description: 'a really really long description about this catalogus',
		labels: [{ tag: 'tag1' }],
		accessURL: 'https://example.com/access',
		downloadURL: 'https://example.com/download',
		type: 'document',
		extension: 'pdf',
		size: 1024,
		anonymization: { anonymized: 'yes', results: 'success' },
		language: { code: 'en', level: 'native' },
		versionOf: 'v1.0',
		hash: 'abc123',
		published: '2024-01-01',
		modified: '2024-01-02',
		license: 'MIT',
	},
	{ // partial data
		id: 'f849f287-492d-4100-91e1-1c4137f0abb5',
		reference: 'ref2',
		title: 'test 2',
		summary: 'a short form summary',
		description: 'a really really long description about this catalogus',
		labels: [{ tag: 'tag2' }],
		accessURL: 'https://example.com/access',
		downloadURL: 'https://example.com/download',
		type: 'document',
		extension: 'pdf',
		size: 1024,
		anonymization: { anonymized: 'yes', results: 'success' },
		language: { code: 'en', level: 'native' },
		versionOf: 'v1.0',
		license: 'MIT',
	},
	{ // invalid data
		id: 'e193ea6b-1222-44cf-a71c-6ddcc232a79b',
		reference: 'ref3',
		title: '',
		summary: 'a short form summary',
		description: 'a really really long description about this catalogus',
		labels: [{ tag: 'tag3' }],
		accessURL: 'https://example.com/access',
		downloadURL: 'https://example.com/download',
		type: 'document',
		extension: 'pdf',
		size: 1024,
		anonymization: { anonymized: 'yes', results: 'success' },
		language: { code: 'en', level: 'native' },
		versionOf: 'v1.0',
		hash: 'abc123',
		published: '2024-01-01',
		modified: '2024-01-02',
		license: 'MIT',
	},
]
