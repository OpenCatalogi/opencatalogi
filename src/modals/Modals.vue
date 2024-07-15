<template>
	<!-- Placeholder Div -->
	<div>
		<AddPublicationModal />
		<EditPublicationModal />
		<AddMetaDataModal />
		<EditMetaDataModal />
		<EditMetadataDataModal />
		<AddCatalogModal />
		<EditCatalogModal />
		<AddListingModal />
		<EditListingModal />
		<EditPublicationDataModal />
	</div>
</template>

<script>
import AddPublicationModal from './publication/AddPublicationModal.vue'
import EditPublicationModal from './publication/EditPublicationModal.vue'
import AddMetaDataModal from './metaData/AddMetaDataModal.vue'
import EditMetaDataModal from './metaData/EditMetaDataModal.vue'
import EditMetadataDataModal from './metadataData/EditMetadataDataModal.vue'
import AddCatalogModal from './catalog/AddCatalogModal.vue'
import EditCatalogModal from './catalog/EditCatalogModal.vue'
import AddListingModal from './directory/AddListingModal.vue'
import EditListingModal from './directory/EditListingModal.vue'
import EditPublicationDataModal from './publicationData/EditPublicationDataModal.vue'

export default {
	name: 'Modals',
	components: {
		AddPublicationModal,
		EditPublicationModal,
		AddMetaDataModal,
		EditMetaDataModal,
		EditMetadataDataModal,
		AddCatalogModal,
		EditCatalogModal,
		AddListingModal,
		EditListingModal,
		EditPublicationDataModal,
	},
}
</script>
