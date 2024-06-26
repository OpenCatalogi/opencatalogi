<template>
	<NcContent appName="opencatalog">
		<MainMenu selected="dashboard" :publication="publication" :metaData="metaData" :catalog="catalog"
			:externalCatalog="externalCatalog" />
		<NcAppContent>
			<template #default>
				<Dashboard />
			</template>
		</NcAppContent>
		<!-- <ZaakSidebar /> -->
		<AddPublicationModal @publication="updatePublicationModal" />
		<EditPublicationModal :publicationName="this.publication" @publication="updatePublicationModal" />
		<AddMetaDataModal @metaData="updateMetaDataModal" />
		<EditMetaDataModal :metaDataName="this.metaData" @metaData="updateMetaDataModal" />
		<AddCatalogModal @catalog="updateCatalogModal" />
		<EditCatalogModal :catalogName="this.catalog" @catalog="updateCatalogModal" />
		<AddExternalCatalogModal @externalCatalog="updateExternalCatalogModal" />
		<EditExternalCatalogModal :externalCatalogName="this.externalCatalog"
			@externalCatalog="updateExternalCatalogModal" />
	</NcContent>
</template>

<script>
import { NcAppContent, NcContent } from '@nextcloud/vue';
import MainMenu from './navigation/MainMenu.vue';
import AddPublicationModal from './modals/publicationModal/AddPublicationModal.vue';
import EditPublicationModal from './modals/publicationModal/EditPublicationModal.vue';
import AddMetaDataModal from './modals/metaDataModal/AddMetaDataModal.vue';
import EditMetaDataModal from './modals/metaDataModal/EditMetaDataModal.vue';
import AddCatalogModal from './modals/catalogModal/AddCatalogModal.vue';
import EditCatalogModal from './modals/catalogModal/EditCatalogModal.vue';
import AddExternalCatalogModal from './modals/externalCatalogModal/AddExternalCatalogModal.vue';
import EditExternalCatalogModal from './modals/externalCatalogModal/EditExternalCatalogModal.vue';
import Dashboard from './views/dashboard.vue';

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
		}
	},
	methods: {
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
	}
}
</script>
