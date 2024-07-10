<script setup>
import { store } from '../../store.js'
</script>

<template>
	<NcModal
		v-if="store.modal === 'editMetaData'"
		ref="modalRef"
		@close="store.setModal(false)">
		<div v-if="!loading" class="modal__content">
			<h2>MetaData bewerken</h2>
			<div class="form-group">
				<NcTextField label="Titel" :value.sync="metaData.title" required="true" />
			</div>
			<div class="form-group">
				<NcTextField label="Versie" :value.sync="metaData.version" />
			</div>
			<div class="form-group">
				<NcTextArea label="Beschrijving" :value.sync="metaData.description" />
			</div>
			<div class="form-group">
				<NcTextArea label="Properties" :value.sync="metaData.properties" />
			</div>
			<div v-if="succesMessage" class="success">
				Succesfully updated metaData
			</div>

			<NcButton :disabled="!metaData.title" type="primary" @click="editMetaData">
				Opslaan
			</NcButton>
		</div>
		<NcLoadingIcon
			v-if="loading"
			:size="100" />
	</NcModal>
</template>

<script>
import { NcButton, NcModal, NcTextField, NcTextArea, NcLoadingIcon } from '@nextcloud/vue'

export default {
	name: 'EditMetaDataModal',
	components: {
		NcModal,
		NcTextField,
		NcTextArea,
		NcButton,
		NcLoadingIcon,
	},
	data() {
		return {
			name: '',
			summery: '',
			metaData: {
				title: '',
				version: '',
				description: '',
				properties: '',
			},
			succesMessage: false,
			hasUpdated: false,
			loading: false,
		}
	},
	updated() {
		if (store.modal === 'editMetaData' && this.hasUpdated) {
			if (this.metaData._id === store.metaDataId) return
			this.hasUpdated = false
		}
		if (store.modal === 'editMetaData' && !this.hasUpdated) {
			this.fetchData(store.metaDataId)
			this.hasUpdated = true
			this.metaData = store.metaDataItem
		}
	},
	methods: {
		fetchData(id) {
			this.metaDataLoading = true
			fetch(
				`/index.php/apps/opencatalogi/api/metadata/${id}`,
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
			this.metaDataLoading = true
			fetch(
				`/index.php/apps/opencatalogi/api/metadata/${store.metaDataId}`,
				{
					method: 'PUT',
					headers: {
						'Content-Type': 'application/json',
					},
					body: JSON.stringify(this.metaData),
				},
			).then((response) => {
				this.closeModal()
			}).catch((err) => {
				this.metaDataLoading = false
				console.error(err)
			})
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
