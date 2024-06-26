<template>
    <NcModal v-if="isModalOpen.addExternalCatalogModal" ref="modalRef" @close="closeModal">
        <div class="modal__content">
            <h2>Add externalCatalog</h2>
            <div class="form-group">
                <NcTextField label="Naam" :value.sync="name" />
            </div>
            <div v-if="succesMessage" class="success">Succesfully added externalCatalog</div>

            <NcButton :disabled="!name" @click="addExternalCatalog" type="primary">
                Submit
            </NcButton>
        </div>
    </NcModal>
</template>

<script>
import { NcButton, NcModal, NcTextField, NcTextArea } from '@nextcloud/vue';
import { isModalOpen } from '../modalContext.js';

export default {
    name: "AddExternalCatalogModal",
    data() {
        return {
            name: '',
            succesMessage: false,
            isModalOpen,

        }
    },
    components: {
        NcModal,
        NcTextField,
        NcTextArea,
        NcButton
    },
    methods: {
        closeModal() {
            isModalOpen.addExternalCatalogModal = false
        },
        addExternalCatalog() {
            this.$emit('externalCatalog', this.name)
            this.succesMessage = true
            setTimeout(() => this.succesMessage = false, 2500);
            this.name = ''
        },
    }
}
</script>

<style>
.modal__content {
    margin: var(--zaa-margin-50);
    text-align: center;
}

.zaakDetailsContainer {
    margin-block-start: var(--zaa-margin-20);
    margin-inline-start: var(--zaa-margin-20);
    margin-inline-end: var(--zaa-margin-20);
}

.success {
    color: green;
}
</style>
