<script setup>
import { publicationStore, navigationStore, themeStore } from '../../store/store.js'
</script>

<template>
	<NcModal v-if="navigationStore.modal === 'addPublicationTheme'"
		ref="modalRef"
		label-id="addPublicationTheme"
		@close="closeModal">
		<div class="modal__content">
			<h2>Thema toevoegen aan {{ publicationStore.publicationItem.title }}</h2>

			<div v-if="success !== null || error">
				<NcNoteCard v-if="success" type="success">
					<p>Thema succesvol toegevoegd</p>
				</NcNoteCard>
				<NcNoteCard v-if="!success" type="error">
					<p>Er is iets fout gegaan bij het toevoegen van een thema</p>
				</NcNoteCard>
				<NcNoteCard v-if="error" type="error">
					<p>{{ error }}</p>
				</NcNoteCard>
			</div>

			<div v-if="success === null" class="form-group">
				<NcSelect v-bind="themes"
					v-model="themes.value"
					input-label="Thema"
					:loading="themeLoading || loading"
					required>
					<!-- eslint-disable-next-line vue/no-unused-vars vue/no-template-shadow  -->
					<template #no-options="{ search, searching, loading }">
						<p v-if="loading">
							Loading...
						</p>
						<p v-if="!loading && !themes.options.length">
							Er zijn geen thema's beschikbaar
						</p>
					</template>
					<!-- eslint-disable-next-line vue/no-unused-vars  -->
					<template #option="{ id, label, summary }">
						<div class="theme-option">
							<ShapeOutline :size="25" />
							<span>
								<h6 style="margin: 0">
									{{ label }}
								</h6>
								{{ summary }}
							</span>
						</div>
					</template>
				</NcSelect>
			</div>

			<NcButton v-if="success === null"
				:disabled="!themes?.value || loading"
				type="primary"
				@click="addPublicationTheme">
				<template #icon>
					<NcLoadingIcon v-if="loading" :size="20" />
					<Plus v-if="!loading" :size="20" />
				</template>
				Toevoegen
			</NcButton>
		</div>
	</NcModal>
</template>

<script>
import { NcButton, NcModal, NcLoadingIcon, NcNoteCard, NcSelect } from '@nextcloud/vue'
import ShapeOutline from 'vue-material-design-icons/ShapeOutline.vue'
import Plus from 'vue-material-design-icons/Plus.vue'

import { Publication } from '../../entities/index.js'

export default {
	name: 'AddPublicationThemeModal',
	components: {
		NcModal,
		NcButton,
		NcLoadingIcon,
		NcNoteCard,
		NcSelect,
		// Icons
		Plus,
	},
	data() {
		return {
			themes: {},
			themeLoading: false,
			loading: false,
			success: null,
			error: false,
			errorCode: '',
			hasUpdated: false,
		}
	},
	updated() {
		if (navigationStore.modal === 'addPublicationTheme' && !this.hasUpdated) {
			this.fetchThemes()
			this.hasUpdated = true
		}
	},
	methods: {
		closeModal() {
			navigationStore.setModal(false)
			this.success = null
			this.hasUpdated = false
			this.themes.value = null
		},
		async fetchThemes() {
			this.themeLoading = true

			themeStore.refreshThemeList()
				.then(({ data }) => {
					const filteredData = data.filter((theme) =>
						!publicationStore.publicationItem.themes.includes(theme?.id),
					)

					this.themes = {
						options: filteredData.map((theme) => ({
							id: theme.id,
							summary: theme.summary,
							label: theme.title,
						})),
					}
				})
				.catch((err) => {
					console.error(err)
				})
				.finally(() => {
					this.themeLoading = false
				})
		},
		addPublicationTheme() {
			this.loading = true
			this.error = false

			const publicationClone = { ...publicationStore.publicationItem }
			publicationClone.themes.push(this.themes.value.id)

			const newPublicationItem = new Publication({
				...publicationClone,
			})

			publicationStore.editPublication(newPublicationItem)
				.then(({ response }) => {
					this.loading = false
					this.success = response.ok

					setTimeout(this.closeModal, 2000)
				})
				.catch((err) => {
					this.error = err
				})
				.finally(() => {
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

<style scoped>
.theme-option {
    display: flex;
    align-items: center;
    gap: 10px;
}
.theme-option > .material-design-icon {
    margin-top: 2px;
}
.theme-option > h6 {
    line-height: 0.8;
}

.v-select.select {
    min-width: 300px;
}
</style>
