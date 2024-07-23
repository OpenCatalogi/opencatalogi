<script setup>
import { useUIStore, useMetadataStore } from '../../store/store.js'
</script>
<template>
	<NcModal
		v-if="UIStore.modal === 'editMetadataDataModal'"
		ref="modalRef"
		@close="UIStore.setModal(false)">
		<div class="modal__content">
			<h2>Eigenschap "{{ metadataStore.metadataDataKey }}" bewerken</h2>
			<NcNoteCard v-if="succes" type="success">
				<p>Eigenschap succesvol bewerkt</p>
			</NcNoteCard>
			<NcNoteCard v-if="error" type="error">
				<p>{{ error }}</p>
			</NcNoteCard>

			<div v-if="success === -1" class="form-group">
				<NcSelect v-bind="typeOptions"
					:value.sync="metadataStore.metadataDataKey"
					required />

				<NcTextField :disabled="loading"
					label="description"
					:value.sync="metadata.properties[metadataStore.metadataDataKey].description" />

				<NcTextField :disabled="loading"
					label="format"
					:value.sync="metadata.properties[metadataStore.metadataDataKey].format" />

				<NcTextField :disabled="loading"
					label="max date"
					:value.sync="metadata.properties[metadataStore.metadataDataKey].maxDate" />

				<NcCheckboxRadioSwitch
					:disabled="loading"
					:checked.sync="metadata.properties[metadataStore.metadataDataKey].required">
					Required
				</NcCheckboxRadioSwitch>

				<NcCheckboxRadioSwitch
					:disabled="loading"
					:checked.sync="metadata.properties[metadataStore.metadataDataKey].default">
					Default
				</NcCheckboxRadioSwitch>

				<NcCheckboxRadioSwitch
					:disabled="loading"
					:checked.sync="metadata.properties[metadataStore.metadataDataKey].cascadeDelete">
					Cascade delete
				</NcCheckboxRadioSwitch>

				<NcTextField :disabled="loading"
					type="number"
					label="Exclusive minimum"
					:value.sync="metadata.properties[metadataStore.metadataDataKey].exclusiveMinimum" />
			</div>

			<NcButton v-if="!success"
				:disabled="!propertyName || !properties.type || loading"
				type="primary"
				@click="updateMetadata(metadata.id)">
				<template #icon>
					<span>
						<NcLoadingIcon v-if="loading" :size="20" />
						<Plus v-if="!loading" :size="20" />
					</span>
				</template>
				Toevoegen
			</NcButton>
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
import Plus from 'vue-material-design-icons/Plus.vue'

export default {
	name: 'EditMetaDataPropertyModal',
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
			UIStore: useUIStore(),
			metadataStore: useMetadataStore(),
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
			successMessage: '',
			hasUpdated: false,
		}
	},
	updated() {
		if (this.UIStore.modal === 'editMetadataDataModal' && this.hasUpdated) {
			if (this.dataKey !== this.metadataStore.metadataDataKey) this.hasUpdated = false
		}
		if (this.UIStore.modal === 'editMetadataDataModal' && !this.hasUpdated) {
			this.metadata = {
				...this.metadataStore.metaDataItem,
				properties: JSON.parse(this.metadataStore.metaDataItem.properties),
			}
			this.metadata.properties[this.metadataStore.metadataDataKey] = this.metadataStore.getMetadataPropertyKeys(this.metadataStore.metadataDataKey)
			this.fetchData(this.metadataStore.metaDataItem.id)

			this.dataKey = this.metadataStore.metadataDataKey
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
						this.metadataStore.metaDataItem = data

						this.metadata = {
							...this.metadataStore.metaDataItem,
							properties: JSON.parse(this.metadataStore.metaDataItem.properties),
						}
						this.metadata.properties[this.metadataStore.metadataDataKey] = this.metadataStore.getMetadataPropertyKeys(this.metadataStore.metadataDataKey)
					})
					this.loading = false
				})
				.catch((err) => {
					console.error(err)
					this.loading = false
				})
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
					body: JSON.stringify(this.metadataStore.metaDataItem),
				},
			)
				.then((response) => {
					this.loading = false
					this.success = true
					// Lets refresh the catalogiList
					this.metadataStore.refreshMetaDataList()
					response.json().then((data) => {
						this.metadataStore.setMetaDataItem(data)
					})
					setTimeout(() => {
						this.UIStore.setModal(false)
					    this.success = false
					}, 3000)
				})
				.catch((err) => {
					this.loading = false
					this.success = false
					this.error = err
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
