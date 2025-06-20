/* eslint-disable no-console */
import { defineStore } from 'pinia'

export const useSearchStore = defineStore('search', {
	state: () => ({
		// Search parameters
		searchTerm: '',
		filters: {},
		ordering: {}, // New: ordering criteria { field: 'ASC'|'DESC' }
		
		// Results and pagination
		searchResults: [],
		pagination: {
			page: 1,
			pages: 1,
			total: 0,
			limit: 20,
			offset: 0,
		},
		facets: {},
		facetable: {},
		
		// Loading and error states
		loading: false,
		error: null,
		
		// View settings
		viewMode: 'cards', // 'cards' or 'table'
		selectedPublications: [],
	}),
	
	getters: {
		getSearchResults: (state) => state.searchResults,
		getPagination: (state) => state.pagination,
		getFacets: (state) => state.facets,
		getFacetable: (state) => state.facetable,
		isLoading: (state) => state.loading,
		getError: (state) => state.error,
		getSearchTerm: (state) => state.searchTerm,
		getFilters: (state) => state.filters,
		getOrdering: (state) => state.ordering,
		getViewMode: (state) => state.viewMode,
		getSelectedPublications: (state) => state.selectedPublications,
	},
	
	actions: {
		/**
		 * Set the search term
		 * 
		 * @param searchTerm The search term to set
		 */
		setSearchTerm(searchTerm: string) {
			this.searchTerm = searchTerm
			console.log('Search term set to:', searchTerm)
		},
		
		/**
		 * Set filters for the search
		 * 
		 * @param filters Object containing filter key-value pairs
		 */
		setFilters(filters: object) {
			this.filters = { ...this.filters, ...filters }
			console.log('Search filters updated:', this.filters)
		},
		
		/**
		 * Clear a specific filter
		 * 
		 * @param filterKey The key of the filter to clear
		 */
		clearFilter(filterKey: string) {
			const { [filterKey]: removed, ...remainingFilters } = this.filters
			this.filters = remainingFilters
			console.log('Filter cleared:', filterKey, 'Remaining filters:', this.filters)
		},
		
		/**
		 * Clear all filters
		 */
		clearAllFilters() {
			this.filters = {}
			console.log('All filters cleared')
		},
		
		/**
		 * Set ordering for a field
		 * 
		 * @param field The field to order by
		 * @param direction ASC or DESC
		 */
		setOrdering(field: string, direction: 'ASC' | 'DESC') {
			this.ordering = { ...this.ordering, [field]: direction }
			console.log('Ordering updated:', this.ordering)
		},
		
		/**
		 * Remove ordering for a field
		 * 
		 * @param field The field to remove ordering from
		 */
		removeOrdering(field: string) {
			const { [field]: removed, ...remainingOrdering } = this.ordering
			this.ordering = remainingOrdering
			console.log('Ordering removed for field:', field, 'Remaining ordering:', this.ordering)
		},
		
		/**
		 * Clear all ordering
		 */
		clearOrdering() {
			this.ordering = {}
			console.log('All ordering cleared')
		},
		
		/**
		 * Set view mode (cards or table)
		 * 
		 * @param mode The view mode to set
		 */
		setViewMode(mode: 'cards' | 'table') {
			this.viewMode = mode
			console.log('View mode set to:', mode)
		},
		
		/**
		 * Toggle selection of a publication
		 * 
		 * @param publicationId The ID of the publication to toggle
		 * @param selected Whether the publication should be selected
		 */
		togglePublicationSelection(publicationId: string, selected: boolean) {
			if (selected) {
				if (!this.selectedPublications.includes(publicationId)) {
					this.selectedPublications.push(publicationId)
				}
			} else {
				this.selectedPublications = this.selectedPublications.filter((id: string) => id !== publicationId)
			}
		},
		
		/**
		 * Select all publications
		 */
		selectAllPublications() {
			this.selectedPublications = this.searchResults.map((pub: any) => pub.id)
		},
		
		/**
		 * Clear all selections
		 */
		clearAllSelections() {
			this.selectedPublications = []
		},
		
		/**
		 * Load initial search results (without search term)
		 */
		async loadInitialResults() {
			console.log('Loading initial search results with facets...')
			await this.searchPublications({ _limit: 20, _page: 1 })
		},
		
		/**
		 * Perform a search using the SearchController API
		 * 
		 * @param params Optional search parameters
		 */
		async searchPublications(params: Record<string, any> = {}) {
			this.loading = true
			this.error = null
			
			try {
				// Build search parameters
				const searchParams = new URLSearchParams({
					// Add search term if provided
					...(this.searchTerm && { _search: this.searchTerm }),
					
					// Add pagination
					_page: (params._page as string) || this.pagination.page.toString(),
					_limit: (params._limit as string) || this.pagination.limit.toString(),
					
					// Enable facets to get all possible filter options
					_facetable: 'true',
					
					// Add filters
					...this.filters,
					
					// Add any additional parameters
					...params,
				})
				
				// Add ordering parameters
				Object.entries(this.ordering).forEach(([field, direction]) => {
					searchParams.append(`_order[${field}]`, direction as string)
				})
				
				console.log('Searching publications with params:', searchParams.toString())
				
				// Make API call to SearchController
				const response = await fetch(`/index.php/apps/opencatalogi/api/search?${searchParams.toString()}`, {
					method: 'GET',
					headers: {
						'Content-Type': 'application/json',
					},
				})
				
				if (!response.ok) {
					throw new Error(`HTTP error! status: ${response.status}`)
				}
				
				const data = await response.json()
				
				// Update state with results
				this.searchResults = data.results || []
				this.pagination = {
					page: data.page || 1,
					pages: data.pages || 1,
					total: data.total || 0,
					limit: data.limit || 20,
					offset: data.offset || 0,
				}
				this.facets = data.facets || {}
				this.facetable = data.facetable || {}
				
				console.log('Search completed successfully:', {
					results: this.searchResults.length,
					pagination: this.pagination,
					facets: Object.keys(this.facets).length,
					facetable: Object.keys(this.facetable).length,
					ordering: this.ordering,
				})
				
			} catch (error) {
				console.error('Search failed:', error)
				this.error = error.message || 'An error occurred while searching'
				this.searchResults = []
				this.pagination = {
					page: 1,
					pages: 1,
					total: 0,
					limit: 20,
					offset: 0,
				}
				this.facets = {}
				this.facetable = {}
			} finally {
				this.loading = false
			}
		},
		
		/**
		 * Get publication details by ID
		 * 
		 * @param publicationId The ID of the publication to fetch
		 */
		async getPublication(publicationId: string) {
			try {
				const response = await fetch(`/index.php/apps/opencatalogi/api/search/${publicationId}`, {
					method: 'GET',
					headers: {
						'Content-Type': 'application/json',
					},
				})
				
				if (!response.ok) {
					throw new Error(`HTTP error! status: ${response.status}`)
				}
				
				const data = await response.json()
				console.log('Publication fetched:', data)
				return data
				
			} catch (error) {
				console.error('Failed to fetch publication:', error)
				throw error
			}
		},
		
		/**
		 * Get publications that this publication uses/references
		 * 
		 * @param publicationId The ID of the publication
		 * @param params Optional search parameters
		 */
		async getPublicationUses(publicationId: string, params: Record<string, any> = {}) {
			try {
				const searchParams = new URLSearchParams(params as Record<string, string>)
				const response = await fetch(`/index.php/apps/opencatalogi/api/search/${publicationId}/uses?${searchParams.toString()}`, {
					method: 'GET',
					headers: {
						'Content-Type': 'application/json',
					},
				})
				
				if (!response.ok) {
					throw new Error(`HTTP error! status: ${response.status}`)
				}
				
				const data = await response.json()
				console.log('Publication uses fetched:', data)
				return data
				
			} catch (error) {
				console.error('Failed to fetch publication uses:', error)
				throw error
			}
		},
		
		/**
		 * Get publications that use/reference this publication
		 * 
		 * @param publicationId The ID of the publication
		 * @param params Optional search parameters
		 */
		async getPublicationUsed(publicationId: string, params: Record<string, any> = {}) {
			try {
				const searchParams = new URLSearchParams(params as Record<string, string>)
				const response = await fetch(`/index.php/apps/opencatalogi/api/search/${publicationId}/used?${searchParams.toString()}`, {
					method: 'GET',
					headers: {
						'Content-Type': 'application/json',
					},
				})
				
				if (!response.ok) {
					throw new Error(`HTTP error! status: ${response.status}`)
				}
				
				const data = await response.json()
				console.log('Publication used by fetched:', data)
				return data
				
			} catch (error) {
				console.error('Failed to fetch publication used by:', error)
				throw error
			}
		},
		
		/**
		 * Clear search results and reset state
		 */
		clearSearch() {
			this.searchTerm = ''
			this.filters = {}
			this.ordering = {}
			this.searchResults = []
			this.pagination = {
				page: 1,
				pages: 1,
				total: 0,
				limit: 20,
				offset: 0,
			}
			this.facets = {}
			this.facetable = {}
			this.error = null
			this.selectedPublications = []
			console.log('Search cleared')
		},
	},
})
