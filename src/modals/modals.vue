<template>
	<!-- Placeholder -->
	<div>
		<AddPublicationModal />
		<EditPublicationModal />
		<AddMetaDataModal />
		<EditMetaDataModal />
		<AddCatalogModal />
		<EditCatalogModal />
		<AddExternalCatalogModal />
		<EditExternalCatalogModal />
	</div>
</template>

<script>
import AddPublicationModal from './publication/AddPublicationModal.vue'
import EditPublicationModal from './publication/Edit.vue'
import AddMetaDataModal from './metaData/Add.vue'
import EditMetaDataModal from './metaData/Edit.vue'
import AddCatalogModal from './catalog/Add.vue'
import EditCatalogModal from './catalog/Edit.vue'
import AddExternalCatalogModal from './directory/Add.vue'
import EditExternalCatalogModal from './directory/Edit.vue'

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
	},
}
</script>
