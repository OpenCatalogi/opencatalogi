import { reactive } from "vue";

export const isModalOpen = reactive({
  addPublicationModal: false,
  editPublicationModal: false,
  addMetaDataModal: false,
  editMetaDataModal: false,
  addCatalogModal: false,
  editCatalogModal: false,
  addExternalCatalogModal: false,
  editExternalCatalogModal: false,
  addDocumentModal: false,
  editDirectoryModal: false,
});
