<template>
    <NcModal v-if="isModalOpen.documentToevoegenModal" ref="modalRef" @close="closeModal">
        <div class="modal__content">
            <h2>Zaak aanmaken</h2>
            <div class="form-group">
                <NcTextField label="Identificatie" :value.sync="identificatie" />
            </div>
            <div class="form-group">
                <NcTextField label="Omschrijving" :value.sync="omschrijving" />
            </div>
            <div class="form-group">
                <NcTextArea label="Toelichting" :value.sync="toelichting" />
            </div>
            <div class="form-group">
                <NcTextField label="Bronorganisatie" :value.sync="bronorganisatie" />
            </div>
            <div class="form-group">
                <NcTextField label="VerantwoordelijkeOrganisatie" :value.sync="verantwoordelijkeOrganisatie" />
            </div>
            <div class="form-group">
                <NcTextField label="Zaaktype" :value.sync="zaaktype" />
            </div>
            <div class="form-group">
                <NcTextField label="Archiefstatus" :value.sync="archiefstatus" />
            </div>
            <div class="form-group">
                <NcTextField label="Startdatum" :value.sync="startdatum" />
            </div>
            <NcButton
                :disabled="!identificatie || !omschrijving || !toelichting || !bronorganisatie || !verantwoordelijkeOrganisatie || !zaaktype || !archiefstatus || !startdatum"
                @click="addZaak" type="primary">
                Submit
            </NcButton>
        </div>
    </NcModal>

</template>

<script>
import { NcButton, NcModal, NcTextField, NcTextArea } from '@nextcloud/vue';
import { isModalOpen } from '../modalContext.js';

export default {
    name: "DocumentenToevoegenModal",
    props: {
        modalOpen: {
            type: Boolean,
            required: true
        },
        closeAction: {
            type: Function,
            required: true
        },
        submitAction: {
            type: Function,
            required: true
        },
    },
    data() {
        return {

            identificatie: '',
            omschrijving: '',
            toelichting: '',
            bronorganisatie: '',
            verantwoordelijkeOrganisatie: '',
            zaaktype: '',
            archiefstatus: '',
            startdatum: '',
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
            isModalOpen.documentToevoegen = false
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
</style>
