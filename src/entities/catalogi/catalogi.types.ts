import { TOrganisation } from '../organisation'

export type TCatalogi = {
    id: string
    title: string
    summary: string
    description: string
    image: string
    listed: boolean
    organisation: TOrganisation
    metadata: string[]
}
