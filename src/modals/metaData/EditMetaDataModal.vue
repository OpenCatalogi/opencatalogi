<script setup>
import { store } from '../../store.js'
</script>

<template>
	<NcModal
		v-if="store.modal === 'metaDataEdit'"
		ref="modalRef"
		@close="store.setModal(false)">
		<div v-if="!loading" class="modal__content">
			<h2>MetaData bewerken</h2>
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
				<NcTextField label="Tooi categorie naam" :value.sync="metaData.tooiCategorieNaam" />
			</div>
			<div class="form-group">
				<NcTextField label="Tooi categorie id" :value.sync="metaData.tooiCategorieId" />
			</div>
			<div class="form-group">
				<NcTextField label="Tooi categorie uri" :value.sync="metaData.tooiCategorieUri" />
			</div>
			<div class="form-group">
				<NcTextField label="Tooi thema naam" :value.sync="metaData.tooiThemaNaam" />
			</div>
			<div class="form-group">
				<NcTextField label="Tooi thema uri" :value.sync="metaData.tooiThemaUri" />
			</div>
			<div v-if="succesMessage" class="success">
				Succesfully updated metaData
			</div>

			<NcButton :disabled="!metaDataName" type="primary" @click="editMetaData">
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
	name: 'EditMetaDataModal',
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
			metaData: {
				tooiCategorieNaam: '',
				tooiCategorieId: '',
				tooiCategorieUri: '',
				tooiThemaNaam: '',
				tooiThemaUri: '',
			},
			succesMessage: false,
			hasUpdated: false,
			loading: false,
		}
	},
	updated() {
		if (!this.hasUpdated) {
			this.fetchData(store.metaDataItem)
			this.hasUpdated = true
		}
	},
	methods: {
		fetchData(id) {
			this.metaDataLoading = true
			fetch(
				`/index.php/apps/opencatalogi/metadata/api/${id}`,
				{
					method: 'GET',
				},
			)
				.then((response) => {
					response.json().then((data) => {
						this.metaData = data
					})
					this.metaDataLoading = false
				})
				.catch((err) => {
					console.error(err)
					this.metaDataLoading = false
				})
		},
		closeModal() {
			store.modal = false
		},
		editMetaData() {
			this.closeModal()
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
