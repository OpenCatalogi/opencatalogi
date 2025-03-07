<script setup>
import { navigationStore, directoryStore, publicationTypeStore } from '../../store/store.js'
</script>

<template>
	<NcModal v-if="navigationStore.modal === 'editListing'"
		ref="modalRef"
		label-id="editDirectoryModal"
		@close="navigationStore.setModal(false)">
		<div class="modal__content">
			<h2>Directory bewerken</h2>
			<div v-if="success !== null || error">
				<NcNoteCard v-if="success" type="success">
					<p>Directory succesvol bewerkt</p>
				</NcNoteCard>
				<NcNoteCard v-if="!success" type="error">
					<p>Er is iets fout gegaan bij het bewerken van Directory</p>
				</NcNoteCard>
				<NcNoteCard v-if="error" type="error">
					<p>{{ error }}</p>
				</NcNoteCard>
			</div>
			<div v-if="success === null" class="form-group">
				<NcTextField label="Url" :value.sync="directoryStore.listingItem.url" />
				<NcTextField label="Status" :value.sync="directoryStore.listingItem.status" />
				<NcTextField label="Last synchronized" :value.sync="directoryStore.listingItem.lastSync" />
			</div>
			<NcButton v-if="success === null"
				type="primary"
				@click="editDirectory()">
				<template #icon>
					<NcLoadingIcon v-if="loading" :size="20" />
					<ContentSaveOutline v-if="!loading" :size="20" />
				</template>
				Bewerken
			</NcButton>
		</div>
	</NcModal>
</template>

<script>
import { NcButton, NcModal, NcTextField, NcLoadingIcon, NcNoteCard } from '@nextcloud/vue'
import ContentSaveOutline from 'vue-material-design-icons/ContentSaveOutline.vue'

export default {
	name: 'EditListingModal',
	components: {
		NcModal,
		NcTextField,
		NcButton,
		NcLoadingIcon,
		NcNoteCard,
		// Icons
		ContentSaveOutline,
	},
	data() {
		return {
			loading: false,
			success: null,
			error: false,
		}
	},
	methods: {
		fetchData(id) {
			this.loading = true
			fetch(
				`/index.php/apps/opencatalogi/api/directory/${directoryStore.listingItem.id}`,
				{
					method: 'GET',
				},
			)
				.then((response) => {
					response.json().then((data) => {
						this.listing = data
						this.loading = false
					})
				})
				.catch((err) => {
					this.error = err
					this.loading = false
				})
		},
		editDirectory() {
			this.loading = true
			fetch(
				`/index.php/apps/opencatalogi/api/directory/${directoryStore.listingItem.id}`,
				{
					method: 'PUT',
					headers: {
						'Content-Type': 'application/json',
					},
					body: JSON.stringify(this.directory),
				},
			).then((response) => {
				// Set propper modal states
				this.loading = false
				this.success = response.ok
				// Let's refresh the catalogiList
				publicationTypeStore.refreshPublicationTypeList()
				response.json().then((data) => {
					this.setListingItem(data)
				})
				navigationStore.setSelected('directory')
				// Wait and then close the modal
				const self = this
				setTimeout(() => {
					self.success = null
					self.closeModal()
				}, 2500)
			}).catch((err) => {
				this.error = err
				this.loading = false
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
