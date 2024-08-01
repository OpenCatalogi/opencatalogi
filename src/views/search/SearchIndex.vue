<script setup>
import { searchStore } from '../../store/store.js'
</script>

<template>
	<NcAppContent>
		<div class="dashboardContainer">
			<h2>Resultaten</h2>
		</div>
		<ul>
			<NcListItem
				v-for="(result, i) in searchStore.searchResults.results"
				:key="`${result}${i}`"
				:name="result.title"
				:subname="result.summary"
				:details="result.metaData.title"
				:bold="false"
				:force-display-actions="true"
				:counter-number="result.attachment_count">
				<template #icon>
					<ListBoxOutline :size="44" />
				</template>
				<template #actions>
					<NcActionButton @click="goToLink(result.portal)">
						<template #icon>
							<LinkIcon :size="20" />
						</template>
						Open portal page
					</NcActionButton>
				</template>
			</NcListItem>
		</ul>
	</NcAppContent>
</template>

<script>

import { NcAppContent, NcListItem, NcActionButton } from '@nextcloud/vue'

import ListBoxOutline from 'vue-material-design-icons/ListBoxOutline.vue'
import LinkIcon from 'vue-material-design-icons/Link.vue'

export default {
	name: 'SearchIndex',
	components: {
		NcAppContent,
		NcListItem,
		NcActionButton,
		// Icons
		ListBoxOutline,
		LinkIcon,
	},
	props: {
		search: {
			type: String,
			required: true,
		},
	},
	data() {
		return {

		}
	},
	watch: {
		search: {
			handler(search) {
				searchStore.getSearchResults()
			},
		},
	},
	mounted() {
		searchStore.getSearchResults()
	},
	methods: {
		goToLink(link) {
			//
		},
	},
}
</script>
<style>
.dashboardContainer {
    margin-inline-start: 65px;
    margin-block-start: 20px
}
</style>
