/**
 * Mock data for Glossary entity testing
 * @module Entities
 * @package
 * @author Ruben Linde
 * @copyright 2024
 * @license AGPL-3.0-or-later
 * @version 1.0.0
 * @see {@link https://github.com/opencatalogi/opencatalogi}
 */

import { Glossary } from './glossary'
import { TGlossary } from './glossary.types'

export const mockGlossaryData = (): TGlossary[] => [
	{
		id: '1',
		title: 'API',
		summary: 'Application Programming Interface',
		description: 'A set of rules and protocols for building and interacting with software applications',
		externalLink: 'https://en.wikipedia.org/wiki/API',
		keywords: ['development', 'programming', 'integration'],
	},
	{
		id: '2',
		title: 'REST',
		summary: 'Representational State Transfer',
		description: 'An architectural style for designing networked applications',
		externalLink: '',
		keywords: [],
	},
	{
		id: '3',
		title: 'GraphQL',
		summary: 'Query Language for APIs',
		description: 'A query language for APIs and a runtime for fulfilling those queries with your existing data',
		externalLink: 'invalid-url',
		keywords: ['api', 'query', 'data'],
	},
]

export const mockGlossary = (data: TGlossary[] = mockGlossaryData()): TGlossary[] => data.map(item => new Glossary(item))
