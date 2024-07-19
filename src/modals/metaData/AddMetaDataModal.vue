<script setup>
import { store } from '../../store.js'
</script>

<template>
	<NcModal
		v-if="store.modal === 'addMetaData'"
		ref="modalRef"
		@close="store.setModal(false)">
		<div class="modal__content">
			<h2>MetaData toevoegen</h2>
			<NcNoteCard v-if="succes" type="success">
				<p>Meta data succesvol toegevoegd</p>
			</NcNoteCard>
			<NcNoteCard v-if="error" type="error">
				<p>{{ error }}</p>
			</NcNoteCard>
			<div v-if="!succes" class="form-group">
				<NcTextField label="Titel" :value.sync="metaData.title" required="true" />
				<NcTextField label="Versie" :value.sync="metaData.version" />
				<NcTextField label="Samenvatting" :disabled="loading" :value.sync="metaData.summery" />
				<NcTextArea label="Beschrijving" :disabled="loading" :value.sync="metaData.description" />
			</div>
			<NcButton
				v-if="!succes"
				:disabled="!metaData.title || loading"
				type="primary"
				@click="addMetaData">
				<template #icon>
					<NcLoadingIcon v-if="loading" :size="20" />
					<ContentSaveOutline v-if="!loading" :size="20" />
				</template>
				Toevoegen
			</NcButton>
		</div>
	</NcModal>
</template>

<script>
import { NcButton, NcModal, NcTextField, NcTextArea, NcLoadingIcon, NcNoteCard } from '@nextcloud/vue'
import ContentSaveOutline from 'vue-material-design-icons/ContentSaveOutline.vue'

export default {
	name: 'AddMetaDataModal',
	components: {
		NcModal,
		NcTextField,
		NcTextArea,
		NcButton,
		NcLoadingIcon,
		NcNoteCard,
		// Icons
		ContentSaveOutline,
	},
	data() {
		return {
			metaData: {
				title: '',
				version: '',
				summery: '',
				description: '',
			},
			metaDataList: [],
			loading: false,
			succes: false,
			error: false,
		}
	},
	methods: {
		addMetaData() {
			this.loading = true
			fetch(
				'/index.php/apps/opencatalogi/api/metadata',
				{
					method: 'POST',
					headers: {
						'Content-Type': 'application/json',
					},
					body: JSON.stringify(this.metaData),
				},
			)
				.then((response) => {
					// Set the form
					this.loading = false
					this.succes = true
					// Lets refresh the catalogiList
					store.refreshMetaDataList()
					response.json().then((data) => {
						store.setMetaDataItem(data)
					})
					store.setSelected('metaData')
					// Update the list
					const self = this
					setTimeout(function() {
						self.succes = false
						this.metaData = { title: '', version: '', summery: '', description: '' }
						store.setModal(false)
					}, 2000)
				})
				.catch((err) => {
					this.metaDataLoading = false
					this.error = err
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

.zaakDetailsContainer {
    margin-block-start: var(--OC-margin-20);
    margin-inline-start: var(--OC-margin-20);
    margin-inline-end: var(--OC-margin-20);
}

.success {
    color: green;
}
</style>
