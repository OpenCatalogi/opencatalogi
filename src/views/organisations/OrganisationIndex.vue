<script setup>
import { navigationStore, organisationStore, searchStore } from '../../store/store.js'
</script>

<template>
	<NcAppContent>
		<template #list>
			<OrganisationList :search-query="searchStore.search" />
		</template>
		<template #default>
			<NcEmptyContent v-if="!organisationStore.organisationItem.id || navigationStore.selected != 'organisations'"
				class="detailContainer"
				name="Geen organisatie"
				description="Nog geen organisatie geselecteerd">
				<template #icon>
					<OfficeBuildingOutline />
				</template>
				<template #action>
					<NcButton type="primary" @click="navigationStore.setModal('organisationAdd')">
						Organisatie toevoegen
					</NcButton>
				</template>
			</NcEmptyContent>
			<OrganisationDetails v-if="organisationStore.organisationItem.id && navigationStore.selected === 'organisations'" :organisation-item="organisationStore.organisationItem" />
		</template>
	</NcAppContent>
</template>

<script>
import { NcAppContent, NcButton, NcEmptyContent } from '@nextcloud/vue'
import OfficeBuildingOutline from 'vue-material-design-icons/OfficeBuildingOutline.vue'
import OrganisationDetails from './OrganisationDetail.vue'
import OrganisationList from './OrganisationList.vue'

export default {
	name: 'OrganisationIndex',
	components: {
		NcAppContent,
		NcEmptyContent,
		OrganisationList,
		OrganisationDetails,
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
