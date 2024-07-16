<template>
	<!-- Placeholder div -->
	<div>
		<DeleteCatalogDialog />
		<DeleteAttachmenDialog />
		<DeleteListingDialog />
		<DeleteMetDataDialog />
		<DeletePublicationDialog />
		<DeleteCatalogDialog />
	</div>
</template>

<script>
import DeleteCatalogDialog from './catalog/DeleteCatalogDialog.vue'
import DeleteAttachmenDialog from './attachment/DeleteAttachmentDialog.vue'
import DeleteListingDialog from './listing/DeleteListingDialog.vue'
import DeleteMetDataDialog from './metaData/DeleteMetaDataDialog.vue'
import DeletePublicationDialog from './publication/DeletePublicationDialog.vue'

export default {
	name: 'Dialogs',
	components: {
		DeleteCatalogDialog,
		DeleteAttachmenDialog,
		DeleteListingDialog,
		DeleteMetDataDialog,
		DeletePublicationDialog,
	},
}
</script>
