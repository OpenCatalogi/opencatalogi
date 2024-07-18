<template>
	<!-- Placeholder div -->
	<div>
		<DeleteCatalogDialog />
		<DeleteAttachmenDialog />
		<CopyAttachmentDialog />
		<DeleteListingDialog />
		<DeleteMetDataDialog />
		<CopyMetaDataDialog />
		<DeleteMetaDataPropertiesDialog />
		<CopyMetaDataPropertiesDialog />
		<DeletePublicationDialog />
		<DeletePublicationDataDialog />
		<CopyPublicationDialog />
		<DeleteCatalogDialog />
	</div>
</template>

<script>
import DeleteCatalogDialog from './catalog/DeleteCatalogDialog.vue'
import DeleteAttachmenDialog from './attachment/DeleteAttachmentDialog.vue'
import CopyAttachmentDialog from './attachment/CopyAttachmentDialog.vue'
import DeleteListingDialog from './listing/DeleteListingDialog.vue'
import DeleteMetDataDialog from './metaData/DeleteMetaDataDialog.vue'
import CopyMetaDataDialog from './metaData/CopyMetaDataDialog.vue'
import DeleteMetaDataPropertiesDialog from './metaDataProperties/DeleteMetaDataPropertiesDialog.vue'
import CopyMetaDataPropertiesDialog from './metaDataProperties/CopyMetaDataPropertiesDialog.vue'
import DeletePublicationDialog from './publication/DeletePublicationDialog.vue'
import CopyPublicationDialog from './publication/CopyPublicationDialog.vue'
import DeletePublicationDataDialog from './publicationData/DeletePublicationDataDialog.vue'

export default {
	name: 'Dialogs',
	components: {
		DeleteCatalogDialog,
		DeleteAttachmenDialog,
		CopyAttachmentDialog,
		DeleteListingDialog,
		DeleteMetDataDialog,
		CopyMetaDataDialog,
		DeleteMetaDataPropertiesDialog,
		CopyMetaDataPropertiesDialog,
		DeletePublicationDialog,
		CopyPublicationDialog,
		DeletePublicationDataDialog,
	},
}
</script>
