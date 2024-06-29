<template>
	<!-- Placeholder -->
	<div>		
		<AddPublicationModal  />
		<EditPublicationModal  />
		<AddMetaDataModal />
		<EditMetaDataModal  />
		<AddCatalogModal />
		<EditCatalogModal />
		<AddExternalCatalogModal />
		<EditExternalCatalogModal />
	</div>
</template>

<script>
import AddPublicationModal from './modals/publicationModal/AddPublicationModal.vue'
import EditPublicationModal from './modals/publicationModal/EditPublicationModal.vue'
import AddMetaDataModal from './modals/metaDataModal/AddMetaDataModal.vue'
import EditMetaDataModal from './modals/metaDataModal/EditMetaDataModal.vue'
import AddCatalogModal from './modals/catalogModal/AddCatalogModal.vue'
import EditCatalogModal from './modals/catalogModal/EditCatalogModal.vue'
import AddExternalCatalogModal from './modals/externalCatalogModal/AddExternalCatalogModal.vue'
import EditExternalCatalogModal from './modals/externalCatalogModal/EditExternalCatalogModal.vue'


export default {
	name: 'Modals',
	components: {
		AddPublicationModal,
		EditPublicationModal,
		AddMetaDataModal,
		EditMetaDataModal,
		AddCatalogModal,
		EditCatalogModal,
		AddExternalCatalogModal,
		EditExternalCatalogModal,
	}
}
</script>


