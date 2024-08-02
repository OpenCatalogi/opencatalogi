import { PublicationsService } from './publications'
import { IProvider } from './provider.types'

export const provider = (): IProvider => ({
  publications: new PublicationsService([])
})
