<script setup>
import { navigationStore, directoryStore } from '../../store/store.js'
</script>

<template>
	<NcModal v-if="navigationStore.modal === 'addListing'" ref="modalRef" @close="closeModal">
		<div class="modal__content">
			<h2>Directory toevoegen</h2>
			<div v-if="success !== null || error">
				<NcNoteCard v-if="success" type="success">
					<p>Listing succesvol toegevoegd</p>
				</NcNoteCard>
				<NcNoteCard v-if="!success" type="error">
					<p>Er is iets fout gegaan bij het toevoegen van Listing</p>
				</NcNoteCard>
				<NcNoteCard v-if="error" type="error">
					<p>{{ error }}</p>
				</NcNoteCard>
			</div>
			<div v-if="success === null" class="form-group">
				<NcTextField label="Title" :value.sync="listing.title" required />
				<NcTextField label="Summary" :value.sync="listing.summary" required />

				<NcSelect v-bind="metaData"
					v-model="metaData.value"
					input-label="MetaData"
					:loading="metaDataLoading"
					:disabled="publicationLoading"
					required />

				<NcTextField label="Description" :value.sync="listing.description" />
				<NcTextField label="Search (url)" :value.sync="listing.search" />
				<NcTextField label="Directory (url)" :value.sync="listing.directory" />
				<NcTextField label="Default" :value.sync="listing.default" />
				<NcTextField label="Available" :value.sync="listing.available" />
			</div>
			<NcButton v-if="success === null"
				:disabled="!listing.title || !listing.summary"
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
import { NcButton, NcModal, NcTextField, NcSelect, NcLoadingIcon, NcNoteCard } from '@nextcloud/vue'
import ContentSaveOutline from 'vue-material-design-icons/ContentSaveOutline.vue'

export default {
	name: 'AddListingModal',
	components: {
		NcModal,
		NcTextField,
		NcSelect,
		NcButton,
		NcLoadingIcon,
		NcNoteCard,
		// Icons
		ContentSaveOutline,
	},
	data() {
		return {
			listing: {
				title: '',
				summary: '',
				description: '',
				search: '',
				directory: '',
				default: '',
				available: '',
			},
			metaData: {},
			metaDataLoading: false,
			loading: false,
			success: null,
			error: false,
		}
	},
	updated() {
		if (navigationStore.modal === 'addListing' && !this.hasUpdated) {
			this.fetchMetaData()
			this.hasUpdated = true
		}
	},
	methods: {
		fetchMetaData() {
			this.metaDataLoading = true
			fetch('/index.php/apps/opencatalogi/api/metadata', {
				method: 'GET',
			})
				.then((response) => {
					response.json().then((data) => {

						this.metaData = {
							options: Object.entries(data.results).map((metaData) => ({
								id: metaData[1].id ?? metaData[1]._id,
								label: metaData[1].title ?? metaData[1].name,
							})),

						}
					})
					this.metaDataLoading = false
				})
				.catch((err) => {
					console.error(err)
					this.metaDataLoading = false
				})
		},
		addDirectory() {
			this.loading = true
			this.$emit('metadata', this.title)
			fetch(
				'/index.php/apps/opencatalogi/api/directory',
				{
					method: 'POST',
					headers: {
						'Content-Type': 'application/json',
					},
					body: JSON.stringify({
						...this.listing,
						metaData: this.metaData.value.id,
					}),
				},
			)
				.then((response) => {
					// Set propper modal states
					this.loading = false
					this.success = response.ok
					// Lets refresh the catalogiList
					directoryStore.refreshListingList()
					response.json().then((data) => {
						directoryStore.setListingItem(data)
					})
					navigationStore.setSelected('directory')
					// Wait and then close the modal
					const self = this
					setTimeout(() => {
						self.success = null
						self.closeModal()
					}, 2000)
				})
				.catch((err) => {
					this.error = err
					this.loading = false
				})
		},
		closeModal() {
			this.listing = {
				title: '',
				summary: '',
				description: '',
				search: '',
				directory: '',
				default: '',
				available: '',
			}
			navigationStore.setModal(false)
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
