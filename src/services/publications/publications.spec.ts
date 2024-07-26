import { mockPublications } from '@/entities'
import { PublicationsService } from './publications'

describe('>>> Publications Service', () => {
  const service = new PublicationsService(mockPublications())

  describe('>> getMany', () => {
    it('should return all data', () => {
      expect(service.getAll()).toEqual(mockPublications())
    })
  })

  describe('>> getOneById', () => {
    it('should return one publication by provided id', () => {
      const publication = mockPublications()[0]
      const id = publication.id
      expect(service.getOneById(id)).toEqual(publication)
    })

    it('should return undefined if no publication found', () => {
      expect(service.getOneById('111111')).toBeUndefined()
    })
  })

  describe('>> create', () => {
    const data = mockPublications()[0]
    it('should add new publication and return it', () => {
      const publication = mockPublications()[0]
      const id = publication.id as string

      const newPublication = service.create(data)
      expect(newPublication.id).toEqual(publication.id)
    })

    it('should throw an error if publication is not valid', () => {
      const data = {
        ...mockPublications()[0],
        title: ''
      }
      expect(() => { service.create(data) }).toThrow()
    })
  })
})