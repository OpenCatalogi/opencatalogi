<script setup>
import { store } from '../../store/store.js'
</script>
<template>
	<NcModal
		v-if="store.modal === 'addPublicationData'"
		ref="modalRef"
		@close="store.setModal(false)">
		<div class="modal__content">
			<h2>Publicatie eigenschap toevoegen</h2>
			<NcNoteCard v-if="succes" type="success">
				<p>Publicatie eigenschap bewerkt</p>
			</NcNoteCard>
			<NcNoteCard v-if="error" type="error">
				<p>{{ error }}</p>
			</NcNoteCard>
			<div v-if="!succes" class="form-group">
				<NcTextField :disabled="loading"
					label="Naam"
					required
					:value.sync="key"
					:loading="loading" />

				<NcTextField :disabled="loading"
					label="Data"
					:value.sync="value"
					:loading="loading" />
			</div>

			<NcButton v-if="!succes"
				:disabled="loading || !key || !value"
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
				@click="store.setModal(false)">
				{{ succes ? 'Sluiten' : 'Annuleer' }}
			</NcButton>
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
			key: '',
			value: '',
			loading: false,
			succes: false,
			error: false,
		}
	},
	methods: {
		AddPublicatieEigenschap() {
			store.publicationItem.data[this.key] = this.value
			this.loading = true
			fetch(
				`/index.php/apps/opencatalogi/api/publications/${store.publicationItem.id}`,
				{
					method: 'PUT',
					headers: {
						'Content-Type': 'application/json',
					},
					body: JSON.stringify(store.publicationItem),
				},
			)
				.then((response) => {
					this.loading = false
					this.succes = true
					// Lets refresh the catalogiList
					response.json().then((data) => {
						store.setPublicationItem(data)
					})
					// Wait for the user to read the feedback then close the model
					const self = this
					setTimeout(function() {
						self.succes = false
						store.setModal(false)
					}, 2000)
				})
				.catch((err) => {
					this.loading = false
					this.error = err
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
