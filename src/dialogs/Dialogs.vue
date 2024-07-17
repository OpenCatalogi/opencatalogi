<template>
	<!-- Placeholder div -->
	<div>
		<DeleteCatalogDialog />
		<DeleteAttachmenDialog />
		<DeleteListingDialog />
		<DeleteMetDataDialog />
		<DeleteMetaDataPropertiesDialog />
		<DeletePublicationDialog />
		<DeletePublicationDataDialog />
		<DeleteCatalogDialog />
	</div>
</template>

<script>
import DeleteCatalogDialog from './catalog/DeleteCatalogDialog.vue'
import DeleteAttachmenDialog from './attachment/DeleteAttachmentDialog.vue'
import DeleteListingDialog from './listing/DeleteListingDialog.vue'
import DeleteMetDataDialog from './metaData/DeleteMetaDataDialog.vue'
import DeleteMetaDataPropertiesDialog from './metaDataProperties/DeleteMetaDataPropertiesDialog.vue'
import DeletePublicationDialog from './publication/DeletePublicationDialog.vue'
import DeletePublicationDataDialog from './publicationData/DeletePublicationDataDialog.vue'

export default {
	name: 'Dialogs',
	components: {
		DeleteCatalogDialog,
		DeleteAttachmenDialog,
		DeleteListingDialog,
		DeleteMetDataDialog,
		DeleteMetaDataPropertiesDialog,
		DeletePublicationDialog,
		DeletePublicationDataDialog,
	},
}
</script>
