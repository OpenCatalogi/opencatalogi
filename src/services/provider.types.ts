import { IPublicationsService, IPublicationsServiceMock } from './publications'

export interface IProvider {
  publications: IPublicationsService
}

export interface IProviderMock {
    publications: IPublicationsServiceMock
}
