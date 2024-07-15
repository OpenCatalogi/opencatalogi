<script setup>
import { store } from '../../store.js'
</script>

<template>
	<NcModal
		v-if="store.modal === 'editMetaData'"
		ref="modalRef"
		@close="store.setModal(false)">
		<div v-if="!succes" class="modal__content">
			<h2>MetaData bewerken</h2>
			<NcNoteCard v-if="succes" type="success">
				<p>Meta data succesvol gewijzigd</p>
			</NcNoteCard>
			<NcNoteCard v-if="error" type="error">
				<p>{{ error }}</p>
			</NcNoteCard>
			<div class="form-group">
				<NcTextField label="Titel" :disabled="loading" :value.sync="metaData.title" />
			</div>
			<div class="form-group">
				<NcTextField label="Versie" :disabled="loading" :value.sync="metaData.version" />
			</div>
			<div class="form-group">
				<NcTextArea label="Beschrijving" :disabled="loading" :value.sync="metaData.description" />
			</div>
			<NcButton :disabled="!metaData.title || loading" type="primary" @click="editMetaData">
				<template #icon>
					<NcLoadingIcon v-if="loading" :size="20" />
					<Pencil v-if="!loading" :size="20" />
				</template>
				Opslaan
			</NcButton>
		</div>
	</NcModal>
</template>

<script>
import { NcButton, NcModal, NcTextField, NcTextArea, NcLoadingIcon, NcNoteCard } from '@nextcloud/vue'
import Pencil from 'vue-material-design-icons/Pencil.vue'

export default {
	name: 'EditMetaDataModal',
	components: {
		NcModal,
		NcTextField,
		NcTextArea,
		NcButton,
		NcLoadingIcon,
		NcNoteCard,
		// Icons
		Pencil,
	},
	data() {
		return {
			metaData: {
				title: '',
				version: '',
				description: '',
			},
			hasUpdated: false,
			loading: false,
			succes: false,
			error: false,
		}
	},
	updated() {
		if (store.modal === 'editMetaData' && this.hasUpdated) {
			if (this.metaData._id === store.metaDataItem?.id) return
			this.hasUpdated = false
		}
		if (store.modal === 'editMetaData' && !this.hasUpdated) {
			this.fetchData(store.metaDataItem?.id)
			this.hasUpdated = true
		}
	},
	mounted() {
		this.metaData = store.metaDataItem
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
						this.metaData = data
					})
					this.loading = false
				})
				.catch((err) => {
					this.error = err
					this.loading = false
				})
		},
		closeModal() {
			store.modal = false
		},
		editMetaData() {
			this.loading = true
			fetch(
				`/index.php/apps/opencatalogi/api/metadata/${store.metaDataItem?._id}`,
				{
					method: 'PUT',
					headers: {
						'Content-Type': 'application/json',
					},
					body: JSON.stringify(this.metaData),
				},
			).then((response) => {
				this.loading = false
				this.succes = true
				// Lets refresh the catalogiList
				store.refreshMetaDataList()
				response.json().then((data) => {
					store.setMetaDataItem(data)
				})
				store.setSelected('metaData')
				setTimeout(() => (this.closeModal()), 2500)
				// Reset the form the form
				this.succes = false
				this.metaData = { title: '', version: '', description: '' }
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
