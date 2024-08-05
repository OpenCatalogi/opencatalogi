<script setup>
import { navigationStore, searchStore, organisationStore } from '../../store/store.js'
</script>

<template>
	<NcAppContent>
		<template #list>
			<OrganizationList :search="searchStore.search" />
		</template>
		<template #default>
			<NcEmptyContent v-if="!publicationStore.publicationItem.id || navigationStore.selected != 'publication'"
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
			<OrganizationDetails v-if="organisationStore.organisationItem.id && navigationStore.selected === 'publication'" :organisation-item="organisationStore.organisationItem" />
		</template>
	</NcAppContent>
</template>

<script>
import { NcAppContent, NcEmptyContent, NcButton } from '@nextcloud/vue'
import OrganizationList from './OrganizationList.vue'
import OrganizationDetails from './OrganizationDetail.vue'
import OfficeBuildingOutline from 'vue-material-design-icons/OfficeBuildingOutline.vue'

export default {
	name: 'OrganizationIndex',
	components: {
		NcAppContent,
		NcEmptyContent,
		PublicationList,
		PublicationDetails,
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
