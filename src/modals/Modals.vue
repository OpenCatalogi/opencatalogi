<template>
	<!-- Placeholder -->
	<div>
		<AddPublicationModal />
		<EditPublicationModal />
		<DeletePublicationModal />
		<AddMetaDataModal />
		<EditMetaDataModal />
		<AddCatalogModal />
		<EditCatalogModal />
		<AddDirectoryModal />
		<EditDirectoryModal />
		<EditPublicationDataModal />
	</div>
</template>

<script>
import AddPublicationModal from './publication/AddPublicationModal.vue'
import EditPublicationModal from './publication/EditPublicationModal.vue'
import DeletePublicationModal from './publication/DeletePublicationModal.vue'
import AddMetaDataModal from './metaData/AddMetaDataModal.vue'
import EditMetaDataModal from './metaData/EditMetaDataModal.vue'
import AddCatalogModal from './catalog/AddCatalogModal.vue'
import EditCatalogModal from './catalog/EditCatalogModal.vue'
import AddDirectoryModal from './directory/AddDirectoryModal.vue'
import EditDirectoryModal from './directory/EditDirectoryModal.vue'
import EditPublicationDataModal from './publicationData/EditPublicationDataModal.vue'

export default {
	name: 'Modals',
	components: {
		AddPublicationModal,
		EditPublicationModal,
		DeletePublicationModal,
		AddMetaDataModal,
		EditMetaDataModal,
		AddCatalogModal,
		EditCatalogModal,
		AddDirectoryModal,
		EditDirectoryModal,
		EditPublicationDataModal,
	},
}
</script>
