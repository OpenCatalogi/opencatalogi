<script setup>
import { store } from '../../store.js'
</script>
<template>
	<NcModal
		v-if="store.modal === 'addMetadataDataModal'"
		ref="modalRef"
		@close="store.setModal(false)">
		<div class="modal__content">
			<h2>Add Metadata eigenschap</h2>

			<div v-if="success === -1" class="form-group">
				<NcTextField :disabled="loading"
					label="Naam"
					required
					:value.sync="propertyName"
					:loading="loading" />

				<NcSelect v-bind="typeOptions"
					v-model="properties.type"
					required
					:loading="loading" />

				<NcTextField :disabled="loading"
					label="description"
					:value.sync="properties.description"
					:loading="loading" />

				<NcTextField :disabled="loading"
					label="format"
					:value.sync="properties.format"
					:loading="loading" />

				<NcTextField :disabled="loading"
					label="max date"
					:value.sync="properties.maxDate"
					:loading="loading" />

				<NcTextField :disabled="loading"
					label="reference"
					:value.sync="properties.$ref"
					:loading="loading" />

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
					:value.sync="properties.exclusiveMinimum"
					:loading="loading" />
			</div>

			<NcButton v-if="success === -1"
				:loading="loading"
				:disabled="loading"
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

			<div v-if="success > -1">
				<NcNoteCard v-if="success" type="success" heading="Success!">
					<p>Succesvol metadata eigenschap toegevoegd</p>
				</NcNoteCard>
				<NcNoteCard v-if="!success" type="error" heading="Error!">
					<p>{{ successMessage }}</p>
				</NcNoteCard>

				<NcButton
					type="primary"
					@click="closeModal">
					Sluiten
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
import Plus from 'vue-material-design-icons/Plus.vue'

export default {
	name: 'AddMetadataDataModal',
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
				$ref: '',
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
			success: -1,
			successMessage: '',
			hasUpdated: false,
		}
	},
	methods: {
		closeModal() {
			store.modal = false
		},
		AddMetadata() {
			const metadata = store.metaDataItem
			metadata.properties = JSON.parse(metadata.properties)
			metadata.properties[this.propertyName] = this.properties

			this.loading = true
			fetch(
				`/index.php/apps/opencatalogi/api/metadata/${metadata.id}`,
				{
					method: 'PUT',
					headers: {
						'Content-Type': 'application/json',
					},
					body: JSON.stringify({
						...metadata,
						properties: JSON.stringify(metadata.properties),
					}),
				},
			)
				.then((response) => {
					this.loading = false
					this.success = 1
					setTimeout(() => {
						this.closeModal()
					    this.success = -1
					}, 3000)
				})
				.catch((err) => {
					this.loading = false
					this.success = 0
					this.successMessage = err
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
