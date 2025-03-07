<script setup>
import { navigationStore, publicationStore } from '../../store/store.js'
import { getTheme } from '../../services/getTheme.js'

</script>
<template>
	<NcModal v-if="navigationStore.modal === 'addPublicationData'"
		ref="modalRef"
		class="addPublicationPropertyModal"
		label-id="addPublicationPropertyModal"
		@close="closeModal">
		<div class="modal__content">
			<h2>Publicatie eigenschap toevoegen</h2>
			<div v-if="success !== null || error">
				<NcNoteCard v-if="success" type="success">
					<p>Publicatie eigenschap succesvol toegevoegd</p>
				</NcNoteCard>
				<NcNoteCard v-if="!success" type="error">
					<p>Er is iets fout gegaan bij het toevoegen van Publicatie eigenschap</p>
				</NcNoteCard>
				<NcNoteCard v-if="error" type="error">
					<p>{{ error }}</p>
				</NcNoteCard>
			</div>
			<NcNoteCard v-if="getSelectedPublicationTypeProperty?.deprecated || false" type="warning">
				<p>Deze eigenschap staat gemarkeerd als afgeschaft, hij zal bij een komende versie van het onderliggende publicatietype waarschijnlijk komen te vervallen.</p>
			</NcNoteCard>
			<div v-if="success === null" class="form-group">
				<NcSelect v-bind="mapPublicationTypeEigenschappen"
					v-model="eigenschappen.value"
					required />

				<div v-if="!!getSelectedPublicationTypeProperty">
					<!-- TYPE : STRING -->
					<div v-if="getSelectedPublicationTypeProperty.type === 'string'">
						<NcDateTimePicker v-if="getSelectedPublicationTypeProperty.format === 'date'"
							v-model="value"
							type="date"
							label="Waarde"
							:error="!verifyInput.success"
							:helper-text="!verifyInput.success ? verifyInput.helperText : ''"
							:disabled="loading"
							:loading="loading" />

						<NcDateTimePicker v-else-if="getSelectedPublicationTypeProperty.format === 'time'"
							v-model="value"
							type="time"
							label="Waarde"
							:error="!verifyInput.success"
							:helper-text="!verifyInput.success ? verifyInput.helperText : ''"
							:disabled="loading"
							:loading="loading" />

						<NcDateTimePicker v-else-if="getSelectedPublicationTypeProperty.format === 'date-time'"
							v-model="value"
							type="datetime"
							label="Waarde"
							:error="!verifyInput.success"
							:helper-text="!verifyInput.success ? verifyInput.helperText : ''"
							:disabled="loading"
							:loading="loading" />

						<NcInputField v-else-if="getSelectedPublicationTypeProperty.format === 'email'"
							:value.sync="value"
							label="Email"
							type="email"
							:error="!verifyInput.success"
							:helper-text="!verifyInput.success ? verifyInput.helperText : ''"
							:disabled="loading"
							:loading="loading" />

						<NcInputField v-else-if="getSelectedPublicationTypeProperty.format === 'idn-email'"
							:value.sync="value"
							label="IDN-Email"
							type="email"
							:error="!verifyInput.success"
							:helper-text="!verifyInput.success ? verifyInput.helperText : ''"
							:disabled="loading"
							:loading="loading" />

						<NcTextField v-else-if="getSelectedPublicationTypeProperty.format === 'regex'"
							:value.sync="value"
							label="Waarde (regex)"
							:error="!verifyInput.success"
							:helper-text="!verifyInput.success ? verifyInput.helperText : ''"
							:disabled="loading"
							:loading="loading" />

						<NcInputField v-else-if="getSelectedPublicationTypeProperty.format === 'password'"
							:value.sync="value"
							type="password"
							label="Wachtwoord"
							:error="!verifyInput.success"
							:helper-text="!verifyInput.success ? verifyInput.helperText : ''"
							:disabled="loading"
							:loading="loading" />

						<NcInputField v-else-if="getSelectedPublicationTypeProperty.format === 'telephone'"
							:value.sync="value"
							type="tel"
							label="Telefoon nummer"
							:error="!verifyInput.success"
							:helper-text="!verifyInput.success ? verifyInput.helperText : ''"
							:disabled="loading"
							:loading="loading" />

						<NcTextField v-else
							:value.sync="value"
							label="Waarde"
							:error="!verifyInput.success"
							:helper-text="!verifyInput.success ? verifyInput.helperText : ''"
							:disabled="loading"
							:loading="loading" />
					</div>

					<!-- TYPE : NUMBER -->
					<NcInputField v-else-if="getSelectedPublicationTypeProperty.type === 'number'"
						:value.sync="value"
						type="number"
						step="any"
						label="Nummer"
						required
						:error="!verifyInput.success"
						:helper-text="!verifyInput.success ? verifyInput.helperText : ''"
						:disabled="loading"
						:loading="loading" />

					<!-- TYPE : INTEGER -->
					<NcInputField v-else-if="getSelectedPublicationTypeProperty.type === 'integer'"
						:value.sync="value"
						type="number"
						step="1"
						label="Integer"
						required
						:error="!verifyInput.success"
						:helper-text="!verifyInput.success ? verifyInput.helperText : ''"
						:disabled="loading"
						:loading="loading" />

					<!-- TYPE : OBJECT -->
					<div v-else-if="getSelectedPublicationTypeProperty.type === 'object'" :class="`codeMirrorContainer ${getTheme()}`">
						<CodeMirror
							v-model="value"
							:basic="true"
							:dark="getTheme() === 'dark'"
							:tab="true"
							:linter="jsonParseLinter()"
							:lang="json()" />
						<NcButton class="prettifyButton" @click="prettifyJson">
							<template #icon>
								<AutoFix :size="20" />
							</template>
							Prettify
						</NcButton>
					</div>

					<!-- TYPE : ARRAY -->
					<NcTextArea v-else-if="getSelectedPublicationTypeProperty.type === 'array'"
						:value.sync="value"
						label="Waarde lijst (splitst op ,)"
						:error="!verifyInput.success"
						:helper-text="!verifyInput.success ? verifyInput.helperText : ''"
						:disabled="loading"
						:loading="loading" />

					<!-- TYPE : BOOLEAN -->
					<NcCheckboxRadioSwitch v-else-if="getSelectedPublicationTypeProperty.type === 'boolean'"
						:checked.sync="value"
						:error="!verifyInput.success"
						:helper-text="!verifyInput.success ? verifyInput.helperText : ''"
						:disabled="loading"
						:loading="loading">
						Waarde
					</NcCheckboxRadioSwitch>
				</div>
			</div>

			<span class="flex-horizontal">
				<NcButton v-if="success === null"
					:disabled="loading
						|| !eigenschappen.value?.id
						|| !verifyInput.success
					"
					type="primary"
					@click="AddPublicatieEigenschap()">
					<template #icon>
						<span>
							<NcLoadingIcon v-if="loading" :size="20" />
							<Plus v-if="!loading" :size="20" />
						</span>
					</template>
					Toevoegen
				</NcButton>

				<NcButton
					@click="navigationStore.setModal(false)">
					{{ success ? 'Sluiten' : 'Annuleer' }}
				</NcButton>
			</span>
		</div>
	</NcModal>
</template>

<!-- eslint-disable no-console -->
<script>
import {
	NcButton,
	NcModal,
	NcTextField,
	NcTextArea,
	NcDateTimePicker,
	NcCheckboxRadioSwitch,
	NcInputField,
	NcNoteCard,
	NcLoadingIcon,
	NcSelect,
} from '@nextcloud/vue'
import { verifyInput as _verifyInput } from './verifyInput.js'
import { setDefaultValue as _setDefaultValue } from './setDefaultValue.js'

import CodeMirror from 'vue-codemirror6'
import { json, jsonParseLinter } from '@codemirror/lang-json'

// icons
import Plus from 'vue-material-design-icons/Plus.vue'
import AutoFix from 'vue-material-design-icons/AutoFix.vue'

import { Publication } from '../../entities/index.js'
import _ from 'lodash'

export default {
	name: 'AddPublicationDataModal',
	components: {
		NcModal,
		NcTextField,
		NcTextArea,
		NcDateTimePicker,
		NcCheckboxRadioSwitch,
		NcInputField,
		NcSelect,
		NcButton,
		NcNoteCard,
		NcLoadingIcon,
		CodeMirror,
	},
	data() {
		return {
			eigenschappen: {},
			publicationType: {},
			value: '',
			loading: false,
			success: null,
			error: false,
		}
	},
	computed: {
		// I write documentation to help me understand what I need to do.

		/**
		 * Takes the properties from the publication type in the store and loops through them, returning only the items not in the publication data
		 * @return {[string, object][]} list of publication type properties NOT in the publication data alongside their key
		 */
		getFilteredPublicationTypeProperties() {
			if (!publicationStore.publicationPublicationType?.properties) return []
			return Object.entries(publicationStore.publicationPublicationType?.properties)
				.filter(([key]) => !Object.keys(publicationStore.publicationItem?.data ?? {}).includes(key))
		},
		/**
		 * based on the result `getFilteredPublicationTypeProperties` gives AND the selected value in the eigenschappen dropdown,
		 * it will return the full publication type property of the selected property, containing the rules for the data.
		 *
		 * It will return `null` if no property is selected
		 * @see getFilteredPublicationTypeProperties
		 * @return {object | null} A single publication type properties object or null
		 */
		getSelectedPublicationTypeProperty() {
			return this.getFilteredPublicationTypeProperties.find(([key]) => key === this.eigenschappen.value?.id)?.[1] || null
		},
		mapPublicationTypeEigenschappen() {
			if (publicationStore.publicationPublicationType) {
				const incomingUrl = publicationStore.publicationPublicationType.source && new URL(publicationStore.publicationPublicationType.source)
				if (incomingUrl?.host !== window.location.host) {
					return {
						inputLabel: 'Publicatietype eigenschap',
						options: Object.entries(publicationStore.publicationPublicationType?.properties)
							.filter(([key]) => !Object.keys(publicationStore.publicationItem?.data).includes(key))
							.map(([key]) => ({
								id: key,
								label: key,
							})),
					}
				}
			}

			return {
				inputLabel: 'Publicatietype eigenschap',
				options: this.getFilteredPublicationTypeProperties
					.map(([key]) => ({
						id: key,
						label: key,
					})),
			}
		},
		/**
		 * Takes the value the user types in and tests it against various rules from `getSelectedPublicationTypeProperty`.
		 * Which then returns a success boolean and a helper text containing the error message when success is false.
		 *
		 * @see getSelectedPublicationTypeProperty
		 */
		verifyInput() {
			return _verifyInput(this.getSelectedPublicationTypeProperty, this.value)
		},
	},
	watch: {
		getSelectedPublicationTypeProperty(newVal) {
			console.log('New selected publication type property', newVal)
			this.setDefaultValue(newVal)
		},
	},
	methods: {
		/**
		 * Accepts the selected publication type property, and changes the value property in `data()` to the default value from the property.
		 *
		 * Depending on the property.type, it will put in specialized data, such as `object` or `boolean`.
		 *
		 * This function only runs when the selected publication type property changes
		 * @param {object} SelectedPublicationTypeProperty The publication type property Object containing the rules
		 * @see getSelectedPublicationTypeProperty
		 */
		setDefaultValue(SelectedPublicationTypeProperty) {
			this.value = _setDefaultValue(SelectedPublicationTypeProperty)
		},
		closeModal() {
			this.success = null
			this.value = ''
			this.eigenschappen = {}
			this.publicationType = {}
			this.error = false
			this.navigationStore.setModal(false)
		},
		AddPublicatieEigenschap() {
			this.loading = true

			const publicationClone = _.cloneDeep(publicationStore.publicationItem)

			publicationClone.data[this.eigenschappen.value?.id] = this.value
			delete publicationClone.publicationDate

			const newPublicationItem = new Publication({
				...publicationClone,
				catalog: publicationClone.catalog?.id ?? publicationClone.catalog,
				publicationType: publicationClone.publicationType?.id ?? publicationClone.publicationType,
			})

			publicationStore.editPublication(newPublicationItem)
				.then(({ response }) => {
					this.loading = false
					this.success = response.ok

					// Wait for the user to read the feedback then close the model
					setTimeout(this.closeModal, 2000)

					// reset modal form
					this.eigenschappen = {}
					this.data = ''
				})
				.catch((err) => {
					this.loading = false
					this.error = err
				})
		},
		prettifyJson() {
			this.value = JSON.stringify(JSON.parse(this.value), null, 2)
		},
		fetchPublicationType(publicationTypeUrl, loading) {

			if (loading) { this.publicationTypeLoading = true }

			fetch(`/index.php/apps/opencatalogi/api/publication_types?source=${publicationTypeUrl}`, {
				method: 'GET',
			})
				.then((response) => {
					response.json().then((data) => {
						this.publicationType = data.results[0]
					})
					if (loading) { this.publicationTypeLoading = false }
				})
				.catch((err) => {
					console.error(err)
					if (loading) { this.publicationTypeLoading = false }
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

.flex-horizontal {
    display: flex;
    gap: 4px;
}

.addPublicationPropertyModal .mx-datepicker {
    margin-top: 0rem;
    transition: margin 400ms;
}
.addPublicationPropertyModal .mx-datepicker:has(.mx-datepicker-popup) {
    margin-top: 12rem;
}
</style>
