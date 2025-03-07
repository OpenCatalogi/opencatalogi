<script setup>
import { navigationStore } from '../store/store.js'
</script>

<template>
	<!-- Placeholder div -->
	<div>
		<DeleteCatalogDialog />
		<DeleteAttachmentDialog v-if="navigationStore.dialog === 'deleteAttachment'" />
		<PublishAttachmentDialog />
		<DepublishAttachmentDialog />
		<CopyAttachmentDialog />
		<DeleteListingDialog />
		<DeletePublicationTypeDialog />
		<CopyPublicationTypeDialog />
		<DeletePublicationTypePropertiesDialog />
		<CopyPublicationTypePropertiesDialog />
		<DownloadPublicationDialog />
		<DeletePublicationDialog />
		<DeletePublicationDataDialog />
		<CopyPublicationDialog />
		<DeletePublicationThemeDialog v-if="navigationStore.dialog === 'deletePublicationThemeDialog'" />
		<PublishPublicationDialog />
		<DepublishPublicationDialog />
		<DeleteCatalogDialog />
		<DeleteCatalogiPublicationType />
		<ArchivePublicationDialog />
		<ViewLogDialog />
		<DeleteOrganizationDialog />
		<CopyOrganizationDialog />
		<DeleteThemeDialog />
		<CopyThemeDialog />
		<CopyMenuDialog v-if="navigationStore.dialog === 'copyMenu'" />
	</div>
</template>

<script>
import CopyAttachmentDialog from './attachment/CopyAttachmentDialog.vue'
import DeleteAttachmentDialog from './attachment/DeleteAttachmentDialog.vue'
import DepublishAttachmentDialog from './attachment/DepublishAttachmentDialog.vue'
import PublishAttachmentDialog from './attachment/PublishAttachmentDialog.vue'
import DeleteCatalogDialog from './catalog/DeleteCatalogDialog.vue'
import DeleteCatalogiPublicationType from './catalogiPublicationType/DeleteCatalogiPublicationType.vue'
import DeleteListingDialog from './listing/DeleteListingDialog.vue'
import ViewLogDialog from './logs/ViewLogDialog.vue'
import CopyPublicationTypeDialog from './publicationType/CopyPublicationTypeDialog.vue'
import DeletePublicationTypeDialog from './publicationType/DeletePublicationTypeDialog.vue'
import CopyPublicationTypePropertiesDialog from './publicationTypeProperties/CopyPublicationTypePropertiesDialog.vue'
import DeletePublicationTypePropertiesDialog from './publicationTypeProperties/DeletePublicationTypePropertiesDialog.vue'
import CopyOrganizationDialog from './organization/CopyOrganizationDialog.vue'
import DeleteOrganizationDialog from './organization/DeleteOrganizationDialog.vue'
import ArchivePublicationDialog from './publication/ArchivePublicationDialog.vue'
import CopyPublicationDialog from './publication/CopyPublicationDialog.vue'
import DeletePublicationDialog from './publication/DeletePublicationDialog.vue'
import DepublishPublicationDialog from './publication/DepublishPublicationDialog.vue'
import PublishPublicationDialog from './publication/PublishPublicationDialog.vue'
import DeletePublicationDataDialog from './publicationData/DeletePublicationDataDialog.vue'
import DeletePublicationThemeDialog from './publicationTheme/DeletePublicationThemeDialog.vue'
import CopyThemeDialog from './theme/CopyThemeDialog.vue'
import DeleteThemeDialog from './theme/DeleteThemeDialog.vue'
import DownloadPublicationDialog from './publication/DownloadPublicationDialog.vue'
import CopyMenuDialog from './menu/CopyMenuDialog.vue'

export default {
	name: 'Dialogs',
	components: {
		DeleteCatalogDialog,
		DeleteCatalogiPublicationType,
		DeleteAttachmentDialog,
		PublishAttachmentDialog,
		DepublishAttachmentDialog,
		CopyAttachmentDialog,
		DeleteListingDialog,
		DeletePublicationTypeDialog,
		CopyPublicationTypeDialog,
		DeletePublicationTypePropertiesDialog,
		CopyPublicationTypePropertiesDialog,
		DeletePublicationDialog,
		CopyPublicationDialog,
		DeletePublicationDataDialog,
		DeletePublicationThemeDialog,
		PublishPublicationDialog,
		DepublishPublicationDialog,
		ArchivePublicationDialog,
		ViewLogDialog,
		DeleteOrganizationDialog,
		CopyOrganizationDialog,
		DeleteThemeDialog,
		CopyThemeDialog,
		DownloadPublicationDialog,
		CopyMenuDialog,
	},
}
</script>
