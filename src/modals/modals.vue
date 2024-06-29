<template>
	<!-- Placehodel div for all of hte modals-->
	<div>		
		<AddPublicationModal @publication="updatePublicationModal" />
		<EditPublicationModal :publication-name="publication" @publication="updatePublicationModal" />
		<AddMetaDataModal @metaData="updateMetaDataModal" />
		<EditMetaDataModal :meta-data-name="metaData" @metaData="updateMetaDataModal" />
		<AddCatalogModal @catalog="updateCatalogModal" />
		<EditCatalogModal :catalog-name="catalog" @catalog="updateCatalogModal" />
		<AddExternalCatalogModal @externalCatalog="updateExternalCatalogModal" />
		<EditExternalCatalogModal :external-catalog-name="externalCatalog" @externalCatalog="updateExternalCatalogModal" />
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
	},
	props: {
		modal: {
			type: string,
			required: true,
		},
		item: {
			type: string,
			required: true,
		},
		selected: {
			type: string,
			required: true,
		}
	},
	methods: {
		setModal(modal) {
			this.modal = modal
			this.$emit('modal', modal)
		},			
		setSelected(selected) {
			this.selected = selected
			this.$emit('selected', selected)
		},
		setItem(item) {
			this.item = item
			this.$emit('item', item)
		}
	},
}
</script>


