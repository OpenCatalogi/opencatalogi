<script setup>
import { store } from '../../store.js'
</script>

<template>
	<NcModal v-if="store.modal === 'editListing'" ref="modalRef" @close="store.setModal(false)">
		<div class="modal__content">
			<h2>Directory bewerken</h2>
			<NcNoteCard v-if="succes" type="success">
				<p>Meta data succesvol toegevoegd</p>
			</NcNoteCard>
			<NcNoteCard v-if="error" type="error">
				<p>{{ error }}</p>
			</NcNoteCard>
			<div v-if="!succes" class="form-group">
				<NcTextField label="Url" :value.sync="store.listingItem.url" />
				<NcTextField label="Status" :value.sync="store.listingItem.status" />
				<NcTextField label="Last synchronized" :value.sync="store.listingItem.lastSync" />
			</div>
			<NcButton v-if="!succes" type="primary" @click="editDirectory()">
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
			succes: false,
			error: false,
		}
	},
	methods: {
		closeModal() {
			store.modal = false
		},
		fetchData(id) {
			this.loading = true
			fetch(
				`/index.php/apps/opencatalogi/api/directory/${store.listingItem.id}`,
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
				`/index.php/apps/opencatalogi/api/directory/${store.listingItem.id}`,
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
				this.succes = true
				// Lets refresh the catalogiList
				store.refreshMetaDataList()
				response.json().then((data) => {
					this.setListingItem(data)
				})
				store.setSelected('directory')
				// Wait and then close the modal
				setTimeout(() => (this.closeModal()), 2500)
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
