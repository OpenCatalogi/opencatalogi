<template>
	<!-- Placeholder Div -->
	<div>
		<AddPublicationModal />
		<EditPublicationModal />
		<AddMetaDataModal />
		<EditMetaDataModal />
		<AddMetadataDataModal />
		<EditMetadataDataModal />
		<AddCatalogModal />
		<EditCatalogModal />
		<AddListingModal />
		<EditListingModal />
		<EditPublicationDataModal />
		<AddPublicationDataModal />
	</div>
</template>

<script>
import AddPublicationModal from './publication/AddPublicationModal.vue'
import EditPublicationModal from './publication/EditPublicationModal.vue'
import AddMetaDataModal from './metaData/AddMetaDataModal.vue'
import EditMetaDataModal from './metaData/EditMetaDataModal.vue'
import AddMetadataDataModal from './metadataData/AddMetadataDataModal.vue'
import EditMetadataDataModal from './metadataData/EditMetadataDataModal.vue'
import AddCatalogModal from './catalog/AddCatalogModal.vue'
import EditCatalogModal from './catalog/EditCatalogModal.vue'
import AddListingModal from './directory/AddListingModal.vue'
import EditListingModal from './directory/EditListingModal.vue'
import EditPublicationDataModal from './publicationData/EditPublicationDataModal.vue'
import AddPublicationDataModal from './publicationData/AddPublicationDataModal.vue'

export default {
	name: 'Modals',
	components: {
		AddPublicationModal,
		EditPublicationModal,
		AddMetaDataModal,
		EditMetaDataModal,
		AddMetadataDataModal,
		EditMetadataDataModal,
		AddCatalogModal,
		EditCatalogModal,
		AddListingModal,
		EditListingModal,
		EditPublicationDataModal,
		AddPublicationDataModal,
	},
}
</script>
