<script setup>
import { navigationStore, catalogiStore, metadataStore } from '../../store/store.js'
</script>

<template>
	<NcDialog
		v-if="navigationStore.dialog === 'deleteCatalogiMetadata'"
		name="Catalogi Metadata verwijderen"
		:can-close="false">
		<p v-if="!succes">
			Wil je <b>{{ metadataStore.metaDataItem?.title }}</b> verwijderen van <b>{{ catalogiStore.catalogiItem?.title }}</b>?
		</p>
		<NcNoteCard v-if="succes" type="success">
			<p>Metadata succesvol verwijderd</p>
		</NcNoteCard>
		<NcNoteCard v-if="error" type="error">
			<p>{{ error }}</p>
		</NcNoteCard>
		<template #actions>
			<NcButton :disabled="loading" icon="" @click="navigationStore.setDialog(false)">
				<template #icon>
					<Cancel :size="20" />
				</template>
				{{ succes ? 'Sluiten' : 'Annuleer' }}
			</NcButton>
			<NcButton
				v-if="!succes"
				:disabled="loading"
				icon="Delete"
				type="error"
				@click="DeleteCatalogiMetadata()">
				<template #icon>
					<NcLoadingIcon v-if="loading" :size="20" />
					<Delete v-if="!loading" :size="20" />
				</template>
				Verwijderen
			</NcButton>
		</template>
	</NcDialog>
</template>

<script>
import { NcButton, NcDialog, NcNoteCard, NcLoadingIcon } from '@nextcloud/vue'

import Cancel from 'vue-material-design-icons/Cancel.vue'
import Delete from 'vue-material-design-icons/Delete.vue'

export default {
	name: 'DeleteCatalogiMetadata',
	components: {
		NcDialog,
		NcButton,
		NcNoteCard,
		NcLoadingIcon,
		// Icons
		Cancel,
		Delete,
	},
	data() {
		return {

			loading: false,
			succes: false,
			error: false,
		}
	},
	methods: {
		DeleteCatalogiMetadata() {
			const metadataArray = catalogiStore.catalogiItem?.metadata
			    .filter((metaId) => metaId.toString() !== metadataStore.metaDataItem?.id.toString())

			this.loading = true
			fetch(
				`/index.php/apps/opencatalogi/api/catalogi/${catalogiStore.catalogiItem.id}`,
				{
					method: 'PUT',
					headers: {
						'Content-Type': 'application/json',
					},
					body: JSON.stringify({
						...catalogiStore.catalogiItem,
						metadata: metadataArray,
					}),
				},
			)
				.then((response) => {
					this.loading = false
					this.succes = true
					// Lets refresh the catalogiList
					catalogiStore.refreshCatalogiList()
					response.json().then((data) => {
						catalogiStore.setCatalogiItem(data)
					})
					navigationStore.setSelected('catalogi')
					// Wait for the user to read the feedback then close the model
					const self = this
					setTimeout(function() {
						self.succes = false
						navigationStore.setDialog(false)
					}, 2000)
				})
				.catch((err) => {
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
