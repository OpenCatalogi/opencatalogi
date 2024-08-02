import {
    TPublication,
    Publication
  } from '@/entities'
  import { IPublicationsService } from './publications.types'
  
  export class PublicationsService implements IPublicationsService {
    constructor (private readonly data: TPublication[]) { }
  
//  @TODO
    getAll (): TPublication[] {
        fetch('/index.php/apps/opencatalogi/api/publications', {
            method: 'GET',
        })
        .then((response) => {
            return response.json();
        })
        .then((data) => {
            const publications = data?.results.map((publicationItem: TPublication) => {
                return new Publication(publicationItem);
            }) as TPublication[];

            this.data.splice(0, this.data.length, ...publications); // Mutate the array without reassigning

            return this.data;
        })
        .catch((err) => {
            console.error(err);
            return [];
        });

        return this.data;
    }

//  @TODO
    getOneById (id: string): TPublication | undefined {
      const data = this.data.find(publication => publication.id === id)
      if (!data) {
        return
      }
      return new Publication(data)
    }
  
//  @TODO
    create (data: TPublication): TPublication {
      const publication = new Publication(data)
      if (!publication.validate()) {
        throw new Error('Publication data is not valid')
      }
  
      return publication
    }
  }
  