/* eslint-disable @typescript-eslint/no-explicit-any */
// TODO: double check this type for correct properties and optionals when stoplight updates - https://conduction.stoplight.io/docs/open-catalogi/fee989a9c8e3f-publication

import { TCatalogi } from '../'

export type TPublication = {
    id: string
	title: string
	summary: string
	description: string
	reference: string
	image: string
	category: string
	portal: string
	featured: boolean
    source: string
    status: 'Concept' | 'Published' | 'Withdrawn' | 'Archived' | 'Revised' | 'Rejected'
    organization: string
    themes: string[]
    data: Record<string, unknown>
    anonymization: {
        anonymized: boolean
        results: string
    }
    language: {
        code: string
        level: string
    }
    published: string | Date
    modified: string | Date
    license: string
    archive: {
        date: string | Date
    }
    geo: {
        type: 'Point' | 'LineString' | 'Polygon' | 'MultiPoint' | 'MultiLineString' | 'MultiPolygon'
        coordinates: [number, number]
    }
    '@self'?: object
    catalog: TCatalogi | any
    register: number | null
    schema: number | null
}
