/* eslint-disable no-console */
import { Publication } from './publication'
import { TPublication } from './publication.types'

describe('Directory Store', () => {
	it('create Publication entity with full data', () => {
		const publication = new Publication(testData[0])

		expect(publication).toBeInstanceOf(Publication)
		expect(publication).toEqual(testData[0])

		expect(publication.validate()).toBe(true)
	})

	it('create Publication entity with partial data', () => {
		const publication = new Publication(testData[1])

		expect(publication).toBeInstanceOf(Publication)
		expect(publication.id).toBe(testData[1].id)
		expect(publication.title).toBe(testData[1].title)
		expect(publication.summary).toBe(testData[1].summary)
		expect(publication.reference).toBe(testData[1].reference)
		expect(publication.description).toBe(testData[1].description)
		expect(publication.image).toBe(testData[1].image)
		expect(publication.category).toBe(testData[1].category)
		expect(publication.portal).toBe(testData[1].portal)
		expect(publication.catalogi).toBe(testData[1].catalogi)
		expect(publication.metaData).toBe(testData[1].metaData)
		expect(publication.publicationDate).toBe(testData[1].publicationDate)
		expect(publication.modified).toBe(testData[1].modified)
		expect(publication.featured).toBe(testData[1].featured)
		expect(publication.organization).toEqual(testData[1].organization)
		expect(publication.data).toEqual(testData[1].data)
		expect(publication.attachments).toEqual(testData[1].attachments)
		expect(publication.attachmentCount).toBe(testData[1].attachmentCount)
		expect(publication.schema).toBe('')
		expect(publication.status).toBe('')
		expect(publication.license).toBe('')
		expect(publication.themes).toBe(testData[1].themes)
		expect(publication.anonymization).toEqual(testData[1].anonymization)

		expect(publication.validate()).toBe(true)
	})

	it('create Publication entity with falsy data', () => {
		const publication = new Publication(testData[2])

		expect(publication).toBeInstanceOf(Publication)
		expect(publication).toEqual(testData[2])

		expect(publication.validate()).toBe(false)
	})
})

const testData: TPublication[] = [
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
	},
]
