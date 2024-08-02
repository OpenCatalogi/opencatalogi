import { TPublication } from '@/entities'

export interface IPublicationsService {
  getAll (): TPublication[]
  getOneById (id: string): TPublication | undefined
  create (data: TPublication): TPublication
}

export interface IPublicationsServiceMock {
  getAll: jest.Mock <TPublication[]>
  getOneById: jest.Mock<TPublication | undefined>
  create: jest.Mock <TPublication>
}
