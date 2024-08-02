import { IPublicationsServiceMock } from './publications.types'

export const mockPublicationsService = (): IPublicationsServiceMock => ({
  getAll: jest.fn(),
  getOneById: jest.fn(),
  create: jest.fn()
})