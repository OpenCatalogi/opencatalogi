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
		<EditOrganisatioModal />
		<AddThemeModal />
		<EditThemeModal />
		<!-- <EditORganisationModal /> -->
	</div>
</template>

<script>

import AddAttachmentModal from './attachment/AddAttachmentModal.vue'
import EditAttachmentModal from './attachment/EditAttachmentModal.vue'
import AddMetaDataModal from './metaData/AddMetaDataModal.vue'
import EditMetaDataModal from './metaData/EditMetaDataModal.vue'
import AddPublicationModal from './publication/AddPublicationModal.vue'
import EditPublicationModal from './publication/EditPublicationModal.vue'

import AddMetaDataPropertyModal from './metaData/AddMetaDataPropertyModal.vue'
import EditMetaDataPropertyModal from './metaData/EditMetaDataPropertyModal.vue'

import AddCatalogModal from './catalog/AddCatalogModal.vue'
import EditCatalogModal from './catalog/EditCatalogModal.vue'
import AddListingModal from './directory/AddDirectoryModal.vue'
import EditListingModal from './directory/EditListingModal.vue'
import AddOrganisationModal from './organisation/AddOrganisationModal.vue'
import EditOrganisatioModal from './organisation/EditOrganisationModal.vue'
import AddPublicationDataModal from './publicationData/AddPublicationDataModal.vue'
import EditPublicationDataModal from './publicationData/EditPublicationDataModal.vue'
import AddThemeModal from './theme/AddThemeModal.vue'
import EditThemeModal from './theme/EditThemeModal.vue'

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
		EditOrganisatioModal,
		AddThemeModal,
		EditThemeModal,
		// EditOrganisationModal,
	},
}
</script>
