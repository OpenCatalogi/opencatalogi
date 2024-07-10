<script setup>
import { store } from '../../store.js'
</script>
<template>
	<NcModal
		v-if="store.modal === 'deletePublication'"
		ref="modalRef"
		@close="store.setModal(false)">
		<div v-if="!loading" class="modal__content">
			<h2>Verwijder publicatie:</h2>

			<div class="deletePublication-info">
				<h3>{{ publication.title }}</h3>
				<span>{{ publication.description }}</span>
			</div>

			<div class="deletePublication-warnings">
				<p>Weet u zeker dat u de publicatie definitief wilt verwijderen?</p>
				<p>Let Op: deze actie is onomkeerbaar.</p>
			</div>

			<div class="deletePublication-buttons">
				<NcButton type="error" @click="deletePublication(store.publicationId)">
					Delete!
				</NcButton>
				<NcButton type="secondary" @click="() => store.modal = false">
					Cancel
				</NcButton>
			</div>
		</div>
	</NcModal>
</template>

<script>
import {
	NcButton,
	NcModal,
} from '@nextcloud/vue'

export default {
	name: 'DeletePublicationModal',
	components: {
		NcModal,
		NcButton,
	},
	data() {
		return {
			publication: [],
			hasUpdated: false,
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
					this.closeModal()
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
