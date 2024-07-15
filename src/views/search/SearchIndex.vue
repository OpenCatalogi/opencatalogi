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
				:name="'Name of the element with normal counter'"
				:bold="false"
				:force-display-actions="true"
				:details="'1h'"
				:counter-number="44">
				<template #icon>
					<LinkIcon disable-menu :size="44" />
				</template>
				<template #subname>
					In this slot you can put both text and other components such as icons
				</template>
				<template #actions>
					<NcActionButton>
						<template #icon>
							<LinkIcon disable-menu :size="20" />
						</template>
						Button one
					</NcActionButton>
					<NcActionButton>
						<template #icon>
							<LinkIcon disable-menu :size="20" />
						</template>
						Button two
					</NcActionButton>
					<NcActionButton>
						<template #icon>
							<LinkIcon disable-menu :size="20" />
						</template>
						Button three
					</NcActionButton>
				</template>
			</NcListItem>
		</ul>
	</NcAppContent>
</template>

<script>

import { NcAppContent, NcListItem, NcActions, NcActionButton } from '@nextcloud/vue'

import CheckboxBlankCircle from 'vue-material-design-icons/CheckboxBlankCircle.vue'
import LinkIcon from 'vue-material-design-icons/Link.vue'

export default {
	name: 'SearchIndex',
	components: {
		NcAppContent,
		NcListItem,
		NcActionButton,
		// Icons
		CheckboxBlankCircle,
		LinkIcon,
	},
}
</script>
<style>
.dashboardContainer {
    margin-inline-start: 75px;
    margin-block-start: 20px
}
</style>
