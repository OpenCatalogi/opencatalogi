<script setup>
import { store } from '../../store.js'
</script>
<template>
	<NcModal
		v-if="store.modal === 'deletePublication'"
		ref="modalRef"
		@close="store.setModal(false)">
		<div v-if="success === -1" class="modal__content">
			<h2>Verwijder publicatie:</h2>

			<div class="deletePublication-info">
				<h3>{{ publication.title }}</h3>
				<span>{{ publication.description }}</span>
			</div>

			<div v-if="loading">
				<NcLoadingIcon :size="100" />
			</div>

			<div class="deletePublication-warnings">
				<p>Weet u zeker dat u de publicatie definitief wilt verwijderen?</p>
				<p>Let Op: deze actie is onomkeerbaar.</p>
			</div>

			<div class="deletePublication-buttons">
				<NcButton type="error" :disabled="loading" @click="deletePublication(store.publicationItem.id)">
					Delete!
				</NcButton>
				<NcButton type="secondary" :disabled="loading" @click="store.setModal(false)">
					Cancel
				</NcButton>
			</div>
		</div>
		<NcLoadingIcon
			v-if="loading"
			:size="100" />
	</NcModal>
</template>

<script>
import {
	NcButton,
	NcModal,
	NcLoadingIcon,
} from '@nextcloud/vue'

export default {
	name: 'DeletePublicationModal',
	components: {
		NcModal,
		NcButton,
		NcLoadingIcon,
	},
	data() {
		return {
			publication: [],
			hasUpdated: false,
			loading: false,
			success: -1,
		}
	},
	updated() {
		if (store.modal === 'deletePublication' && this.hasUpdated) {
			if (this.publication === store.publicationItem) return
			this.hasUpdated = false
		}
		if (store.modal === 'deletePublication' && !this.hasUpdated) {
			this.publication = store.publicationItem
			this.hasUpdated = true

			// reset state
			this.loading = false
			this.success = -1
		}
	},
	methods: {
		closeModal() {
			store.modal = false
		},
		deletePublication(id) {
			this.loading = true
			fetch(
				`/index.php/apps/opencatalogi/api/publications/${id}`,
				{
					method: 'DELETE',
				},
			)
				.then((response) => {
					if (response.ok === true) this.success = 1
					else this.success = 0

					setTimeout(() => this.closeModal(), 3000)
				})
				.catch((err) => {
					this.loading = false
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

.deletePublication-info {
    margin-block: 1rem 2rem;
}
.deletePublication-info > h3 {
    margin: 0
}

.deletePublication-warnings {
    margin-block: 1rem;
}
.deletePublication-warnings > * {
    font-weight: bold;
}

.deletePublication-buttons {
    display: flex;
}
</style>
