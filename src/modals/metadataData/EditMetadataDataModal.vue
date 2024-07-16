<script setup>
import { store } from '../../store.js'
</script>
<template>
	<NcModal
		v-if="store.modal === 'editMetadataDataModal'"
		ref="modalRef"
		@close="store.setModal(false)">
		<div class="modal__content">
			<h2>Edit Metadata property {{ store.metadataDataKey }}</h2>

			<div v-if="success === -1" class="form-group">
				<NcSelect v-bind="typeOptions"
					v-model="metadata.properties[store.metadataDataKey].type"
					required
					:loading="loading" />

				<NcTextField :disabled="loading"
					label="description"
					:value.sync="metadata.properties[store.metadataDataKey].description"
					:loading="loading" />

				<NcTextField :disabled="loading"
					label="format"
					:value.sync="metadata.properties[store.metadataDataKey].format"
					:loading="loading" />

				<NcTextField :disabled="loading"
					label="max date"
					:value.sync="metadata.properties[store.metadataDataKey].maxDate"
					:loading="loading" />

				<NcTextField :disabled="loading"
					label="reference"
					:value.sync="metadata.properties[store.metadataDataKey].$ref"
					:loading="loading" />

				<NcCheckboxRadioSwitch
					:disabled="loading"
					:checked.sync="metadata.properties[store.metadataDataKey].required">
					Required
				</NcCheckboxRadioSwitch>

				<NcCheckboxRadioSwitch
					:disabled="loading"
					:checked.sync="metadata.properties[store.metadataDataKey].default">
					Default
				</NcCheckboxRadioSwitch>

				<NcCheckboxRadioSwitch
					:disabled="loading"
					:checked.sync="metadata.properties[store.metadataDataKey].cascadeDelete">
					Cascade delete
				</NcCheckboxRadioSwitch>

				<NcTextField :disabled="loading"
					type="number"
					label="Exclusive minimum"
					:value.sync="metadata.properties[store.metadataDataKey].exclusiveMinimum"
					:loading="loading" />
			</div>

			<NcButton v-if="success === -1"
				:loading="loading"
				:disabled="loading"
				type="primary"
				@click="updateMetadata(metadata.id)">
				<template #icon>
					<span>
						<NcLoadingIcon v-if="loading" :size="20" />
						<Pencil v-if="!loading" :size="20" />
					</span>
				</template>
				Submit
			</NcButton>

			<div v-if="success > -1">
				<NcNoteCard v-if="success" type="success" heading="Success!">
					<p>Successfully updated metadata property {{ store.metadataDataKey }}</p>
				</NcNoteCard>
				<NcNoteCard v-if="!success" type="error" heading="Error!">
					<p>Something went wrong</p>
				</NcNoteCard>

				<NcButton
					type="primary"
					@click="closeModal">
					Close
				</NcButton>
			</div>
		</div>
	</NcModal>
</template>

<script>
import {
	NcButton,
	NcModal,
	NcTextField,
	NcSelect,
	NcCheckboxRadioSwitch,
	NcNoteCard,
	NcLoadingIcon,
} from '@nextcloud/vue'

// icons
import Pencil from 'vue-material-design-icons/Pencil.vue'

export default {
	name: 'EditMetadataDataModal',
	components: {
		NcModal,
		NcTextField,
		NcSelect,
		NcCheckboxRadioSwitch,
		NcButton,
		NcNoteCard,
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
			dataKey: '',
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
			success: -1,
			hasUpdated: false,
		}
	},
	updated() {
		if (store.modal === 'editMetadataDataModal' && this.hasUpdated) {
			if (this.dataKey !== store.metadataDataKey) this.hasUpdated = false
		}
		if (store.modal === 'editMetadataDataModal' && !this.hasUpdated) {
			this.metadata = {
				...store.metaDataItem,
				properties: JSON.parse(store.metaDataItem.properties),
			}
			this.metadata.properties[store.metadataDataKey] = store.getMetadataPropertyKeys(store.metadataDataKey)
			this.fetchData(store.metaDataItem.id)

			this.dataKey = store.metadataDataKey
			this.hasUpdated = true
		}
	},
	methods: {
		fetchData(id) {
			this.loading = true
			fetch(
				`/index.php/apps/opencatalogi/api/metadata/${id}`,
				{
					method: 'GET',
				},
			)
				.then((response) => {
					response.json().then((data) => {
						store.metaDataItem = data

						this.metadata = {
							...store.metaDataItem,
							properties: JSON.parse(store.metaDataItem.properties),
						}
						this.metadata.properties[store.metadataDataKey] = store.getMetadataPropertyKeys(store.metadataDataKey)
					})
					this.loading = false
				})
				.catch((err) => {
					console.error(err)
					this.loading = false
				})
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
					this.loading = false
					this.success = 1
					setTimeout(() => this.closeModal(), 3000)
				})
				.catch((err) => {
					this.loading = false
					this.success = 0
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

.form-group .group {
    margin-block-end: 2rem;
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
