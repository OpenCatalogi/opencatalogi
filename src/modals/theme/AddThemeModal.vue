<script setup>
import { navigationStore, themeStore } from '../../store/store.js'
</script>
<template>
	<NcModal
		v-if="navigationStore.modal === 'themeAdd'"
		ref="modalRef"
		label-id="addThemeModal"
		@close="navigationStore.setModal(false)">
		<div class="modal__content">
			<h2>Thema toevoegen</h2>
			<div v-if="success !== null || error">
				<NcNoteCard v-if="success" type="success">
					<p>Thema succesvol toegevoegd</p>
				</NcNoteCard>
				<NcNoteCard v-if="!success" type="error">
					<p>Er is iets fout gegaan bij het toevoegen van Thema</p>
				</NcNoteCard>
				<NcNoteCard v-if="error" type="error">
					<p>{{ error }}</p>
				</NcNoteCard>
			</div>
			<div class="formContainer">
				<div v-if="success === null" class="form-group">
					<NcTextField
						:disabled="loading"
						label="Titel"
						:value.sync="theme.title" />
					<NcTextField
						:disabled="loading"
						label="Samenvatting"
						:value.sync="theme.summary" />
					<NcTextArea
						:disabled="loading"
						label="Beschrijving"
						:value.sync="theme.description" />
				</div>
			</div>
			<NcButton
				v-if="success === null"
				:disabled="!theme.title || loading"
				type="primary"
				@click="addTheme()">
				<template #icon>
					<NcLoadingIcon v-if="loading" :size="20" />
					<Plus v-if="!loading" :size="20" />
				</template>
				Toevoegen
			</NcButton>
		</div>
	</NcModal>
</template>

<script>
import {
	NcButton,
	NcLoadingIcon,
	NcModal,
	NcNoteCard,
	NcTextArea,
	NcTextField,
} from '@nextcloud/vue'
import Plus from 'vue-material-design-icons/Plus.vue'

export default {
	name: 'AddThemeModal',
	components: {
		NcModal,
		NcTextField,
		NcTextArea,
		NcButton,
		NcLoadingIcon,
		NcNoteCard,
		// Icons
		Plus,
	},
	data() {
		return {
			theme: {
				title: '',
				summary: '',
				description: '',
			},

			errorCode: '',
			themeLoading: false,
			hasUpdated: false,
			loading: false,
			success: null,
			error: false,
		}
	},
	updated() {
		if (navigationStore.modal === 'themeAdd' && !this.hasUpdated) {
			this.hasUpdated = true
		}
	},
	methods: {
		isJsonString(str) {
			try {
				JSON.parse(str)
			} catch (e) {
				return false
			}
			return true
		},
		addTheme() {
			this.loading = true
			this.error = false
			fetch('/index.php/apps/opencatalogi/api/themes', {
				method: 'POST',
				headers: {
					'Content-Type': 'application/json',
				},
				body: JSON.stringify({
					...this.theme,
				}),
			})
				.then((response) => {
					this.loading = false
					this.success = response.ok
					// Lets refresh the themeList
					themeStore.refreshThemeList()
					response.json().then((data) => {
						themeStore.setThemeList(data)
					})
					navigationStore.setSelected('themes')
					// Wait for the user to read the feedback then close the model
					const self = this
					setTimeout(function() {
						self.success = null
						navigationStore.setModal(false)
						self.theme = {
							title: '',
							summary: '',
							description: '',
							oin: '',
							tooi: '',
							rsin: '',
							pki: '',
						}
						self.hasUpdated = false
					}, 2000)
				})
				.catch((err) => {
					this.error = err
					this.loading = false
					self.hasUpdated = false
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

.formContainer > * {
  margin-block-end: 10px;
}

.selectGrid {
  display: grid;
  grid-gap: 5px;
  grid-template-columns: 1fr 1fr;
}

.zaakDetailsContainer {
  margin-block-start: var(--OC-margin-20);
  margin-inline-start: var(--OC-margin-20);
  margin-inline-end: var(--OC-margin-20);
}

.success {
  color: green;
}

.APM-horizontal {
  display: flex;
  gap: 4px;
  flex-direction: row;
  align-items: center;
}
</style>
