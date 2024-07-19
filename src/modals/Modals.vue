<template>
	<!-- Placeholder Div -->
	<div>
		<AddAttachmentModal />
		<EditAttachmentnModal />
		<AddPublicationModal />
		<EditPublicationModal />
		<AddMetaDataModal />
		<EditMetaDataModal />
		<AddMetaDataPropertyModal />
		<EditMetaDataPropertyModal />
		<AddCatalogModal />
		<EditCatalogModal />
		<AddListingModal />
		<EditListingModal />
		<EditPublicationDataModal />
		<AddPublicationDataModal />
	</div>
</template>

<script>

import AddAttachmentModal from './attachment/AddAttachmentModal.vue'
import EditAttachmentnModal from './attachment/EditAttachmentModal.vue'
import AddPublicationModal from './publication/AddPublicationModal.vue'
import EditPublicationModal from './publication/EditPublicationModal.vue'
import AddMetaDataModal from './metaData/AddMetaDataModal.vue'
import EditMetaDataModal from './metaData/EditMetaDataModal.vue'

import AddMetaDataPropertyModal from './metaData/AddMetaDataPropertyModal.vue'
import EditMetaDataPropertyModal from './metaData/EditMetaDataPropertyModal.vue'

import AddCatalogModal from './catalog/AddCatalogModal.vue'
import EditCatalogModal from './catalog/EditCatalogModal.vue'
import AddListingModal from './directory/AddListingModal.vue'
import EditListingModal from './directory/EditListingModal.vue'
import EditPublicationDataModal from './publicationData/EditPublicationDataModal.vue'
import AddPublicationDataModal from './publicationData/AddPublicationDataModal.vue'

export default {
	name: 'Modals',
	components: {
		AddAttachmentModal,
		EditAttachmentnModal,
		AddPublicationModal,
		EditPublicationModal,
		AddMetaDataModal,
		EditMetaDataModal,
		AddMetaDataPropertyModal,
		EditMetaDataPropertyModal,
		AddCatalogModal,
		EditCatalogModal,
		AddListingModal,
		EditListingModal,
		EditPublicationDataModal,
		AddPublicationDataModal,
	},
}
</script>
