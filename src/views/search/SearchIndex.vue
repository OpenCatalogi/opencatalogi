<script setup>
import { searchStore } from '../../store/store.js'
</script>

<template>
	<NcAppContent>
		<h2 class="pageHeader">
			Resultaten
		</h2>
		<NcNoteCard v-if="!searchStore.searchResults?.results?.length > 0 || !searchStore.searchResults" type="info">
			<p>Er zijn op dit moment geen publicaties die aan uw zoekopdracht voldoen</p>
		</NcNoteCard>
		<NcLoadingIcon v-if="!searchStore.searchResults"
			:size="64"
			class="loadingIcon"
			appearance="dark"
			name="Publicaties aan het laden" />
		<SearchList v-if="searchStore.searchResults?.results?.length > 0" />
	</NcAppContent>
</template>

<script>
import { NcAppContent, NcNoteCard, NcLoadingIcon } from '@nextcloud/vue'
import SearchList from './SearchList.vue'

export default {
	name: 'SearchIndex',
	components: {
		NcAppContent,
		NcNoteCard,
		NcLoadingIcon,
		SearchList,
	},
	data() {
		return {}
	},
	mounted() {
		searchStore.getSearchResults()
	},
}
</script>
