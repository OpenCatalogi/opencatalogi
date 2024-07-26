import { PublicationsService } from './publications'
import data from './data.json'
import { IProvider } from './provider.types'

export const provider = (): IProvider => ({
  publications: new PublicationsService(data)
})
