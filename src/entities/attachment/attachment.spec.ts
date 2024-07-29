/* eslint-disable no-console */
import { Attachment } from './attachment'
import { TAttachment } from './attachment.types'

describe('Attachment Store', () => {
	it('create Attachment entity with full data', () => {
		const attachment = new Attachment(testData[0])

		expect(attachment).toBeInstanceOf(Attachment)
		expect(attachment).toEqual(testData[0])

		expect(attachment.validate()).toBe(true)
	})

	it('create Attachment entity with partial data', () => {
		const attachment = new Attachment(testData[1])

		expect(attachment).toBeInstanceOf(Attachment)
		expect(attachment.id).toBe(testData[1].id)
		expect(attachment.reference).toBe(testData[1].reference)
		expect(attachment.title).toBe(testData[1].title)
		expect(attachment.summary).toBe(testData[1].summary)
		expect(attachment.description).toBe(testData[1].description)
		expect(attachment.labels).toBe(testData[1].labels)
		expect(attachment.accessURL).toBe(testData[1].accessURL)
		expect(attachment.downloadURL).toBe(testData[1].downloadURL)
		expect(attachment.type).toBe(testData[1].type)
		expect(attachment.extension).toBe(testData[1].extension)
		expect(attachment.size).toBe(testData[1].size)
		expect(attachment.anonymization).toBe(testData[1].anonymization)
		expect(attachment.language).toBe(testData[1].language)
		expect(attachment.versionOf).toBe(testData[1].versionOf)
		expect(attachment.hash).toBe('')
		expect(attachment.published).toBe('')
		expect(attachment.modified).toBe('')
		expect(attachment.license).toBe(testData[1].license)

		expect(attachment.validate()).toBe(true)
	})

	it('create Attachment entity with falsy data', () => {
		const attachment = new Attachment(testData[2])

		expect(attachment).toBeInstanceOf(Attachment)
		expect(attachment).toEqual(testData[2])

		expect(attachment.validate()).toBe(false)
	})
})

const testData: TAttachment[] = [
	{ // full data
		id: '1',
		reference: 'ref1',
		title: 'test 1',
		summary: 'a short form summary',
		description: 'a really really long description about this catalogus',
		labels: ['tag1'],
		accessURL: 'https://example.com/access',
		downloadURL: 'https://example.com/download',
		type: 'document',
		extension: 'pdf',
		size: 1024,
		anonymization: { anonymized: true, results: 'success' },
		language: { code: 'en', level: 'native' },
		versionOf: '8b4d8937-b356-46ae-b03b-bbce7070c7e6',
		hash: 'abc123',
		published: '2024-01-01',
		modified: '2024-01-02',
		license: 'MIT',
	},
	{ // partial data
		id: '2',
		reference: 'ref2',
		title: 'test 2',
		summary: 'a short form summary',
		description: 'a really really long description about this catalogus',
		labels: ['tag2'],
		accessURL: 'https://example.com/access',
		downloadURL: 'https://example.com/download',
		type: 'document',
		extension: 'pdf',
		size: 1024,
		anonymization: { anonymized: true, results: 'success' },
		language: { code: 'en', level: 'native' },
		versionOf: '2459551d-f0dd-4354-821b-169304cde611',
		license: 'MIT',
	},
	{ // invalid data
		id: '3',
		reference: 'ref3',
		title: '',
		summary: 'a short form summary',
		description: 'a really really long description about this catalogus',
		labels: ['tag3'],
		accessURL: 'https://example.com/access',
		downloadURL: 'https://example.com/download',
		type: 'document',
		extension: 'pdf',
		size: 1024,
		anonymization: { anonymized: true, results: 'success' },
		language: { code: 'en', level: 'native' },
		versionOf: '83b2914d-8561-479c-8d1f-e58a0f1ec88f',
		hash: 'abc123',
		published: '2024-01-01',
		modified: '2024-01-02',
		license: 'MIT',
	},
]
