/* eslint-disable no-console */
import { Publication } from './publication'
import { mockPublicationsData } from './publication.mock'

describe('Directory Store', () => {
	it('create Publication entity with full data', () => {
		const publication = new Publication(mockPublicationsData()[0])

		expect(publication).toBeInstanceOf(Publication)
		expect(publication).toEqual(mockPublicationsData()[0])

		expect(publication.validate()).toBe(true)
	})

	it('create Publication entity with partial data', () => {
		const publication = new Publication(mockPublicationsData()[1])

		expect(publication).toBeInstanceOf(Publication)
		expect(publication.id).toBe(mockPublicationsData()[1].id)
		expect(publication.title).toBe(mockPublicationsData()[1].title)
		expect(publication.summary).toBe(mockPublicationsData()[1].summary)
		expect(publication.reference).toBe(mockPublicationsData()[1].reference)
		expect(publication.description).toBe(mockPublicationsData()[1].description)
		expect(publication.image).toBe(mockPublicationsData()[1].image)
		expect(publication.category).toBe(mockPublicationsData()[1].category)
		expect(publication.portal).toBe(mockPublicationsData()[1].portal)
		expect(publication.catalogi).toBe(mockPublicationsData()[1].catalogi)
		expect(publication.metaData).toBe(mockPublicationsData()[1].metaData)
		expect(publication.publicationDate).toBe(mockPublicationsData()[1].publicationDate)
		expect(publication.modified).toBe(mockPublicationsData()[1].modified)
		expect(publication.featured).toBe(mockPublicationsData()[1].featured)
		expect(publication.organization).toEqual(mockPublicationsData()[1].organization)
		expect(publication.data).toEqual(mockPublicationsData()[1].data)
		expect(publication.attachments).toEqual(mockPublicationsData()[1].attachments)
		expect(publication.attachmentCount).toBe(mockPublicationsData()[1].attachmentCount)
		expect(publication.schema).toBe('')
		expect(publication.status).toBe('')
		expect(publication.license).toBe('')
		expect(publication.themes).toBe(mockPublicationsData()[1].themes)
		expect(publication.anonymization).toEqual(mockPublicationsData()[1].anonymization)

		expect(publication.validate()).toBe(true)
	})

	it('create Publication entity with falsy data', () => {
		const publication = new Publication(mockPublicationsData()[2])

		expect(publication).toBeInstanceOf(Publication)
		expect(publication).toEqual(mockPublicationsData()[2])

		expect(publication.validate()).toBe(false)
	})
})