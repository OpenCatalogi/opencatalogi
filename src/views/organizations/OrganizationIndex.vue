<script setup>
import { navigationStore, objectStore } from '../../store/store.js'
</script>

<template>
	<NcAppContent>
		<template #list>
			<OrganizationList />
		</template>
		<template #default>
			<NcEmptyContent v-if="showEmptyContent"
				class="detailContainer"
				name="Geen organisatie"
				description="Nog geen organisatie geselecteerd">
				<template #icon>
					<OfficeBuildingOutline />
				</template>
				<template #action>
					<NcButton type="primary" @click="navigationStore.setModal('organization')">
						Organisatie toevoegen
					</NcButton>
				</template>
			</NcEmptyContent>
			<OrganizationDetails v-if="!showEmptyContent" />
		</template>
	</NcAppContent>
</template>

<script>
import { computed } from 'vue'
import { storeToRefs } from 'pinia'
import { NcAppContent, NcEmptyContent, NcButton } from '@nextcloud/vue'
import OrganizationList from './OrganizationList.vue'
import OrganizationDetails from './OrganizationDetail.vue'
import OfficeBuildingOutline from 'vue-material-design-icons/OfficeBuildingOutline.vue'

// Make the stores reactive
const { selected } = storeToRefs(navigationStore)
const activeOrganization = computed(() => objectStore.getActiveObject('organization'))

const showEmptyContent = computed(() => {
	const hasActiveOrganization = activeOrganization.value
	const isOrganizationSelected = selected.value === 'organizations'
	return !hasActiveOrganization || !isOrganizationSelected
})

export default {
	name: 'OrganizationIndex',
	components: {
		NcAppContent,
		NcEmptyContent,
		NcButton,
		OrganizationList,
		OrganizationDetails,
		OfficeBuildingOutline,
	},
}
</script>
