/* eslint-disable no-console */
import { Publication } from './publication'
import { mockPublications } from './publication.mock'

describe('Directory Store', () => {
	it('create Publication entity with full data', () => {
		const publication = new Publication(mockPublications()[0])

		expect(publication).toBeInstanceOf(Publication)
		expect(publication).toEqual(mockPublications()[0])

		// log any errors
		!publication.validate().success && console.error(publication.validate().error)

		expect(publication.validate().success).toBe(true)
	})

	it('create Publication entity with partial data', () => {
		const publication = new Publication(mockPublications()[1])

		expect(publication).toBeInstanceOf(Publication)
		expect(publication).not.toBe(mockPublications()[1])
		expect(publication.status).toBe('Concept')

		// log any errors
		!publication.validate().success && console.error(publication.validate().error)

		expect(publication.validate().success).toBe(true)
	})

	it('create Publication entity with falsy data', () => {
		const publication = new Publication(mockPublications()[2])

		expect(publication).toBeInstanceOf(Publication)
		expect(publication).toEqual(mockPublications()[2])

		// log any errors
		!publication.validate().success && console.error(publication.validate().error)

		expect(publication.validate().success).toBe(false)
	})
})
