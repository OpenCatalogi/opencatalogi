import { reactive } from "vue";

export const isModalOpen = reactive({
  publicatieToevoegenModal: false,
  metadataToevoegenModal: false,
  documentToevoegenModal: false,
  catalogusToevoegenModal: false,
  directoryBewerkenModal: false,
});
