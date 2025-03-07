export type TCatalogi = {
    id: string
    title: string
    summary: string
    description: string
    image: string
    listed: boolean
    organization: string // it is supposed to be TOrganization according to the stoplight, but reality is a bit different
    publicationTypes: string[]
}
