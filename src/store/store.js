/* eslint-disable no-console */
// The store script handles app wide variables (or state), for the use of these variables and there governing concepts read the design.md
import { reactive } from 'vue'
import uiState from './modules/uiState.js'
import search from './modules/search.js'
import catalogi from './modules/catalogi.js'
import directory from './modules/directory.js'
import metadata from './modules/metadata.js'
import publication from './modules/publication.js'

export const store = reactive({
	// generic
	...uiState,
	...search,
	// feature-specific
	...catalogi,
	...directory,
	...metadata,
	...publication,
})
