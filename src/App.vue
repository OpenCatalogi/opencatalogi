<template>
	<NcContent app-name="opencatalog">
		<MainMenu selected="dashboard"
			:publication="publication"
			:meta-data="metaData"
			:catalog="catalog"
			:external-catalog="externalCatalog"
			@activeMenuItem="updateMenuPublication" />
		<NcAppContent>
			<template #default>
				<Dashboard />
			</template>
		</NcAppContent>
		<!-- <ZaakSidebar /> -->
		<AddPublicationModal @publication="updatePublicationModal" />
		<EditPublicationModal :publication-name="publication" @publication="updatePublicationModal" />
		<AddMetaDataModal @metaData="updateMetaDataModal" />
		<EditMetaDataModal :meta-data-name="metaData" @metaData="updateMetaDataModal" />
		<AddCatalogModal @catalog="updateCatalogModal" />
		<EditCatalogModal :catalog-name="catalog" @catalog="updateCatalogModal" />
		<AddExternalCatalogModal @externalCatalog="updateExternalCatalogModal" />
		<EditExternalCatalogModal :external-catalog-name="externalCatalog"
			@externalCatalog="updateExternalCatalogModal" />
	</NcContent>
</template>

<script>
import { NcAppContent, NcContent } from '@nextcloud/vue'
import MainMenu from './navigation/MainMenu.vue'
import AddPublicationModal from './modals/publicationModal/AddPublicationModal.vue'
import EditPublicationModal from './modals/publicationModal/EditPublicationModal.vue'
import AddMetaDataModal from './modals/metaDataModal/AddMetaDataModal.vue'
import EditMetaDataModal from './modals/metaDataModal/EditMetaDataModal.vue'
import AddCatalogModal from './modals/catalogModal/AddCatalogModal.vue'
import EditCatalogModal from './modals/catalogModal/EditCatalogModal.vue'
import AddExternalCatalogModal from './modals/externalCatalogModal/AddExternalCatalogModal.vue'
import EditExternalCatalogModal from './modals/externalCatalogModal/EditExternalCatalogModal.vue'
import Dashboard from './views/dashboard.vue'

export default {
	name: 'App',
	components: {
		NcContent,
		NcAppContent,
		MainMenu,
		AddPublicationModal,
		EditPublicationModal,
		AddMetaDataModal,
		EditMetaDataModal,
		AddCatalogModal,
		EditCatalogModal,
		AddExternalCatalogModal,
		EditExternalCatalogModal,
		Dashboard,
	},
	data() {
		return {
			publication: '',
			metaData: '',
			catalog: '',
			externalCatalog: '',
			activeMenuItem: '',
		}
	},
	methods: {
		updateMenuPublication(selected) {
			this.activeMenuItem = selected
		},
		updatePublicationModal(variable) {
			this.publication = variable
		},
		updateMetaDataModal(variable) {
			this.metaData = variable
		},
		updateCatalogModal(variable) {
			this.catalog = variable
		},
		updateExternalCatalogModal(variable) {
			this.externalCatalog = variable
		},
	},
}
</script>
