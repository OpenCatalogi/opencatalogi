/* eslint-disable no-console */
import { setActivePinia, createPinia } from 'pinia'

import { usePublicationStore } from './publication.js'
import { Attachment, Publication } from '../../entities/index.js'

describe('Publication Store', () => {
	beforeEach(() => {
		setActivePinia(createPinia())
	})

	it('sets publication item correctly', () => {
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
	})

	it('sets publication list correctly', () => {
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
	})

	// TODO: fix this - make sure you can retrieve the data from this key correctly
	it('set publication data.data property key correctly', () => {
		const store = usePublicationStore()

		store.setPublicationDataKey('contactPoint')

		expect(store.publicationDataKey).toBe('contactPoint')
	})

	it('set attachment item correctly', () => {
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
	})
})

const testData = [
	{ // full data
		id: '1',
		title: 'test 1',
		summary: 'a short form summary',
		description: 'a really really long description about this catalogus',
		reference: 'ref1',
		image: 'https://example.com/image.jpg',
		category: 'category1',
		portal: 'portal1',
		catalogi: 'catalogi1',
		metaData: 'meta1',
		featured: true,
		organization: {
			type: 'software',
			$ref: 'any',
			format: 'ani',
			description: 'a very long description',
		},
		schema: 'https://example.com/schema',
		status: 'status1',
		attachments: {
			type: 'file',
			items: {
				$ref: 'any',
			},
			format: 'jpeg',
		},
		attachmentCount: 1,
		themes: ['theme1'],
		data: {
			type: 'object',
			required: true,
		},
		anonymization: {
			type: 'string',
			format: 'something',
			description: '🤷‍♂️',
			$ref: 'stringy',
		},
		languageObject: {
			type: 'string',
			format: 'something',
			description: 'a language',
			$ref: 'https://langy.org',
		},
		publicationDate: new Date(2024, 7, 24).toISOString(),
		modified: new Date(2024, 1, 2).toISOString(),
		license: {
			type: 'MIT',
		},
	},
	{ // partial data
		id: '2',
		title: 'test 2',
		summary: 'a short form summary',
		description: 'a really really long description about this catalogus',
		reference: 'ref2',
		image: 'https://example.com/image.jpg',
		category: 'category2',
		portal: 'portal2',
		catalogi: 'catalogi2',
		featured: true,
		organization: {},
		schema: 'https://example.com/schema',
		status: 'status1',
		attachmentCount: 1,
		themes: ['theme1'],
		data: {
			type: 'object',
			required: true,
		},
		anonymization: {
			type: 'string',
			$ref: 'stringy',
		},
		languageObject: {
			type: 'string',
			format: 'something',
			description: 'a language',
			$ref: 'https://langy.org',
		},
		publicationDate: new Date(2024, 7, 24).toISOString(),
		modified: new Date(2024, 1, 2).toISOString(),
		license: {
			type: 'MIT',
		},
	},
	{ // invalid data
		id: '1',
		title: '',
		summary: 'a short form summary',
		description: 'a really really long description about this catalogus',
		reference: 'ref1',
		image: 'https://example.com/image.jpg',
		category: 'category1',
		portal: 'portal1',
		catalogi: 'catalogi1',
		metaData: 'meta1',
		featured: true,
		organization: {
			type: 'software',
			$ref: 'any',
			format: 'ani',
			description: 'a very long description',
		},
		schema: 'https://example.com/schema',
		status: 'status1',
		attachments: {
			type: 'file',
			items: {
				$ref: 'any',
			},
			format: 'jpeg',
		},
		attachmentCount: 1,
		themes: ['theme1'],
		data: {
			type: 'object',
			required: true,
		},
		anonymization: {
			type: 'string',
			format: 'something',
			description: '🤷‍♂️',
			$ref: 'stringy',
		},
		languageObject: {
			type: 'string',
			format: 'something',
			description: 'a language',
			$ref: 'https://langy.org',
		},
		publicationDate: new Date(2024, 7, 24).toISOString(),
		modified: new Date(2024, 1, 2).toISOString(),
		license: {
			type: 'MIT',
		},
	},
]

const attachmentTestData = [
	{ // full data
		id: '1',
		reference: 'ref1',
		title: 'this is quite a long title',
		summary: 'the limitation of needing at least 50 characters is annoying',
		description: 'a really really long description about this catalogus',
		labels: ['tag1'],
		accessURL: 'https://example.com/access',
		downloadURL: 'https://example.com/download',
		type: 'document',
		extension: 'pdf',
		size: 1024,
		anonymization: { anonymized: true, results: 'success' },
		language: { code: 'en', level: 'A1' },
		versionOf: '8b4d8937-b356-46ae-b03b-bbce7070c7e6',
		hash: 'abc123',
		published: '2024-01-01',
		modified: '2024-01-02',
		license: 'MIT',
	},
	{ // partial data
		id: '2',
		reference: 'ref2',
		title: 'this is an even longer title',
		summary: 'the limitation of needing at least 50 characters is annoying',
		description: 'a really really long description about this catalogus',
		labels: ['tag2'],
		accessURL: 'https://example.com/access',
		downloadURL: 'https://example.com/download',
		type: 'document',
		extension: 'pdf',
		size: 1024,
		anonymization: { anonymized: true, results: 'success' },
		language: { code: 'en', level: 'A1' },
		versionOf: '2459551d-f0dd-4354-821b-169304cde611',
		license: 'MIT',
	},
	{ // invalid data
		id: '3',
		reference: 'ref3',
		title: '',
		summary: 'the limitation of needing at least 50 characters is annoying',
		description: 'a really really long description about this catalogus',
		labels: ['tag3'],
		accessURL: 'https://example.com/access',
		downloadURL: 'https://example.com/download',
		type: 'document',
		extension: 'pdf',
		size: 1024,
		anonymization: { anonymized: true, results: 'success' },
		language: { code: 'en', level: 'A1' },
		versionOf: '83b2914d-8561-479c-8d1f-e58a0f1ec88f',
		hash: 'abc123',
		published: '2024-01-01',
		modified: '2024-01-02',
		license: 'MIT',
	},
]
