/**
 * Test suite for Glossary entity
 * @module Entities
 * @package
 * @author Ruben Linde
 * @copyright 2024
 * @license AGPL-3.0-or-later
 * @version 1.0.0
 * @see {@link https://github.com/opencatalogi/opencatalogi}
 */

/* eslint-disable no-console */
import { Glossary } from './glossary'
import { mockGlossary } from './glossary.mock'

describe('Glossary Store', () => {
	it('create Glossary entity with full data', () => {
		const glossary = new Glossary(mockGlossary()[0])

		expect(glossary).toBeInstanceOf(Glossary)
		expect(glossary).toEqual(mockGlossary()[0])

		expect(glossary.validate().success).toBe(true)
	})

	it('create Glossary entity with partial data', () => {
		const glossary = new Glossary(mockGlossary()[1])

		expect(glossary).toBeInstanceOf(Glossary)
		expect(glossary).toEqual(mockGlossary()[1])

		expect(glossary.validate().success).toBe(true)
	})

	it('create Glossary entity with falsy data', () => {
		const glossary = new Glossary(mockGlossary()[2])

		expect(glossary).toBeInstanceOf(Glossary)
		expect(glossary).toEqual(mockGlossary()[2])

		expect(glossary.validate().success).toBe(false)
	})
})
