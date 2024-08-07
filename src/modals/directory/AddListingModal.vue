<script setup>
import { navigationStore, directoryStore } from '../../store/store.js'
</script>

<template>
	<NcModal
		v-if="navigationStore.modal === 'addListing'"
		ref="modalRef"
		label-id="addListingModal"
		@close="navigationStore.setModal(false)">
		<div class="modal__content">
			<h2>Directory toevoegen</h2>
			<div v-if="success !== null || error">
				<NcNoteCard v-if="success" type="success">
					<p>Listing succesvol toegevoegd</p>
				</NcNoteCard>
				<NcNoteCard v-if="!success" type="error">
					<p>Er is iets fout gegaan bij het toevoegen van Listing</p>
				</NcNoteCard>
				<NcNoteCard v-if="error && !success" type="error">
					<p>{{ error }}</p>
				</NcNoteCard>
			</div>
			<div v-if="success === null" class="form-group">
				<NcNoteCard v-if="validateUrlError" type="error">
					<p>Er is geen valide URL ingevoerd.</p>
				</NcNoteCard>
				<NcTextField v-model="directory.directory" label="Url" @input="validateUrl" />
			</div>
			<NcButton
				v-if="success === null"
				:disabled="!isUrlValid || loading || !directory.directory"
				type="primary"
				@click="addDirectory">
				<template #icon>
					<NcLoadingIcon v-if="loading" :size="20" />
					<ContentSaveOutline v-if="!loading" :size="20" />
				</template>
				Submit
			</NcButton>
		</div>
	</NcModal>
</template>

<script>
import { NcButton, NcModal, NcTextField, NcLoadingIcon, NcNoteCard } from '@nextcloud/vue'
import ContentSaveOutline from 'vue-material-design-icons/ContentSaveOutline.vue'

export default {
	name: 'AddListingModal',
	components: {
		NcModal,
		NcTextField,
		NcButton,
		NcLoadingIcon,
		NcNoteCard,
		ContentSaveOutline,
	},
	data() {
		return {
			directory: {
				directory: '',
			},
			loading: false,
			success: null,
			error: false,
			validateUrlError: null,
			// eslint-disable-next-line no-useless-escape
			urlPattern: /^https?:\/\/(?:www\.)?[-a-zA-Z0-9@:%._\+~#=]{1,256}\.[a-zA-Z0-9()]{1,6}\b(?:[-a-zA-Z0-9()@:%_\+.~#?&\/=]*)$/,
		}
	},
	computed: {
		isUrlValid() {
			return this.urlPattern.test(this.directory.directory)
		},
	},
	methods: {
		addDirectory() {
			this.loading = true
			this.$emit('metadata', this.title)
			fetch('/index.php/apps/opencatalogi/api/directory', {
				method: 'POST',
				headers: {
					'Content-Type': 'application/json',
				},
				body: JSON.stringify(this.directory),
			})
				.then((response) => {
					this.loading = false
					this.success = response.ok
					directoryStore.refreshListingList()
					response.json().then((data) => {
						directoryStore.setListingItem(data)
					})
					navigationStore.setSelected('directory')
					setTimeout(() => {
						this.success = null
						this.closeModal()
					}, 2500)

					this.directory = {
						directory: '',
					}
				})
				.catch((err) => {
					this.error = err
					this.loading = false
				})
		},
		closeModal() {
			navigationStore.setModal(false)
		},
		validateUrl(event) {
			this.directory.directory = event.target.value
			if (!this.isUrlValid) {
				this.validateUrlError = 'Er is geen valide URL ingevoerd.'
			} else {
				this.validateUrlError = null
			}
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
