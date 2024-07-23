<script setup>
import { UIStore, metadataStore } from '../../store/store.js'
</script>
<template>
	<NcModal
		v-if="UIStore.modal === 'addMetadataDataModal'"
		ref="modalRef"
		@close="UIStore.setModal(false)">
		<div class="modal__content">
			<h2>Eigenschap toevoegen</h2>
			<NcNoteCard v-if="success" type="success">
				<p>Eigenschap succesvol toegevoegd</p>
			</NcNoteCard>
			<NcNoteCard v-if="error" type="error">
				<p>{{ error }}</p>
			</NcNoteCard>

			<div v-if="!success" class="form-group">
				<NcTextField :disabled="loading"
					label="Naam"
					required
					:value.sync="propertyName" />

				<NcSelect v-bind="typeOptions"
					v-model="properties.type"
					required />

				<NcTextField :disabled="loading"
					label="description"
					:value.sync="properties.description" />

				<NcTextField :disabled="loading"
					label="format"
					:value.sync="properties.format" />

				<NcTextField :disabled="loading"
					label="max date"
					:value.sync="properties.maxDate" />

				<NcCheckboxRadioSwitch
					:disabled="loading"
					:checked.sync="properties.required">
					Required
				</NcCheckboxRadioSwitch>

				<NcCheckboxRadioSwitch
					:disabled="loading"
					:checked.sync="properties.default">
					Default
				</NcCheckboxRadioSwitch>

				<NcCheckboxRadioSwitch
					:disabled="loading"
					:checked.sync="properties.cascadeDelete">
					Cascade delete
				</NcCheckboxRadioSwitch>

				<NcTextField :disabled="loading"
					type="number"
					label="Exclusive minimum"
					:value.sync="properties.exclusiveMinimum" />
			</div>

			<NcButton v-if="!success"
				:disabled="!propertyName || !properties.type || loading"
				type="primary"
				@click="AddMetadata()">
				<template #icon>
					<span>
						<NcLoadingIcon v-if="loading" :size="20" />
						<Plus v-if="!loading" :size="20" />
					</span>
				</template>
				Toevoegen
			</NcButton>

			<NcButton v-if="success"
				type="primary"
				@click="UIStore.setModal(false)">
				Sluiten
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
	name: 'AddMetaDataPropertyModal',
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

			propertyName: '',
			properties: {
				type: '',
				description: '',
				format: '',
				maxDate: '',
				required: false,
				default: false,
				cascadeDelete: false,
				exclusiveMinimum: '',
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
			success: false,
			error: false,
			hasUpdated: false,
		}
	},
	methods: {
		AddMetadata() {
			metadataStore.metaDataItem.properties[this.propertyName] = this.properties

			this.loading = true
			fetch(
				`/index.php/apps/opencatalogi/api/metadata/${metadataStore.metaDataItem.id}`,
				{
					method: 'PUT',
					headers: {
						'Content-Type': 'application/json',
					},
					body: JSON.stringify(metadataStore.metaDataItem),
				},
			)
				.then((response) => {
					this.loading = false
					this.success = true
					// Lets refresh the catalogiList
					metadataStore.refreshMetaDataList()
					response.json().then((data) => {
						metadataStore.setMetaDataItem(data)
					})
					setTimeout(() => {
						// lets reset
						this.propertyName = ''
						this.properties = {
							type: '',
							description: '',
							format: '',
							maxDate: '',
							required: false,
							default: false,
							cascadeDelete: false,
							exclusiveMinimum: '',
						}
						UIStore.setModal(false)
					    this.success = false
					}, 2000)
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
