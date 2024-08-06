<template>
	<!-- Placeholder Div -->
	<div>
		<AddAttachmentModal />
		<EditAttachmentModal />
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
		<AddPublicationDataModal />
		<EditPublicationDataModal />
		<AddOrganisationModal />
		<!-- <EditORganisationModal /> -->
	</div>
</template>

<script>

import AddAttachmentModal from './attachment/AddAttachmentModal.vue'
import EditAttachmentModal from './attachment/EditAttachmentModal.vue'
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
import AddOrganisationModal from './organisation/AddOrganisationModal.vue'

export default {
	name: 'Modals',
	components: {
		AddAttachmentModal,
		EditAttachmentModal,
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
		AddPublicationDataModal,
		EditPublicationDataModal,
		AddOrganisationModal,
		// EditOrganisationModal,
	},
}
</script>
