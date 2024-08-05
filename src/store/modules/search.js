/* eslint-disable no-console */
import { defineStore } from 'pinia'

export const useSearchStore = defineStore(
    'search', {
        state: () => ({
            search: '',
            searchResults: '',
        }),
    actions: {
        setSearch(search) {
            this.search = search
            console.log('Active search set to ' + search)
        },
        setSearchResults(searchResults) {
            this.searchResults = searchResults
            console.log('Active search set to ' + searchResults)
        },
        getSearchResults() {
            fetch(
                '/index.php/apps/opencatalogi/api/search?_search=' + this.search,
                {
                    method: 'GET',
                },
            )
                .then(
                    (response) => {
                    response.json().then(
                            (data) => {
                            this.searchResults = data
                            }
                        )
                    }
                )
                .catch(
                    (err) => {
                    console.error(err)
                    }
                )
        },
        clearSearch() {
            this.search = ''
        },
        },
    }
)
