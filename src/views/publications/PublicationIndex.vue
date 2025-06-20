<script setup>
import { navigationStore, objectStore } from '../../store/store.js'
</script>

<template>
	<NcAppContent>
		<template #list>
			<PublicationList />
		</template>
		<template #default>
			<NcEmptyContent v-if="showEmptyContent"
				class="detailContainer"
				name="Geen publicatie"
				description="Nog geen publicatie geselecteerd">
				<template #icon>
					<ListBoxOutline />
				</template>
				<template #action>
					<!-- <NcButton type="primary" @click="objectStore.clearActiveObject('publication'); navigationStore.setModal('publication')"> -->
					<NcButton type="primary" @click="objectStore.clearActiveObject('publication'); navigationStore.setModal('objectModal')">
						Publicatie toevoegen
					</NcButton>
				</template>
			</NcEmptyContent>
			<PublicationDetails v-if="!showEmptyContent" />
		</template>
	</NcAppContent>
</template>

<script>
import { computed } from 'vue'
import { storeToRefs } from 'pinia'
import { NcAppContent, NcEmptyContent, NcButton } from '@nextcloud/vue'
import PublicationList from './PublicationList.vue'
import PublicationDetails from './PublicationDetail.vue'
import ListBoxOutline from 'vue-material-design-icons/ListBoxOutline.vue'

// Make the stores reactive
const { selected } = storeToRefs(navigationStore)
const activePublication = computed(() => objectStore.getActiveObject('publication'))

const showEmptyContent = computed(() => {
	const hasActivePublication = activePublication.value
	const isPublicationSelected = selected.value === 'publication'
	return !hasActivePublication || !isPublicationSelected
})

export default {
	name: 'PublicationIndex',
	components: {
		NcAppContent,
		NcEmptyContent,
		ListBoxOutline,
		PublicationList,
		PublicationDetails,
		NcButton,
	},
}
</script>
