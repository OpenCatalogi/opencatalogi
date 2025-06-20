<script setup>
import { navigationStore, objectStore } from './../store/store.js'
</script>

<template>
	<!-- Placeholder Div -->
	<div>
		<!--Generic-->
		<CatalogModal />
		<OrganizationModal v-if="navigationStore.modal === 'organization'" />
		<ThemeModal v-if="navigationStore.modal === 'theme'" />
		<AddPublicationThemeModal v-if="navigationStore.modal === 'addPublicationTheme'" />
		<PageModal v-if="navigationStore.modal === 'page'" />
		<GlossaryModal />
		<MenuModal v-if="navigationStore.modal === 'menu'" />
		<!--View Modals-->
		<ViewDirectoryModal />
		<ViewGlossaryModal />
		<ViewMenuModal />
		<ViewPageModal />
		<ViewThemeModal />
		<!--Specific-->
		<AddAttachmentModal :drop-files="objectStore.getActiveObject('attachment')" />
		<EditAttachmentModal />
		<AddDirectoryModal />
		<EditListingModal v-if="navigationStore.modal === 'editListing'" />
		<PageContentForm v-if="navigationStore.modal === 'pageContentForm'" />
		<!-- Menu -->
		<MenuItemForm v-if="navigationStore.modal === 'menuItemForm'" />
		<DeleteMenuItemModal v-if="navigationStore.modal === 'deleteMenuItem'" />
		<UploadFilesModal v-if="navigationStore.modal === 'uploadFiles'" />
		<ObjectModal v-if="navigationStore.modal === 'objectModal'" />
	</div>
</template>

<script>

// Generic
import CatalogModal from './catalog/CatalogModal.vue'
import OrganizationModal from './organization/OrganizationModal.vue'
import ThemeModal from './theme/ThemeModal.vue'
import PageModal from './page/PageModal.vue'
import GlossaryModal from './glossary/GlossaryModal.vue'
import UploadFilesModal from './generic/UploadFiles.vue'
// View Modals
import ViewDirectoryModal from './directory/ViewDirectoryModal.vue'
import ViewGlossaryModal from './glossary/ViewGlossaryModal.vue'
import ViewMenuModal from './menu/ViewMenuModal.vue'
import ViewPageModal from './page/ViewPageModal.vue'
import ViewThemeModal from './theme/ViewThemeModal.vue'
// Specific
import AddAttachmentModal from './attachment/AddAttachmentModal.vue'
import EditAttachmentModal from './attachment/EditAttachmentModal.vue'

import AddDirectoryModal from './directory/AddDirectoryModal.vue'
import EditListingModal from './directory/EditListingModal.vue'
import PageContentForm from './pageContents/PageContentForm.vue'
// menu
import MenuModal from './menu/MenuModal.vue'
import MenuItemForm from './menuItem/MenuItemForm.vue'
import DeleteMenuItemModal from './menuItem/DeleteMenuItemModal.vue'
import ObjectModal from './object/ObjectModal.vue'
// Publication
import AddPublicationThemeModal from './theme/AddPublicationThemeModal.vue'
/**
 * Component that contains all modals used in the application
 */
export default {
	name: 'Modals',
	components: {
		AddAttachmentModal,
		EditAttachmentModal,
		CatalogModal,
		AddDirectoryModal,
		EditListingModal,
		OrganizationModal,
		ThemeModal,
		PageModal,
		MenuModal,
		GlossaryModal,
		// View Modals
		ViewDirectoryModal,
		ViewGlossaryModal,
		ViewMenuModal,
		ViewPageModal,
		ViewThemeModal,
		PageContentForm,
		// menu
		MenuItemForm,
		DeleteMenuItemModal,
		UploadFilesModal,
		ObjectModal,
		AddPublicationThemeModal,
	},
}
</script>
