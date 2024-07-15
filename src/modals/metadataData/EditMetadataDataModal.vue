<script setup>
import { store } from '../../store.js'
</script>
<template>
	<NcModal
		v-if="store.modal === 'editMetadataDataModal'"
		ref="modalRef"
		@close="store.setModal(false)">
		<div v-if="!loading" class="modal__content">
			<h2>Edit Metadata data {{ store.metadataDataKey }}</h2>

			<div v-if="!metadataLoading">
				<div class="form-group">
					<NcSelect v-bind="typeOptions"
						v-model="metadata.properties[store.metadataDataKey].type"
						required
						:loading="metadataLoading" />

					<NcTextField :disabled="loading"
						label="description"
						:value.sync="metadata.properties[store.metadataDataKey].description"
						:loading="metadataLoading" />

					<NcTextField :disabled="loading"
						label="format"
						:value.sync="metadata.properties[store.metadataDataKey].format"
						:loading="metadataLoading" />

					<NcTextField :disabled="loading"
						label="max date"
						:value.sync="metadata.properties[store.metadataDataKey].maxDate"
						:loading="metadataLoading" />

					<NcTextField :disabled="loading"
						label="reference"
						:value.sync="metadata.properties[store.metadataDataKey].$ref"
						:loading="metadataLoading" />
                    
                    <NcCheckboxRadioSwitch
                        :checked.sync="metadata.properties[store.metadataDataKey].required">
                        Required
                    </NcCheckboxRadioSwitch>
                    
                    <NcCheckboxRadioSwitch
                        :checked.sync="metadata.properties[store.metadataDataKey].default">
                        Default
                    </NcCheckboxRadioSwitch>
                    
                    <NcCheckboxRadioSwitch
                        :checked.sync="metadata.properties[store.metadataDataKey].cascadeDelete">
                        Cascade delete
                    </NcCheckboxRadioSwitch>
				</div>

				<div v-if="succesMessage" class="success">
					Succesfully updated metadata
				</div>
			</div>
			<NcLoadingIcon
				v-if="metadataLoading"
				:size="100"
				appearance="dark"
				name="Metadata details aan het laden" />

			<NcButton :disabled="!metadataLoading" type="primary" @click="updateMetadata(metadata.id)">
				Submit
			</NcButton>
		</div>

		<NcLoadingIcon
			v-if="loading"
			:size="100" />
	</NcModal>
</template>

<script>
import {
	NcButton,
	NcModal,
	NcTextField,
	NcSelect,
    NcCheckboxRadioSwitch,
	NcLoadingIcon,
} from '@nextcloud/vue'

export default {
	name: 'EditMetadataDataModal',
	components: {
		NcModal,
		NcTextField,
		NcSelect,
        NcCheckboxRadioSwitch,
		NcButton,
		NcLoadingIcon,
	},
	data() {
		return {
			metadata: {
				title: '',
				description: '',
				version: '',
				properties: {},
				_schema: '',
				_id: '',
				id: '',
			},
			typeOptions: {
				inputLabel: 'Type',
				multiple: false,
				options: [
					'string',
					'boolean',
					'object',
					'array',
				],
			},
			loading: false,
			succesMessage: false,
			hasUpdated: false,
			metadataLoading: false,
		}
	},
	updated() {
        if (store.modal === 'editMetadataDataModal' && this.hasUpdated) {
			if (this.metadata._id === store.metaDataItem._id || this.metadata.id === store.metaDataItem.id) return
			this.hasUpdated = false
		}
		if (store.modal === 'editMetadataDataModal' && !this.hasUpdated) {
			this.metadata = {
				...store.metaDataItem,
				properties: JSON.parse(store.metaDataItem.properties),
			}
			this.fetchData(store.metaDataItem.id)
			this.hasUpdated = true

			this.prepareDataKeys()
		}
	},
	methods: {
		fetchData(id) {
			this.metadataLoading = true
			fetch(
				`/index.php/apps/opencatalogi/api/metadata/${id}`,
				{
					method: 'GET',
				},
			)
				.then((response) => {
					response.json().then((data) => {
						this.metadata = {
							...this.metadata,
							...data,
							properties: JSON.parse(data.properties),
						}
					})
					this.metadataLoading = false
				})
				.catch((err) => {
					console.error(err)
					this.metadataLoading = false
				})
		},
		prepareDataKeys() {
			const data = this.metadata.properties[store.metadataDataKey]

			if (!data?.type) data.type = ''
			if (!data?.description) data.description = ''
			if (!data?.required) data.required = false
			if (!data?.default) data.default = false
			if (!data?.format) data.format = ''
			if (!data?.$ref) data.$ref = ''
			if (!data?.cascadeDelete) data.cascadeDelete = false
			if (!data?.maxDate) data.maxDate = ''

			// items object
			if (!data?.items) data.items = {}
			if (!data?.items?.$ref) data.items.$ref = ''
		},
		closeModal() {
			store.modal = false
		},
		updateMetadata(id) {
			this.loading = true
			fetch(
				`/index.php/apps/opencatalogi/api/metadata/${id}`,
				{
					method: 'PUT',
					headers: {
						'Content-Type': 'application/json',
					},
					body: JSON.stringify({
						...this.metadata,
						properties: JSON.stringify(this.metadata.properties),
					}),
				},
			)
				.then((response) => {
					this.closeModal()
				})
				.catch((err) => {
					this.loading = false
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
