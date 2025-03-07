<script setup>
import { navigationStore, organizationStore, searchStore } from '../../store/store.js'
</script>

<template>
	<NcAppContent>
		<template #list>
			<OrganizationList :search="searchStore.search" />
		</template>
		<template #default>
			<NcEmptyContent v-if="!organizationStore.organizationItem?.id || navigationStore.selected != 'organizations'"
				class="detailContainer"
				name="Geen organisatie"
				description="Nog geen organisatie geselecteerd">
				<template #icon>
					<OfficeBuildingOutline />
				</template>
				<template #action>
					<NcButton type="primary" @click="navigationStore.setModal('organizationForm')">
						Organisatie toevoegen
					</NcButton>
				</template>
			</NcEmptyContent>
			<OrganizationDetails v-if="organizationStore.organizationItem?.id && navigationStore.selected === 'organizations'" :organization-item="organizationStore.organizationItem" />
		</template>
	</NcAppContent>
</template>

<script>
import { NcAppContent, NcButton, NcEmptyContent } from '@nextcloud/vue'
import OfficeBuildingOutline from 'vue-material-design-icons/OfficeBuildingOutline.vue'
import OrganizationDetails from './OrganizationDetail.vue'
import OrganizationList from './OrganizationList.vue'

export default {
	name: 'OrganizationIndex',
	components: {
		NcAppContent,
		NcEmptyContent,
		OrganizationList,
		OrganizationDetails,
		NcButton,
		// Icons
		OfficeBuildingOutline,
	},
	data() {
		return {

		}
	},
}
</script>
