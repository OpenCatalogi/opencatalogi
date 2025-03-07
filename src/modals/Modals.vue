<script setup>
import { navigationStore, publicationStore } from './../store/store.js'
</script>

<template>
	<!-- Placeholder Div -->
	<div>
		<AddAttachmentModal :drop-files="publicationStore.attachmentFile" />
		<EditAttachmentModal />
		<AddPublicationModal />
		<EditPublicationModal />
		<AddPublicationTypeModal />
		<EditPublicationTypeModal />
		<AddPublicationTypePropertyModal />
		<EditPublicationTypePropertyModal />
		<AddPublicationThemeModal />
		<AddCatalogModal />
		<EditCatalogModal />
		<AddCatalogiPublicationType />
		<AddDirectoryModal />
		<EditListingModal />
		<AddPublicationDataModal />
		<EditPublicationDataModal />
		<OrganizationFormModal v-if="navigationStore.modal === 'organizationForm'" />
		<AddThemeModal />
		<EditThemeModal />
		<PageForm v-if="navigationStore.modal === 'pageForm'" />
		<DeletePage v-if="navigationStore.modal === 'deletePage'" />
		<AddPageContentsModal v-if="navigationStore.modal === 'addPageContents'" />
		<!-- Menu -->
		<EditMenuModal v-if="navigationStore.modal === 'editMenu'" />
		<DeleteMenuModal />
		<EditMenuItemModal v-if="navigationStore.modal === 'editMenuItem'" />
		<DeleteMenuItemModal v-if="navigationStore.modal === 'deleteMenuItem'" />
	</div>
</template>

<script>
import AddAttachmentModal from './attachment/AddAttachmentModal.vue'
import EditAttachmentModal from './attachment/EditAttachmentModal.vue'
import AddPublicationTypeModal from './publicationType/AddPublicationTypeModal.vue'
import EditPublicationTypeModal from './publicationType/EditPublicationTypeModal.vue'
import AddPublicationModal from './publication/AddPublicationModal.vue'
import EditPublicationModal from './publication/EditPublicationModal.vue'

import AddPublicationTypePropertyModal from './publicationType/AddPublicationTypePropertyModal.vue'
import EditPublicationTypePropertyModal from './publicationType/EditPublicationTypePropertyModal.vue'
import AddPublicationThemeModal from './publicationTheme/AddPublicationThemeModal.vue'

import AddCatalogModal from './catalog/AddCatalogModal.vue'
import EditCatalogModal from './catalog/EditCatalogModal.vue'
import AddCatalogiPublicationType from './catalogiPublicationType/AddCatalogiPublicationType.vue'
import AddDirectoryModal from './directory/AddDirectoryModal.vue'
import EditListingModal from './directory/EditListingModal.vue'
import OrganizationFormModal from './organization/OrganizationFormModal.vue'
import AddPublicationDataModal from './publicationData/AddPublicationDataModal.vue'
import EditPublicationDataModal from './publicationData/EditPublicationDataModal.vue'
import AddThemeModal from './theme/AddThemeModal.vue'
import EditThemeModal from './theme/EditThemeModal.vue'
import PageForm from './page/PageForm.vue'
import DeletePage from './page/DeletePage.vue'
import AddPageContentsModal from './pageContents/AddPageContents.vue'
// menu
import EditMenuModal from './menu/EditMenuModal.vue'
import DeleteMenuModal from './menu/DeleteMenuModal.vue'
import EditMenuItemModal from './menuItem/EditMenuItemModal.vue'
import DeleteMenuItemModal from './menuItem/DeleteMenuItemModal.vue'

/**
 * Component that contains all modals used in the application
 */
export default {
	name: 'Modals',
	components: {
		AddAttachmentModal,
		EditAttachmentModal,
		AddPublicationModal,
		EditPublicationModal,
		AddPublicationTypeModal,
		EditPublicationTypeModal,
		AddPublicationTypePropertyModal,
		EditPublicationTypePropertyModal,
		AddPublicationThemeModal,
		AddCatalogModal,
		EditCatalogModal,
		AddCatalogiPublicationType,
		AddDirectoryModal,
		EditListingModal,
		AddPublicationDataModal,
		EditPublicationDataModal,
		OrganizationFormModal,
		AddThemeModal,
		EditThemeModal,
		PageForm,
		DeletePage,
		AddPageContentsModal,
		// menu
		EditMenuModal,
		DeleteMenuModal,
		EditMenuItemModal,
		DeleteMenuItemModal,
	},
}
</script>
