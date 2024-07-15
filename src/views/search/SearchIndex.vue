<script setup>
import { store } from '../../store.js'
</script>

<template>
	<NcAppContent>
		<div class="dashboardContainer">
			<h2>Resultaten</h2>
		</div>
		<ul>
			<NcListItem
				:name="'Name'"
				:bold="false"
				:force-display-actions="true"
				:details="'metadata'"
				:counter-number="44">
				<template #icon>
					<ListBoxOutline :size="44" />
				</template>
				<template #subname>
					Summery
				</template>
				<template #actions>
					<NcActionButton>
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
}
</script>
<style>
.dashboardContainer {
    margin-inline-start: 65px;
    margin-block-start: 20px
}
</style>
