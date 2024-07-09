<template>
	<!-- Placeholder -->
	<div>
		<AddPublicationModal />
		<EditPublicationModal />
		<AddMetaDataModal />
		<EditMetaDataModal />
		<AddCatalogModal />
		<EditCatalogModal />
		<AddListingModal />
		<EditListingModal />
	</div>
</template>

<script>
import AddPublicationModal from './publication/AddPublicationModal.vue'
import EditPublicationModal from './publication/EditPublicationModal.vue'
import AddMetaDataModal from './metaData/AddMetaDataModal.vue'
import EditMetaDataModal from './metaData/EditMetaDataModal.vue'
import AddCatalogModal from './catalog/AddCatalogModal.vue'
import EditCatalogModal from './catalog/EditCatalogModal.vue'
import AddListingModal from './directory/AddListingModal.vue'
import EditListingModal from './directory/EditListingModal.vue'

export default {
	name: 'Modals',
	components: {
		AddPublicationModal,
		EditPublicationModal,
		AddMetaDataModal,
		EditMetaDataModal,
		AddCatalogModal,
		EditCatalogModal,
		AddListingModal,
		EditListingModal,
	},
}
</script>
