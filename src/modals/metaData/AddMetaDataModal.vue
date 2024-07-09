<script setup>
import { store } from '../../store.js'
</script>

<template>
	<NcModal
	v-if="store.modal === 'metaDataAdd'"
	ref="modalRef"
	@close="store.setModal(false)">
		<div class="modal__content">
			<h2>Add MetaData</h2>
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
	</NcModal>
</template>

<script>
import { NcButton, NcModal, NcTextField, NcTextArea } from '@nextcloud/vue'
import { store } from '../../store.js'

export default {
	name: 'AddMetaDataModal',
	components: {
		NcModal,
		NcTextField,
		NcTextArea,
		NcButton
	},
	data() {
		return {
			tooiCategorieNaam: '',
			tooiCategorieId: '',
			tooiCategorieUri: '',
			tooiThemaNaam: '',
			tooiThemaUri: '',
			succesMessage: false
		}
	},
	methods: {
		closeModal() {
			store.modal = false
		},
		addMetaData() {
			this.$emit('metaData', this.name)
			this.succesMessage = true
			setTimeout(() => this.succesMessage = false, 2500)
			this.name = ''
		},
	},
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
