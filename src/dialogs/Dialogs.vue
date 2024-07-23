<template>
	<!-- Placeholder div -->
	<div>
		<DeleteCatalogDialog />
		<DeleteAttachmentDialog />
		<PublishAttachmentDialog />
		<DepublishAttachmentDialog />
		<CopyAttachmentDialog />
		<DeleteListingDialog />
		<DeleteMetDataDialog />
		<CopyMetaDataDialog />
		<DeleteMetaDataPropertiesDialog />
		<CopyMetaDataPropertiesDialog />
		<DeletePublicationDialog />
		<DeletePublicationDataDialog />
		<CopyPublicationDialog />
		<PublishPublicationDialog />
		<DepublishPublicationDialog />
		<DeleteCatalogDialog />
		<DepublishPublicationDialog />
		<ArchivePublicationDialog />
		<ViewLogDialog />
	</div>
</template>

<script>
import DeleteCatalogDialog from './catalog/DeleteCatalogDialog.vue'
import DeleteAttachmentDialog from './attachment/DeleteAttachmentDialog.vue'
import PublishAttachmentDialog from './attachment/PublishAttachmentDialog.vue'
import DepublishAttachmentDialog from './attachment/DepublishAttachmentDialog.vue'
import CopyAttachmentDialog from './attachment/CopyAttachmentDialog.vue'
import DeleteListingDialog from './listing/DeleteListingDialog.vue'
import DeleteMetDataDialog from './metaData/DeleteMetaDataDialog.vue'
import CopyMetaDataDialog from './metaData/CopyMetaDataDialog.vue'
import DeleteMetaDataPropertiesDialog from './metaDataProperties/DeleteMetaDataPropertiesDialog.vue'
import CopyMetaDataPropertiesDialog from './metaDataProperties/CopyMetaDataPropertiesDialog.vue'
import DeletePublicationDialog from './publication/DeletePublicationDialog.vue'
import CopyPublicationDialog from './publication/CopyPublicationDialog.vue'
import DeletePublicationDataDialog from './publicationData/DeletePublicationDataDialog.vue'
import PublishPublicationDialog from './publication/PublishPublicationDialog.vue'
import DepublishPublicationDialog from './publication/DepublishPublicationDialog.vue'
import ArchivePublicationDialog from './publication/ArchivePublicationDialog.vue'
import ViewLogDialog from './logs/ViewLogDialog.vue'

export default {
	name: 'Dialogs',
	components: {
		DeleteCatalogDialog,
		DeleteAttachmentDialog,
		PublishAttachmentDialog,
		DepublishAttachmentDialog,
		CopyAttachmentDialog,
		DeleteListingDialog,
		DeleteMetDataDialog,
		CopyMetaDataDialog,
		DeleteMetaDataPropertiesDialog,
		CopyMetaDataPropertiesDialog,
		DeletePublicationDialog,
		CopyPublicationDialog,
		DeletePublicationDataDialog,
		PublishPublicationDialog,
		DepublishPublicationDialog,
		ArchivePublicationDialog,
		ViewLogDialog,
	},
}
</script>
