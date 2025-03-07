<script setup>
import { navigationStore, themeStore } from '../../store/store.js'
</script>

<template>
	<NcDialog
		v-if="navigationStore.dialog === 'copyTheme'"
		name="Thema kopieren"
		:can-close="false">
		<p v-if="!succes">
			Wil je <b>{{ themeStore.themeItem.name ?? themeStore.themeItem.title }}</b> kopiëren?
		</p>
		<NcNoteCard v-if="succes" type="success">
			<p>Thema succesvol gekopieerd</p>
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
				type="primary"
				@click="CopyTheme()">
				<template #icon>
					<NcLoadingIcon v-if="loading" :size="20" />
					<ContentCopy v-if="!loading" :size="20" />
				</template>
				Kopiëren
			</NcButton>
		</template>
	</NcDialog>
</template>

<script>
import { NcButton, NcDialog, NcNoteCard, NcLoadingIcon } from '@nextcloud/vue'

import Cancel from 'vue-material-design-icons/Cancel.vue'
import ContentCopy from 'vue-material-design-icons/ContentCopy.vue'

export default {
	name: 'CopyThemeDialog',
	components: {
		NcDialog,
		NcButton,
		NcNoteCard,
		NcLoadingIcon,
		// Icons
		Cancel,
		ContentCopy,
	},
	data() {
		return {

			loading: false,
			succes: false,
			error: false,
		}
	},
	methods: {
		CopyTheme() {
			this.loading = true
			themeStore.themeItem.title = 'KOPIE: ' + themeStore.themeItem.title
			delete themeStore.themeItem.id
			delete themeStore.themeItem._id
			fetch(
				'/index.php/apps/opencatalogi/api/objects/theme',
				{
					method: 'POST',
					headers: {
						'Content-Type': 'application/json',
					},
					body: JSON.stringify(themeStore.themeItem),
				},
			)
				.then((response) => {
					this.loading = false
					this.succes = true
					// Let's refresh the catalogiList
					themeStore.refreshThemeList()
					response.json().then((data) => {
						themeStore.setThemeItem(data)
					})
					navigationStore.setSelected('themes')
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
