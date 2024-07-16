<script setup>
import { store } from '../../store.js'
</script>
<template>
	<NcModal
		v-if="store.modal === 'addPublicationDataModal'"
		ref="modalRef"
		@close="store.setModal(false)">
		<div class="modal__content">
			<h2>Publicatie eigenschap toevoegen</h2>

			<div v-if="success === -1" class="form-group">
				<NcTextField :disabled="loading"
					label="Naam"
					required
					:value.sync="dataName"
					:loading="loading" />

				<NcTextField :disabled="loading"
					label="Data"
					:value.sync="data"
					:loading="loading" />
			</div>

			<NcButton v-if="success === -1"
				:loading="loading"
				:disabled="loading"
				type="primary"
				@click="AddPublicatie()">
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
					<p>Succesvol publicatie eigenschap toegevoegd</p>
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
	NcNoteCard,
	NcLoadingIcon,
} from '@nextcloud/vue'

// icons
import Plus from 'vue-material-design-icons/Plus.vue'

export default {
	name: 'AddPublicationDataModal',
	components: {
		NcModal,
		NcTextField,
		NcButton,
		NcNoteCard,
		NcLoadingIcon,
	},
	data() {
		return {
			dataName: '',
			data: '',
			loading: false,
			success: -1,
			successMessage: '',
			hasUpdated: false,
		}
	},
	updated() {
		if (store.modal === 'addPublicationDataModal' && !this.hasUpdated) {
			this.dataName = ''
			this.data = ''
			this.hasUpdated = true
		}
	},
	methods: {
		closeModal() {
			store.modal = false
			this.success = -1
		},
		AddPublicatie() {
			const publication = store.publicationItem
			publication.data.data[this.dataName] = this.data

			this.loading = true
			fetch(
				`/index.php/apps/opencatalogi/api/publications/${publication.id}`,
				{
					method: 'PUT',
					headers: {
						'Content-Type': 'application/json',
					},
					body: JSON.stringify(publication),
				},
			)
				.then((response) => {
					this.loading = false
					this.success = 1
					this.hasUpdated = false
					setTimeout(() => {
						this.closeModal()
					    this.success = -1
					}, 3000)
				})
				.catch((err) => {
					this.loading = false
					this.success = 0
					this.hasUpdated = false
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
