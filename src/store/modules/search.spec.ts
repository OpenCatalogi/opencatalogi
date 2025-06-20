/* eslint-disable no-console */
import { createPinia, setActivePinia } from 'pinia'

import { useSearchStore } from './search'

describe('Search Store', () => {
	beforeEach(() => {
		setActivePinia(createPinia())
	})

	it('set search term correctly', () => {
		const store = useSearchStore()

		store.setSearchTerm('cata')
		expect(store.getSearchTerm).toBe('cata')

		store.setSearchTerm('woo')
		expect(store.getSearchTerm).toBe('woo')

		store.setSearchTerm('foo bar')
		expect(store.getSearchTerm).toBe('foo bar')
	})

	it('set filters correctly', () => {
		const store = useSearchStore()

		store.setFilters({ category: 'test', status: 'published' })
		expect(store.getFilters).toEqual({ category: 'test', status: 'published' })
	})

	it('clear search correctly', () => {
		const store = useSearchStore()

		store.setSearchTerm('Lorem ipsum dolor sit amet')
		expect(store.getSearchTerm).toBe('Lorem ipsum dolor sit amet')

		store.clearSearch()

		expect(store.getSearchTerm).toBe('')
		expect(store.getFilters).toEqual({})
		expect(store.getSearchResults).toEqual([])
	})

	it('set view mode correctly', () => {
		const store = useSearchStore()

		store.setViewMode('table')
		expect(store.getViewMode).toBe('table')

		store.setViewMode('cards')
		expect(store.getViewMode).toBe('cards')
	})

	it('toggle publication selection correctly', () => {
		const store = useSearchStore()

		store.togglePublicationSelection('pub1', true)
		expect(store.getSelectedPublications).toContain('pub1')

		store.togglePublicationSelection('pub2', true)
		expect(store.getSelectedPublications).toContain('pub2')

		store.togglePublicationSelection('pub1', false)
		expect(store.getSelectedPublications).not.toContain('pub1')
		expect(store.getSelectedPublications).toContain('pub2')
	})

	it('clear all selections correctly', () => {
		const store = useSearchStore()

		store.togglePublicationSelection('pub1', true)
		store.togglePublicationSelection('pub2', true)
		expect(store.getSelectedPublications.length).toBe(2)

		store.clearAllSelections()
		expect(store.getSelectedPublications).toEqual([])
	})
})
