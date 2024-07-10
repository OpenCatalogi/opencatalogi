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
			<div v-if="!publicationLoading">
				<h3>{{ publication.title }}</h3>
				<span>{{ publication.description }}</span>

				<h4>Weet je zeker dat je dit wilt verwijderen?</h4>

				<div v-if="succesMessage" class="success">
					Successfully deleted publication
				</div>
			</div>
			<NcLoadingIcon
				v-if="publicationLoading"
				:size="100"
				appearance="dark"
				name="Publicatie details aan het laden" />

			<div class="deletePublication-buttons">
				<NcButton type="primary" @click="deletePublication(store.publicationId)">
					Delete!
				</NcButton>
				<NcButton type="secondary" @click="() => store.modal = false">
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
			loading: false,
			succesMessage: false,
			catalogiLoading: false,
			metaDataLoading: false,
			hasUpdated: false,
			publicationLoading: false,
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

.deletePublication-buttons {
    display: flex;
}
</style>
