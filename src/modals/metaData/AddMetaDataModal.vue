<script setup>
import { store } from '../../store.js'
</script>

<template>
	<NcModal
		v-if="store.modal === 'metaDataAdd'"
		ref="modalRef"
		@close="store.setModal(false)">
		<div v-if="!loading" class="modal__content">
			<h2>MetaData toevoegen</h2>
			<div class="form-group">
				<NcTextField :disabled="loading"
					label="Naam"
					maxlength="255"
					:value.sync="name"
					required />
				<NcTextField :disabled="loading"
					label="Samenvatting"
					maxlength="255"
					:value.sync="summery" />
			</div>
			<div class="form-group">
				<NcTextField label="Tooi categorie naam" :value.sync="tooiCategorieNaam" />
			</div>
			<div class="form-group">
				<NcTextField label="Tooi categorie id" :value.sync="tooiCategorieId" />
			</div>
			<div class="form-group">
				<NcTextField label="Tooi categorie uri" :value.sync="tooiCategorieUri" />
			</div>
			<div class="form-group">
				<NcTextField label="Tooi thema naam" :value.sync="tooiThemaNaam" />
			</div>
			<div class="form-group">
				<NcTextField label="Tooi thema uri" :value.sync="tooiThemaUri" />
			</div>
			<div v-if="succesMessage" class="success">
				Succesfully added MetaData
			</div>

			<NcButton :disabled="!tooiCategorieNaam" type="primary" @click="addMetaData">
				Submit
			</NcButton>
		</div>
		<NcLoadingIcon
			v-if="loading"
			:size="100" />
	</NcModal>
</template>

<script>
import { NcButton, NcModal, NcTextField, NcLoadingIcon } from '@nextcloud/vue'

export default {
	name: 'AddMetaDataModal',
	components: {
		NcModal,
		NcTextField,
		NcButton,
		NcLoadingIcon,
	},
	data() {
		return {
			name: '',
			summery: '',
			tooiCategorieNaam: '',
			tooiCategorieId: '',
			tooiCategorieUri: '',
			tooiThemaNaam: '',
			tooiThemaUri: '',
			succesMessage: false,
			loading: false,
		}
	},
	methods: {
		closeModal() {
			store.modal = false
		},
		addMetaData() {
			this.closeModal()
		},
	},
}
</script>

<style>
.modal__content {
    margin: var(--OC-margin-50);
    text-align: center;
}

.zaakDetailsContainer {
    margin-block-start: var(--OC-margin-20);
    margin-inline-start: var(--OC-margin-20);
    margin-inline-end: var(--OC-margin-20);
}

.success {
    color: green;
}
</style>
