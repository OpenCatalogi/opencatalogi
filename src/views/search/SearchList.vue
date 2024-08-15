<script setup>
import { searchStore } from '../../store/store.js'
</script>

<template>
	<ul>
		<NcListItem
			v-for="(result, i) in searchStore.searchResults.results"
			:key="`${result}${i}`"
			:name="result.title || 'Geen titel'"
			:subname="result.summary || 'Geen samenvatting'"
			:details="result.metaData?.title || 'Geen metadata'"
			:bold="false"
			:force-display-actions="true"
			:counter-number="result.attachment_count || 0">
			<template #icon>
				<ListBoxOutline :size="44" />
			</template>
			<template #actions>
				<NcActionButton v-if="result.portal" @click="goToLink(result.portal)">
					<template #icon>
						<OpenInNew :size="20" />
					</template>
					Open portal page
				</NcActionButton>
			</template>
		</NcListItem>
	</ul>
</template>
<script>
import { NcListItem, NcActionButton } from '@nextcloud/vue'

// Icons
import ListBoxOutline from 'vue-material-design-icons/ListBoxOutline.vue'
import OpenInNew from 'vue-material-design-icons/OpenInNew.vue'

export default {
	name: 'SearchList',
	components: {
		NcListItem,
		NcActionButton,
		// Icons
		ListBoxOutline,
		OpenInNew,
	},
	methods: {
		goToLink(link) {
			//
		},
	},
}
</script>
