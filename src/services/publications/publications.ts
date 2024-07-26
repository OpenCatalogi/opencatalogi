import {
    TPublication,
    Publication
  } from '@/entities'
  import { IPublicationsService } from './publications.types'
  
  export class PublicationsService implements IPublicationsService {
    constructor (private readonly data: TPublication[]) { }
  
    getAll (): TPublication[] {
      return this.data
    }
  
    getOneById (id: string): TPublication | undefined {
      const data = this.data.find(publication => publication.id === id)
      if (!data) {
        return
      }
      return new Publication(data)
    }
  
    create (data: TPublication): TPublication {
      const publication = new Publication(data)
      if (!publication.validate()) {
        throw new Error('Publication data is not valid')
      }
  
      return publication
    }
  }
  