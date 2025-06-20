<!-- eslint-disable -->
<script setup>
import { catalogiStore, publicationTypeStore, navigationStore, publicationStore, themeStore, organizationStore } from '../../store/store.js'
</script>

<template>
	<div class="detailContainer">
		<div class="head">
			<h1 class="h1">
				{{ publicationStore.publicationItem?.title }}
			</h1>

			<NcActions :disabled="loading"
				:primary="true"
				:menu-name="loading ? 'Laden...' : 'Acties'"
				:inline="1"
				title="Acties die je kan uitvoeren op deze publicatie">
				<template #icon>
					<span>
						<NcLoadingIcon v-if="loading" :size="20" appearance="dark" />
						<DotsHorizontal v-if="!loading" :size="20" />
					</span>
				</template>
				<NcActionButton close-after-click
					title="Bekijk de documentatie over publicaties"
					@click="openLink('https://conduction.gitbook.io/opencatalogi-nextcloud/gebruikers/publicaties', '_blank')">
					<template #icon>
						<HelpCircleOutline :size="20" />
					</template>
					Help
				</NcActionButton>
				<NcActionButton close-after-click @click="navigationStore.setModal('editPublication')">
					<template #icon>
						<Pencil :size="20" />
					</template>
					Bewerken
				</NcActionButton>
				<NcActionButton close-after-click @click="navigationStore.setDialog('copyPublication')">
					<template #icon>
						<ContentCopy :size="20" />
					</template>
					Kopiëren
				</NcActionButton>
				<NcActionButton v-if="publicationStore.publicationItem?.status !== 'Published'"
					close-after-click
					@click="publicationStore.setPublicationItem(publication); navigationStore.setDialog('publishPublication')">
					<template #icon>
						<Publish :size="20" />
					</template>
					Publiceren
				</NcActionButton>
				<NcActionButton v-if="publicationStore.publicationItem?.status === 'Published'"
					close-after-click
					@click="publicationStore.setPublicationItem(publication); navigationStore.setDialog('depublishPublication')">
					<template #icon>
						<PublishOff :size="20" />
					</template>
					Depubliceren
				</NcActionButton>
				<NcActionButton close-after-click @click="navigationStore.setDialog('archivePublication')">
					<template #icon>
						<ArchivePlusOutline :size="20" />
					</template>
					Archiveren
				</NcActionButton>
				<NcActionButton close-after-click @click="navigationStore.setDialog('downloadPublication')">
					<template #icon>
						<Download :size="20" />
					</template>
					Downloaden
				</NcActionButton>
				<NcActionButton close-after-click @click="navigationStore.setModal('addPublicationData')">
					<template #icon>
						<FileTreeOutline :size="20" />
					</template>
					Eigenschap toevoegen
				</NcActionButton>
				<NcActionButton close-after-click @click="addAttachment">
					<template #icon>
						<FolderOutline :size="20" />
					</template>
					Bijlage toevoegen
				</NcActionButton>
				<NcActionButton close-after-click @click="navigationStore.setModal('addPublicationTheme')">
					<template #icon>
						<ShapeOutline :size="20" />
					</template>
					Thema toevoegen
				</NcActionButton>
				<NcActionButton close-after-click @click="navigationStore.setDialog('deletePublication')">
					<template #icon>
						<Delete :size="20" />
					</template>
					Verwijderen
				</NcActionButton>
			</NcActions>
		</div>
		<div class="container">
			<div class="detailGrid">
				<div v-if="publicationStore.publicationItem?.reference">
					<b>Referentie:</b>
					<span>{{ publicationStore.publicationItem?.reference }}</span>
				</div>
				<div v-if="publicationStore.publicationItem?.summary">
					<b>Samenvatting:</b>
					<span>{{ publicationStore.publicationItem?.summary || '-' }}</span>
				</div>
				<div v-if="publicationStore.publicationItem?.description">
					<b>Beschrijving:</b>
					<span>{{ publicationStore.publicationItem?.description || '-' }}</span>
				</div>
				<div v-if="publicationStore.publicationItem?.category">
					<b>Categorie:</b>
					<span>{{ publicationStore.publicationItem?.category || '-' }}</span>
				</div>
				<div v-if="publicationStore.publicationItem?.portal">
					<b>Portal:</b>
					<span><a target="_blank" :href="publicationStore.publicationItem?.portal">{{
						publicationStore.publicationItem?.portal || '-' }}</a></span>
				</div>
				<div v-if="publicationStore.publicationItem?.image">
					<b>Afbeelding:</b>
					<span>{{ publicationStore.publicationItem?.image || '-' }}</span>
				</div>
				<div v-if="publicationStore.publicationItem?.featured">
					<b>Uitgelicht:</b>
					<span>{{ publicationStore.publicationItem?.featured ? "Ja" : "Nee" }}</span>
				</div>
				<div v-if="publicationStore.publicationItem?.license">
					<b>Licentie:</b>
					<span>{{ publicationStore.publicationItem?.license || '-' }}</span>
				</div>
				<div v-if="publicationStore.publicationItem?.status">
					<b>Status:</b>
					<span>{{ publicationStore.publicationItem?.status || '-' }}</span>
				</div>
				<div v-if="publicationStore.publicationItem?.published">
					<b>Gepubliceerd:</b>
					<span>{{ new Date(publicationStore.publicationItem?.published).toLocaleDateString() || '-' }}</span>
				</div>
				<div v-if="publicationStore.publicationItem?.modified">
					<b>Gewijzigd:</b>
					<span>{{ publicationStore.publicationItem?.modified || '-' }}</span>
				</div>
				<div v-if="publicationStore.publicationItem?.source">
					<b>Bron:</b>
					<span>{{ publicationStore.publicationItem?.source || '-' }}</span>
				</div>
				<div v-if="publicationStore.publicationItem?.catalogi">
					<b>Catalogi:</b>
					<span v-if="catalogiLoading">Loading...</span>
					<div v-if="!catalogiLoading" class="buttonLinkContainer">
						<span>{{ catalogi?.title }}</span>
						<NcActions>
							<NcActionLink :aria-label="`ga naar ${catalogi?.title}`"
								:name="catalogi?.title"
								@click="goToCatalogi()">
								<template #icon>
									<OpenInApp :size="20" />
								</template>
								{{ catalogi?.title }}
							</NcActionLink>
						</NcActions>
					</div>
				</div>
			</div>
			<div class="detailGrid linksParameters">
				<div>
					<b>Organisatie:</b>
					<span v-if="organizationLoading">Loading...</span>
					<div v-if="!organizationLoading && organization?.title" class="buttonLinkContainer">
						<span>{{ organization?.title }}</span>
						<NcActions>
							<NcActionLink :aria-label="`ga naar ${organization?.title}`"
								:name="organization?.title"
								@click="goToOrganization()">
								<template #icon>
									<OpenInApp :size="20" />
								</template>
								{{ organization?.title }}
							</NcActionLink>
						</NcActions>
					</div>
					<div v-if="!organizationLoading && !organization?.title" class="buttonLinkContainer">
						<span>Geen organisatie gekoppeld</span>
					</div>
				</div>
				<div v-if="publicationStore.publicationItem?.publicationType">
					<b>Publicatietype:</b>
					<span v-if="publicationTypeLoading">Loading...</span>
					<div v-if="!publicationTypeLoading" class="buttonLinkContainer">
						<span>{{ publicationType?.title }}</span>
						<NcActions>
							<NcActionLink :aria-label="`ga naar ${publicationType?.title}`"
								:name="publicationType?.title"
								@click="goToPublicationType()">
								<template #icon>
									<OpenInApp :size="20" />
								</template>
								{{ publicationType?.title }}
							</NcActionLink>
						</NcActions>
					</div>
				</div>
			</div>
			<div class="tabContainer">
				<BTabs content-class="mt-3" justified>
					<BTab title="Bijlagen" active>
						<div class="tabPanel">
							<div class="buttonsContainer">
								<NcButton type="primary"
									class="fullWidthButton"
									aria-label="Bijlage toevoegen"
									@click="addAttachment">
									<template #icon>
										<Plus :size="20" />
									</template>
									Bijlage toevoegen
								</NcButton>
								<NcButton type="secondary"
									aria-label="Open map"
									@click="openFolder(publicationStore.publicationItem?.['@self']?.folder)">
									<template #icon>
										<FolderOutline :size="20" />
									</template>
								</NcButton>
								<NcActions :disabled="loading"
									:primary="true"
									class="checkboxListActionButton"
									:menu-name="loading ? 'Laden...' : 'Acties'"
									:inline="0"
									title="Acties die je kan uitvoeren op deze publicatie">
									<template #icon>
										<span>
											<NcLoadingIcon v-if="loading" :size="20" appearance="dark" />
											<DotsHorizontal v-if="!loading" :size="20" />
										</span>
									</template>
									<NcActionButton close-after-click @click="selectAllAttachments('published')">
										<template #icon>
											<SelectAllIcon v-if="!allPublishedSelected" :size="20" />
											<SelectRemove v-else :size="20" />
										</template>
										{{ !allPublishedSelected ? "Selecteer" : "Deselecteer" }} alle gepubliceerde bijlagen
									</NcActionButton>
									<NcActionButton close-after-click @click="selectAllAttachments('unpublished')">
										<template #icon>
											<SelectAllIcon v-if="!allUnpublishedSelected" :size="20" />
											<SelectRemove v-else :size="20" />
										</template>
										{{ !allUnpublishedSelected ? "Selecteer" : "Deselecteer" }} alle ongepubliceerde bijlagen
									</NcActionButton>
									<NcActionButton v-if="selectedUnpublishedCount > 0" close-after-click @click="bulkPublish">
										<template #icon>
											<Publish :size="20" />
										</template>
										Publiceer {{ selectedUnpublishedCount }} bijlage{{ selectedUnpublishedCount > 1 ? 'n' : '' }}
									</NcActionButton>
									<NcActionButton v-if="selectedPublishedCount > 0" close-after-click @click="bulkDepublish">
										<template #icon>
											<PublishOff :size="20" />
										</template>
										Depubliceer {{ selectedPublishedCount }} bijlage{{ selectedPublishedCount > 1 ? 'n' : '' }}
									</NcActionButton>
									<NcActionButton v-if="selectedAttachments.length > 0" close-after-click @click="bulkDeleteAttachments">
										<template #icon>
											<Delete :size="20" />
										</template>
										Verwijder {{ selectedAttachmentsEntities.length }} bijlage{{ selectedAttachmentsEntities.length > 1 ? 'n' : '' }}
									</NcActionButton>
								</NcActions>
							</div>

							<div v-if="publicationStore.publicationAttachments?.results?.length > 0">
								<div v-for="(attachment, i) in publicationStore.publicationAttachments?.results" :key="`${attachment}${i}`" class="checkedItem">
									<NcCheckboxRadioSwitch
										:checked="selectedAttachments.includes(attachment.id)"
										@update:checked="toggleSelection(attachment)" />

									<NcListItem
										:class="`${attachment.title === editingTags ? 'editingTags' : ''}`"
										:name="attachment.name ?? attachment?.title"
										:bold="false"
										:active="publicationStore.attachmentItem?.id === attachment.id"
										:force-display-actions="true"
										@click="setActiveAttachment(attachment)">
										<template #icon>
											<NcLoadingIcon v-if="fileIdsLoading.includes(attachment.id) && (depublishLoading.includes(attachment.id) || publishLoading.includes(attachment.id) || saveTagsLoading.includes(attachment.id))" :size="44" />
											<ExclamationThick v-else-if="!attachment.accessUrl || !attachment.downloadUrl" class="warningIcon" :size="44" />
											<FileOutline v-else
												class="publishedIcon"
												disable-menu
												:size="44" />
										</template>

										<template #details>
											<span>{{ formatFileSize(attachment?.size) }}</span>
										</template>
										<template #indicator>
											<div v-if="editingTags !== attachment.title" class="fileLabelsContainer">
												<NcCounterBubble v-for="label of attachment.labels" :key="label">
													{{ label }}
												</NcCounterBubble>
											</div>
											<div v-if="editingTags === attachment.title" class="editTagsContainer">
												<NcSelect
													v-model="editedTags"
													class="editTagsSelect"
													:disabled="saveTagsLoading.includes(attachment.id)"
													:taggable="true"
													:multiple="true"
													:aria-label-combobox="labelOptionsEdit.inputLabel"
													:options="labelOptionsEdit.options" />
												<NcButton
													v-tooltip="'Labels opslaan'"
													class="editTagsButton"
													type="primary"
													:aria-label="`save tags for ${attachment.title}`"
													@click="saveTags(attachment, editedTags)">
													<template #icon>
														<ContentSaveOutline v-if="!saveTagsLoading.includes(attachment.id)" :size="20" />
														<NcLoadingIcon v-if="saveTagsLoading.includes(attachment.id)" :size="20" />
													</template>
												</NcButton>
											</div>
										</template>
										<template #subname>
											{{ attachment?.published ? new Date(attachment?.published).toLocaleDateString() : "Niet gepubliceerd" }} - {{ attachment?.type || 'Geen type' }}
										</template>
										<template #actions>
											<NcActionButton close-after-click @click="openFile(attachment)">
												<template #icon>
													<OpenInNew :size="20" />
												</template>
												Bekijk bestand
											</NcActionButton>
											<NcActionButton v-if="!attachment.published"
												close-after-click
												@click="publishFile(attachment)">
												<template #icon>
													<Publish :size="20" />
												</template>
												Publiceren
											</NcActionButton>
											<NcActionButton v-if="attachment.published"
												close-after-click
												@click="depublishFile(attachment)">
												<template #icon>
													<PublishOff :size="20" />
												</template>
												Depubliceren
											</NcActionButton>
											<NcActionButton close-after-click @click="deleteFile(attachment)">
												<template #icon>
													<Delete :size="20" />
												</template>
												Verwijderen
											</NcActionButton>
											<NcActionButton close-after-click @click="editTags(attachment)">
												<template #icon>
													<TagEdit :size="20" />
												</template>
												Tags bewerken
											</NcActionButton>
										</template>
									</NcListItem>
								</div>

								<div class="paginationContainer">
									<BPagination v-model="currentPage" :total-rows="publicationStore.publicationAttachments?.total" :per-page="limit" />
									<div>
										<span>Aantal per pagina</span>
										<NcSelect v-model="limit"
											aria-label-combobox="Aantal per pagina"
											class="limitSelect"
											:options="limitOptions.options"
											:taggable="true"
											:selectable="(option) => !isNaN(option) && (typeof option !== 'boolean')" />
									</div>
								</div>
							</div>

							<div v-if="publicationStore.publicationAttachments?.results?.length === 0">
								<b class="emptyStateMessage">
									Nog geen bijlage toegevoegd
								</b>
							</div>

							<div
								v-if="publicationStore.publicationAttachments?.results?.length !== 0 && !publicationStore.publicationAttachments?.results?.length > 0">
								<NcLoadingIcon :size="64"
									class="loadingIcon"
									appearance="dark"
									name="Bijlagen aan het laden" />
							</div>
						</div>
					</BTab>
					<BTab title="Eigenschappen">
						<div class="tabPanel">
							<div class="buttonsContainer">
								<NcButton type="primary"
									class="fullWidthButton"
									aria-label="Bijlage toevoegen"
									@click="navigationStore.setModal('addPublicationData')">
									<template #icon>
										<Plus :size="20" />
									</template>
									Eigenschap toevoegen
								</NcButton>
								<NcActions :disabled="loading"
									:primary="true"
									class="checkboxListActionButton"
									:menu-name="loading ? 'Laden...' : 'Acties'"
									:inline="0"
									title="Acties die je kan uitvoeren op deze publicatie">
									<template #icon>
										<span>
											<NcLoadingIcon v-if="loading" :size="20" appearance="dark" />
											<DotsHorizontal v-if="!loading" :size="20" />
										</span>
									</template>
									<NcActionButton close-after-click @click="selectAllPublicationData()">
										<template #icon>
											<SelectAllIcon v-if="!allPublicationDataSelected" :size="20" />
											<SelectRemove v-else :size="20" />
										</template>
										{{ !allPublicationDataSelected ? "Selecteer" : "Deselecteer" }} alle eigenschappen
									</NcActionButton>
									<NcActionButton close-after-click :disabled="selectedPublicationData.length === 0" @click="bulkDeleteEigenschappen">
										<template #icon>
											<Delete :size="20" />
										</template>
										Verwijder {{ selectedPublicationData.length }} eigenschap{{ selectedPublicationData.length > 1 || selectedPublicationData.length === 0 ? 'pen' : '' }}
									</NcActionButton>
								</NcActions>
							</div>
							<div v-if="Object.keys(publicationStore.publicationItem?.data).length > 0">
								<div v-for="(value, key, i) in publicationStore.publicationItem?.data" :key="`${key}${i}`" class="checkedItem">
									<NcCheckboxRadioSwitch
										:checked="selectedPublicationData.includes(key)"
										@update:checked="togglePublicationDataSelection(key)" />
									<NcListItem
										:name="key"
										:bold="false"
										:force-display-actions="true"
										:active="publicationStore.publicationDataKey === key"
										@click="setActiveDataKey(key)">
										<template #icon>
											<CircleOutline
												:class="publicationStore.publicationDataKey === key && 'selectedZaakIcon'"
												disable-menu
												:size="44" />
										</template>
										<template #subname>
											{{ value }}
										</template>
										<template #actions>
											<NcActionButton close-after-click @click="editPublicationDataItem(key)">
												<template #icon>
													<Pencil :size="20" />
												</template>
												Bewerken
											</NcActionButton>
											<NcActionButton close-after-click @click="deletePublicationDataItem(key)">
												<template #icon>
													<Delete :size="20" />
												</template>
												Verwijderen
											</NcActionButton>
										</template>
									</NcListItem>
								</div>
							</div>
						</div>
						<div v-if="Object.keys(publicationStore.publicationItem?.data).length === 0" class="tabPanel">
							<b class="emptyStateMessage">
								Geen eigenschappen gevonden
							</b>
						</div>
					</BTab>
					<BTab title="Thema's">
						<div class="tabPanel">
							<div class="buttonsContainer">
								<NcButton type="primary"
									class="fullWidthButton"
									aria-label="Thema toevoegen"
									@click="navigationStore.setModal('addPublicationTheme')">
									<template #icon>
										<Plus :size="20" />
									</template>
									Thema toevoegen
								</NcButton>
								<NcActions :disabled="loading"
									:primary="true"
									class="checkboxListActionButton"
									:menu-name="loading ? 'Laden...' : 'Acties'"
									:inline="0"
									title="Acties die je kan uitvoeren op deze publicatie">
									<template #icon>
										<span>
											<NcLoadingIcon v-if="loading" :size="20" appearance="dark" />
											<DotsHorizontal v-if="!loading" :size="20" />
										</span>
									</template>
									<NcActionButton close-after-click @click="selectAllThemes()">
										<template #icon>
											<SelectAllIcon v-if="!allThemesSelected" :size="20" />
											<SelectRemove v-else :size="20" />
										</template>
										{{ !allThemesSelected ? "Selecteer" : "Deselecteer" }} alle thema's
									</NcActionButton>
									<NcActionButton close-after-click :disabled="selectedThemes.length === 0" @click="bulkDeleteThemes">
										<template #icon>
											<Delete :size="20" />
										</template>
										Verwijder {{ selectedThemes.length }} thema{{ selectedThemes.length > 1 || selectedThemes.length === 0 ? "'s" : '' }}
									</NcActionButton>
								</NcActions>
							</div>
							<div v-if="filteredThemes?.length || missingThemes?.length">
								<div v-for="(value, key, i) in filteredThemes" :key="`${value.id}${i}`" class="checkedItem">
									<NcCheckboxRadioSwitch
										:checked="selectedThemes.includes(value.id)"
										@update:checked="toggleThemeSelection(value)" />
									<NcListItem
										:name="value.title"
										:bold="false"
										:force-display-actions="true">
										<template #icon>
											<ShapeOutline
												:class="themeStore.themeItem?.id === value.id && 'selectedZaakIcon'"
												disable-menu
												:size="44" />
										</template>
										<template #subname>
											{{ value.summary }}
										</template>
										<template #actions>
											<NcActionButton close-after-click @click="themeStore.setThemeItem(value); navigationStore.setSelected('themes')">
												<template #icon>
													<OpenInApp :size="20" />
												</template>
												Bekijken
											</NcActionButton>
											<NcActionButton close-after-click @click="themeStore.setThemeItem(value); navigationStore.setDialog('deletePublicationThemeDialog')">
												<template #icon>
													<Delete :size="20" />
												</template>
												Verwijderen
											</NcActionButton>
										</template>
									</NcListItem>
								</div>
								<NcListItem v-for="(value, key, i) in missingThemes"
									:key="`${value}${i}`"
									:name="'Thema ' + value"
									:bold="false"
									:force-display-actions="true">
									<template #icon>
										<Alert disable-menu
											:size="44" />
									</template>
									<template #subname>
										Thema {{ value }} bestaat niet, het is aan te raden om het te verwijderen van deze publicatie.
									</template>
									<template #actions>
										<NcActionButton close-after-click :disabled="deleteThemeLoading" @click="deleteMissingTheme(value)">
											<template #icon>
												<Delete :size="20" />
											</template>
											Verwijderen
										</NcActionButton>
									</template>
								</NcListItem>
							</div>
							<div v-if="!filteredThemes?.length && !missingThemes?.length" class="tabPanel">
								<b class="emptyStateMessage">
									Geen thema's gevonden
								</b>
							</div>
						</div>
					</BTab>
					<BTab title="Logging">
						<table width="100%">
							<tr>
								<th><b>Tijdstip</b></th>
								<th><b>Gebruiker</b></th>
								<th><b>Actie</b></th>
								<th><b>Details</b></th>
							</tr>
							<tr>
								<td>18-07-2024 11:55:21</td>
								<td>Ruben van der Linde</td>
								<td>Created</td>
								<td>
									<NcButton @click="navigationStore.setDialog('viewLog')">
										<template #icon>
											<TimelineQuestionOutline :size="20" />
										</template>
										Bekijk details
									</NcButton>
								</td>
							</tr>
						</table>
					</BTab>
					<BTab v-if="1 == 2" title="Rechten">
						<table width="100%">
							<tr>
								<td>Deze publicatie is <b v-if="prive">NIET</b> openbaar toegankelijk</td>
								<td>
									<NcButton @click="prive = !prive">
										<template #icon>
											<LockOpenVariantOutline v-if="!prive" :size="20" />
											<LockOutline v-if="prive" :size="20" />
										</template>
										<span v-if="!prive">Privé maken</span>
										<span v-if="prive">Openbaar maken</span>
									</NcButton>
								</td>
							</tr>
							<tr v-if="prive">
								<td>Gebruikersgroepen</td>
								<td>
									<NcSelectTags v-model="userGroups"
										input-label="gebruikers groepen"
										:multiple="true" />
								</td>
							</tr>
						</table>
					</BTab>
					<BTab v-if="1 == 2" title="Statistieken">
						<apexchart v-if="publication.status === 'Published'"
							width="100%"
							type="line"
							:options="chart.options"
							:series="chart.series" />
						<NcNoteCard type="info">
							<p>Er zijn nog geen statistieken over deze publicatie bekend</p>
						</NcNoteCard>
					</BTab>
				</BTabs>
			</div>
		</div>
		<DeleteMultipleAttachmentsDialog
			v-if="navigationStore.dialog === 'deleteMultipleAttachments'"
			:attachments-to-delete="selectedAttachmentsEntities"
			@done="onBulkDeleteAttachmentsDone"
			@cancel="onBulkDeleteAttachmentsCancel" />
		<DeleteMultiplePublicationDataDialog
			v-if="navigationStore.dialog === 'deleteMultiplePublicationData'"
			:keys-to-delete="selectedPublicationData"
			@done="onBulkDeletePublicationDataDone"
			@cancel="onBulkDeletePublicationDataCancel" />
		<DeleteMultipleThemesDialog
			v-if="navigationStore.dialog === 'deleteMultipleThemes'"
			:themes-to-delete="selectedThemesEntities"
			@done="onBulkDeleteThemesDone"
			@cancel="onBulkDeleteThemesCancel" />
	</div>
</template>

<script>
// Components
import { NcActionButton, NcActions, NcButton, NcListItem, NcLoadingIcon, NcNoteCard, NcSelect, NcSelectTags, NcActionLink, NcCounterBubble, NcCheckboxRadioSwitch } from '@nextcloud/vue'
import { BTab, BTabs, BPagination } from 'bootstrap-vue'
import VueApexCharts from 'vue-apexcharts'
import DeleteMultipleAttachmentsDialog from '../../dialogs/attachment/DeleteMultipleAttachmentsDialog.vue'
import DeleteMultiplePublicationDataDialog from '../../dialogs/publicationData/DeleteMultiplePublicationDataDialog.vue'
import DeleteMultipleThemesDialog from '../../dialogs/publicationTheme/DeleteMultiplePublicationThemeDialog.vue'

// Icons
import ArchivePlusOutline from 'vue-material-design-icons/ArchivePlusOutline.vue'
import CircleOutline from 'vue-material-design-icons/CircleOutline.vue'
import ContentCopy from 'vue-material-design-icons/ContentCopy.vue'
import Delete from 'vue-material-design-icons/Delete.vue'
import DotsHorizontal from 'vue-material-design-icons/DotsHorizontal.vue'
import Download from 'vue-material-design-icons/Download.vue'
import FileTreeOutline from 'vue-material-design-icons/FileTreeOutline.vue'
import HelpCircleOutline from 'vue-material-design-icons/HelpCircleOutline.vue'
import LockOpenVariantOutline from 'vue-material-design-icons/LockOpenVariantOutline.vue'
import LockOutline from 'vue-material-design-icons/LockOutline.vue'
import OpenInApp from 'vue-material-design-icons/OpenInApp.vue'
import Pencil from 'vue-material-design-icons/Pencil.vue'
import Publish from 'vue-material-design-icons/Publish.vue'
import PublishOff from 'vue-material-design-icons/PublishOff.vue'
import TimelineQuestionOutline from 'vue-material-design-icons/TimelineQuestionOutline.vue'
import FolderOutline from 'vue-material-design-icons/FolderOutline.vue'
import OpenInNew from 'vue-material-design-icons/OpenInNew.vue'
import Alert from 'vue-material-design-icons/Alert.vue'
import FileOutline from 'vue-material-design-icons/FileOutline.vue'
import ShapeOutline from 'vue-material-design-icons/ShapeOutline.vue'
import ExclamationThick from 'vue-material-design-icons/ExclamationThick.vue'
import Plus from 'vue-material-design-icons/Plus.vue'
import TagEdit from 'vue-material-design-icons/TagEdit.vue'
import ContentSaveOutline from 'vue-material-design-icons/ContentSaveOutline.vue'
import SelectAllIcon from 'vue-material-design-icons/SelectAll.vue'
import SelectRemove from 'vue-material-design-icons/SelectRemove.vue'
import { Publication } from '../../entities/index.js'
import { getTheme } from '../../services/getTheme.js'

export default {
	name: 'PublicationDetail',
	components: {
		// Components
		NcLoadingIcon,
		NcActionButton,
		NcActions,
		NcButton,
		NcListItem,
		NcSelectTags,
		NcNoteCard,
		NcActionLink,
		NcCheckboxRadioSwitch,
		DeleteMultipleAttachmentsDialog,
		BTab,
		BTabs,
		apexchart: VueApexCharts,
		DeleteMultiplePublicationDataDialog,
		DeleteMultipleThemesDialog,
	},
	props: {
		publicationItem: {
			type: Object,
			required: true,
		},
	},
	data() {
		return {
			publication: [],
			catalogi: [],
			publicationType: [],
			organization: [],
			themes: [],
			logs: [],
			prive: false,
			loading: false,
			catalogiLoading: false,
			publicationTypeLoading: false,
			organizationLoading: false,
			hasUpdated: false,
			saveTagsLoading: [],
			selectedAttachments: [],
			selectedPublicationData: [],
			userGroups: [
				{
					id: '1',
					label: 'Content Beheerders',
				},
			],
			chart: {
				options: {
					theme: {
						mode: getTheme(),
						monochrome: {
							enabled: true,
							color: getComputedStyle(document.documentElement).getPropertyValue('--color-primary-element').trim() || '#079cff',
							shadeTo: 'light',
							shadeIntensity: 0,
						},
					},
					chart: {
						id: 'Aantal bekeken publicaties',
					},
					xaxis: {
						categories: ['7-11', '7-12', '7-13', '7-15', '7-16', '7-17', '7-18'],
					},
				},
				series: [{
					name: 'Weergaven',
					data: [0, 0, 0, 0, 0, 0, 15],
				}],
			},
			upToDate: false,
			deleteThemeLoading: false,
			editingTags: null,
			editedTags: [],
			publishLoading: [],
			depublishLoading: [],
			fileIdsLoading: [],
			labelOptionsEdit: {
				inputLabel: 'Labels',
				multiple: true,
			},
			limit: '200',
			limitOptions: {
				options: ['10', '20', '50', '100', '200'],
				value: this.limit,
			},
			currentPage: publicationStore.publicationAttachments?.page || 1,
			totalPages: publicationStore.publicationAttachments?.total || 1,
			selectedThemes: [],
		}
	},
	computed: {
		filteredThemes() {
			return themeStore.themeList.filter((theme) => this.publication?.themes?.includes(theme.id))
		},
		missingThemes() { // themes (id's)- which are on the publication but do not exist on the themeList
			return this.publication?.themes?.filter((themeId) => !themeStore.themeList.map((theme) => theme.id).includes(themeId))
		},
		selectedPublishedCount() {
			return this.selectedAttachments.filter((a) => {
				const found = publicationStore?.publicationAttachments?.results
					?.find(item => item.id === a)
				if (!found) return false

				return !!found.published
			}).length
		},
		selectedUnpublishedCount() {
			return this.selectedAttachments.filter((a) => {
				const found = publicationStore?.publicationAttachments?.results
					?.find(item => item.id === a)
				if (!found) return false
				return found.published === null
			}).length
		},
		selectedAttachmentsEntities() {
			return publicationStore.publicationAttachments?.results
				?.filter(attach => this.selectedAttachments.includes(attach.id)) || []
		},
		allPublishedSelected() {
			const published = publicationStore.publicationAttachments?.results
				?.filter(item => !!item.published)
				.map(item => item.id) || []

			if (!published.length) {
				return false
			}
			return published.every(pubId => this.selectedAttachments.includes(pubId))
		},
		allUnpublishedSelected() {
			const unpublished = publicationStore.publicationAttachments?.results
				?.filter(item => !item.published)
				.map(item => item.id) || []

			if (!unpublished.length) {
				return false
			}
			return unpublished.every(unpubId => this.selectedAttachments.includes(unpubId))
		},
		allPublicationDataSelected() {
			const keys = publicationStore.publicationItem ? Object.keys(publicationStore.publicationItem.data) : []
			if (!keys.length) return false
			return keys.every(key => this.selectedPublicationData.includes(key))
		},
		allThemesSelected() {
			const themes = this.filteredThemes?.map(theme => theme.id) || []
			if (!themes.length) return false
			return themes.every(themeId => this.selectedThemes.includes(themeId))
		},
		selectedThemesEntities() {
			return this.filteredThemes?.filter(theme => this.selectedThemes.includes(theme.id)) || []
		},
	},
	watch: {
		publicationItem: {
			handler(newPublicationItem, oldPublicationItem) {

				if (!this.upToDate || JSON.stringify(newPublicationItem) !== JSON.stringify(oldPublicationItem)) {
					this.publication = publicationStore.publicationItem
					this.fetchCatalogi(publicationStore.publicationItem?.catalog?.id ?? publicationStore.publicationItem.catalogi)
					this.fetchPublicationType(publicationStore.publicationItem?.publicationType)
					this.fetchThemes()
					publicationStore.publicationItem?.id && this.fetchData(publicationStore.publicationItem.id)
				}
			},
			deep: true,
		},

		currentPage(newVal) {
			this.loading = true
			publicationStore.getPublicationAttachments(this.publication.id, { page: newVal, limit: this.limit }).finally(() => {
				this.loading = false
			})
		},

		limit(newVal) {
			this.loading = true
			publicationStore.getPublicationAttachments(this.publication.id, { page: 1, limit: newVal }).finally(() => {
				this.loading = false
			})
		},

	},
	mounted() {
		this.publication = publicationStore.publicationItem

		this.fetchCatalogi(this.publication.catalog?.id ?? this.publication.catalog)
		this.fetchPublicationType(publicationStore.publicationItem.publicationType)
		this.fetchThemes()
		publicationStore.publicationItem?.id && this.fetchData(publicationStore.publicationItem.id)

	},
	methods: {
		fetchData(id) {
			// this.loading = true

			publicationStore.getOnePublication(id, { doNotSetStore: true })
				.then(({ response, data }) => {
					this.publication = data
					// this.oldZaakId = id
					this.fetchCatalogi(data.catalog?.id ?? data.catalog)
					this.fetchPublicationType(data.publicationType)
					this.fetchThemes()
					publicationStore.getPublicationAttachments(id, { page: this.currentPage, limit: this.limit })
					publicationStore.getTags().then(({ response, data }) => {
						this.labelOptionsEdit.options = data
					})
					data?.organization && this.fetchOrganization(data.organization, true)
					// this.loading = false
				})
				.catch((err) => {
					console.error(err)
					// this.oldZaakId = id
					// this.loading = false
				})
		},
		fetchCatalogi(catalogiId, loading) {
			if (loading) { this.catalogiLoading = true }

			catalogiStore.getOneCatalogi(catalogiId, { doNotSetStore: true })
				.then(({ response, data }) => {
					this.catalogi = data

					if (loading) { this.catalogiLoading = false }
				})
				.catch((err) => {
					console.error(err)
					if (loading) { this.catalogiLoading = false }
				})
		},
		fetchOrganization(organizationId, loading) {
			if (loading) { this.organizationLoading = true }

			organizationStore.getOneOrganization(organizationId, { doNotSetStore: true })
				.then(({ response, data }) => {
					this.organization = data

					if (loading) { this.organizationLoading = false }
				})
				.catch((err) => {
					console.error(err)
					if (loading) { this.organizationLoading = false }
				})
		},
		deleteFile(attachment) {
			publicationStore.setAttachmentItem(attachment)
			publicationStore.setCurrentPage(this.currentPage)
			publicationStore.setLimit(this.limit)
			navigationStore.setDialog('deleteAttachment')
		},
		publishFile(attachment) {
			this.publishLoading.push(attachment.id)
			this.fileIdsLoading.push(attachment.id)
			publicationStore.publishFile(this.publication.id, attachment.title).then(() => {
				publicationStore.getPublicationAttachments(this.publication.id, { page: this.currentPage, limit: this.limit }).finally(() => {
					this.publishLoading.splice(this.publishLoading.indexOf(attachment.id), 1)
					this.fileIdsLoading.splice(this.fileIdsLoading.indexOf(attachment.id), 1)
				})
			})
		},
		depublishFile(attachment) {
			this.depublishLoading.push(attachment.id)
			this.fileIdsLoading.push(attachment.id)
			publicationStore.depublishFile(this.publication.id, attachment.title).then(() => {
				publicationStore.getPublicationAttachments(this.publication.id, { page: this.currentPage, limit: this.limit }).finally(() => {
					this.depublishLoading.splice(this.depublishLoading.indexOf(attachment.id), 1)
					this.fileIdsLoading.splice(this.fileIdsLoading.indexOf(attachment.id), 1)
				})
			})
		},
		bulkPublish() {
			const unpublishedAttachments = publicationStore.publicationAttachments?.results
				?.filter(
					attachment =>
						this.selectedAttachments.includes(attachment.id) && !attachment.published,
				) || []

			const promises = unpublishedAttachments.map(async attachment => {
				return await this.publishFile(attachment)
			})

			Promise.all(promises).then(() => {
				publicationStore.getPublicationAttachments(this.publication.id, {
					page: this.currentPage,
					limit: this.limit,
				})
				this.selectedAttachments = []
			})
		},

		bulkDepublish() {
			const publishedAttachments = publicationStore.publicationAttachments?.results
				?.filter(
					attachment =>
						this.selectedAttachments.includes(attachment.id) && attachment.published,
				) || []

			const promises = publishedAttachments.map(async attachment => {
				return await this.depublishFile(attachment)
			})

			Promise.all(promises).then(() => {
				this.getPublicationAttachments(this.publication.id, {
					page: this.currentPage,
					limit: this.limit,
				})
				this.selectedAttachments = []
			})
		},
		bulkDeleteAttachments() {
			if (!this.selectedAttachments.length) return
			navigationStore.setDialog('deleteMultipleAttachments')
		},
		onBulkDeleteAttachmentsDone() {
			navigationStore.setDialog(false)
			this.selectedAttachments = []
			publicationStore.getPublicationAttachments(
				this.publication.id, { page: 1, limit: this.limit },
			)
		},
		onBulkDeleteAttachmentsCancel() {
			navigationStore.setDialog(false)
		},
		toggleSelection(attachment) {
			const numericId = Number(attachment.id)
			if (this.selectedAttachments.includes(numericId)) {
				this.selectedAttachments = this.selectedAttachments.filter(itemId => itemId !== numericId)
			} else {
				this.selectedAttachments.push(numericId)
			}
		},
		selectAllAttachments(mode) {
			if (mode === 'published') {
				const publishedIds = publicationStore.publicationAttachments?.results
					?.filter(item => item.published)
					.map(item => Number(item.id)) || []

				const allSelected = publishedIds.length > 0 && publishedIds.every(id => this.selectedAttachments.includes(id))

				if (!allSelected) {
					this.selectedAttachments = Array.from(new Set([...this.selectedAttachments, ...publishedIds]))
				} else {
					this.selectedAttachments = this.selectedAttachments.filter(id => !publishedIds.includes(id))
				}
			} else if (mode === 'unpublished') {
				const unpublishedIds = publicationStore.publicationAttachments?.results
					?.filter(item => !item.published)
					.map(item => Number(item.id)) || []

				const allSelected = unpublishedIds.length > 0 && unpublishedIds.every(id => this.selectedAttachments.includes(id))

				if (!allSelected) {
					this.selectedAttachments = Array.from(new Set([...this.selectedAttachments, ...unpublishedIds]))
				} else {
					this.selectedAttachments = this.selectedAttachments.filter(id => !unpublishedIds.includes(id))
				}
			}
		},
		togglePublicationDataSelection(key) {
			if (this.selectedPublicationData.includes(key)) {
				this.selectedPublicationData = this.selectedPublicationData.filter(k => k !== key)
			} else {
				this.selectedPublicationData.push(key)
			}
		},
		selectAllPublicationData() {
			const keys = publicationStore.publicationItem ? Object.keys(publicationStore.publicationItem.data) : []
			if (!keys.length) return

			if (!this.allPublicationDataSelected) {
				this.selectedPublicationData = keys
			} else {
				this.selectedPublicationData = []
			}
		},
		bulkDeleteEigenschappen() {
			if (!this.selectedPublicationData.length) return
			navigationStore.setDialog('deleteMultiplePublicationData')
		},
		onBulkDeletePublicationDataDone() {
			navigationStore.setDialog(false)
			this.selectedPublicationData = []
		},
		onBulkDeletePublicationDataCancel() {
			navigationStore.setDialog(false)
		},
		editTags(attachment) {
			this.editingTags = attachment.title
			this.editedTags = attachment.labels
		},
		saveTags(attachment) {
			this.saveTagsLoading.push(attachment.id)
			this.fileIdsLoading.push(attachment.id)
			publicationStore.editTags(this.publication.id, attachment.title, this.editedTags)
				.then((response) => {
					this.editingTags = null
					this.editedTags = []
				})
				.catch((err) => {
					console.error(err)
				})
				.finally(() => {
					publicationStore.getTags().then(({ response, data }) => {
						this.labelOptionsEdit.options = data
					})
					publicationStore.getPublicationAttachments(this.publication.id, { page: this.currentPage, limit: this.limit }).finally(() => {
						this.saveTagsLoading.splice(this.saveTagsLoading.indexOf(attachment.id), 1)
						this.fileIdsLoading.splice(this.fileIdsLoading.indexOf(attachment.id), 1)
					})

				})
		},
		fetchPublicationType(publicationTypeUrl, loading) {
			if (loading) this.publicationTypeLoading = true

			const validUrl = this.validUrl(publicationTypeUrl)

			if (validUrl) {
				fetch(`/index.php/apps/opencatalogi/api/publication_types?source=${publicationTypeUrl}`, {
					method: 'GET',
				})
					.then((response) => {
						response.json().then((data) => {
							this.publicationType = data.results[0]
							publicationStore.setPublicationPublicationType(data.results[0])
						})
						if (loading) { this.publicationTypeLoading = false }
					})
					.catch((err) => {
						console.error(err)
						if (loading) { this.publicationTypeLoading = false }
					})
			} else {
				fetch(`/index.php/apps/opencatalogi/api/publication_types?id=${publicationTypeUrl}`, {
					method: 'GET',
				})
					.then((response) => {
						response.json().then((data) => {
							this.publicationType = data.results[0]
							publicationStore.setPublicationPublicationType(data.results[0])
						})
						if (loading) { this.publicationTypeLoading = false }
					})
					.catch((err) => {
						console.error(err)
						if (loading) { this.publicationTypeLoading = false }
					})
			}
		},
		fetchThemes(themes, loading) {
			if (loading) { this.themesLoading = true }

			themeStore.refreshThemeList()
				.then(({ response, data }) => {
					this.themes = data

					if (loading) { this.themesLoading = false }
				})
				.catch((err) => {
					console.error(err)
					if (loading) { this.themesLoading = false }
				})
		},
		validUrl(url) {
			try {
				const test = new URL(url)
				return test.href
			} catch (err) {
				return false
			}
		},
		getTime() {
			const timeNow = new Date().toISOString()
			return timeNow
		},
		addAttachment() {
			publicationStore.setAttachmentItem([])
			navigationStore.setModal('AddAttachment')
		},
		deletePublication() {
			publicationStore.setPublicationItem(this.publication)
			navigationStore.setModal('deletePublication')
		},
		editPublicationDataItem(key) {
			publicationStore.setPublicationDataKey(key)
			navigationStore.setModal('editPublicationData')
		},
		deletePublicationDataItem(key) {
			publicationStore.setPublicationDataKey(key)
			navigationStore.setDialog('deletePublicationDataDialog')
		},
		editPublicationAttachmentItem(key) {
			publicationStore.setPublicationDataKey(key)
			navigationStore.setModal('editPublicationDataModal')
		},
		goToPublicationType() {
			publicationTypeStore.setPublicationTypeItem(this.publicationType)
			navigationStore.setSelected('publicationType')
		},
		goToOrganization() {
			organizationStore.setOrganizationItem(this.organization)
			navigationStore.setSelected('organizations')
		},
		goToCatalogi() {
			catalogiStore.setCatalogiItem(this.catalogi)
			navigationStore.setSelected('catalogi')
		},
		openLink(url, type = '') {
			window.open(url, type)
		},
		setActiveAttachment(attachment) {
			if (JSON.stringify(publicationStore.attachmentItem) === JSON.stringify(attachment)) {
				publicationStore.setAttachmentItem(false)
			} else { publicationStore.setAttachmentItem(attachment) }
		},
		setActiveDataKey(dataKey) {
			if (publicationStore.publicationDataKey === dataKey) {
				publicationStore.setPublicationDataKey(false)
			} else { publicationStore.setPublicationDataKey(dataKey) }
		},
		deleteMissingTheme(themeId) {
			this.deleteThemeLoading = true

			const newPublication = new Publication({
				...this.publication,
				themes: this.publication.themes.filter((theme) => theme !== themeId),
			})

			publicationStore.editPublication(newPublication)
				.then(() => (this.deleteThemeLoading = false))
		},
		/**
		 * Opens the folder URL in a new tab after parsing the encoded URL and converting to Nextcloud format
		 * @param {string} url - The encoded folder URL to open (e.g. "Open Registers\/Publicatie Register\/Publicatie\/123")
		 */
		openFolder(url) {
			// Parse the encoded URL by replacing escaped characters
			const decodedUrl = url.replace(/\\\//g, '/')

			// Ensure URL starts with forward slash
			const normalizedUrl = decodedUrl.startsWith('/') ? decodedUrl : '/' + decodedUrl

			// Construct the proper Nextcloud Files app URL with the normalized path
			// Use window.location.origin to get the current domain instead of hardcoding
			const nextcloudUrl = `${window.location.origin}/index.php/apps/files/files?dir=${encodeURIComponent(normalizedUrl)}`

			// Open URL in new tab
			window.open(nextcloudUrl, '_blank')
		},
		/**
		 * Opens a file in the Nextcloud Files app
		 * @param {object} file - The file object containing id, path, and other metadata
		 */
		openFile(file) {
			// Extract the directory path without the filename
			const dirPath = file.path.substring(0, file.path.lastIndexOf('/'))

			// Remove the '/admin/files/' prefix if it exists
			const cleanPath = dirPath.replace(/^\/admin\/files\//, '/')

			// Construct the proper Nextcloud Files app URL with file ID and openfile parameter
			const filesAppUrl = `/index.php/apps/files/files/${file.id}?dir=${encodeURIComponent(cleanPath)}&openfile=true`

			// Open URL in new tab
			window.open(filesAppUrl, '_blank')
		},
		/**
		 * Formats a file size in bytes to a human readable string
		 * @param {number} bytes - The file size in bytes
		 * @return {string} Formatted file size (e.g. "1.5 MB")
		 */
		 formatFileSize(bytes) {
			const sizes = ['Bytes', 'KB', 'MB', 'GB', 'TB']
			if (bytes === 0) return 'n/a'
			const i = parseInt(Math.floor(Math.log(bytes) / Math.log(1024)))
			if (i === 0 && sizes[i] === 'Bytes') return '< 1 KB'
			if (i === 0) return bytes + ' ' + sizes[i]
			return (bytes / Math.pow(1024, i)).toFixed(1) + ' ' + sizes[i]
		},
		toggleThemeSelection(theme) {
			if (this.selectedThemes.includes(theme.id)) {
				this.selectedThemes = this.selectedThemes.filter(id => id !== theme.id)
			} else {
				this.selectedThemes.push(theme.id)
			}
		},
		selectAllThemes() {
			const themes = this.filteredThemes?.map(theme => theme.id) || []
			if (!themes.length) return

			if (!this.allThemesSelected) {
				this.selectedThemes = themes
			} else {
				this.selectedThemes = []
			}
		},
		bulkDeleteThemes() {
			if (!this.selectedThemes.length) return
			navigationStore.setDialog('deleteMultipleThemes')
		},
		onBulkDeleteThemesDone() {
			navigationStore.setDialog(false)
			this.selectedThemes = []
		},
		onBulkDeleteThemesCancel() {
			navigationStore.setDialog(false)
		},
	},

}
</script>
<style>

.editingTags > div > a {
	height: auto !important;
}
</style>
<style scoped>
h4 {
	font-weight: bold;
}

.head {
	display: flex;
	justify-content: space-between;
}

.button {
	max-height: 10px;
}

.h1 {
	display: block !important;
	font-size: 2em !important;
	margin-block-start: 0.67em !important;
	margin-block-end: 0.67em !important;
	margin-inline-start: 0px !important;
	margin-inline-end: 0px !important;
	font-weight: bold !important;
	unicode-bidi: isolate !important;
}

.dataContent {
	display: flex;
	flex-direction: column;
}

.fileLabelsContainer {
	display: inline-flex;
	gap: 3px;
}

.active.publicationDetails-actionsDelete {
	background-color: var(--color-error) !important;
}

.active.publicationDetails-actionsDelete button {
	color: #EBEBEB !important;
}

.PublicationDetail-clickable {
	cursor: pointer !important;
}

.buttonLinkContainer {
	display: inline-flex;
	align-items: center;
}

.flex-hor {
	display: flex;
	gap: 4px;
}

.float-right {
	float: right;
}

.buttonsContainer {
	display: flex;
	gap: 10px;
	margin-block-end: 20px;
}

.fullWidthButton {
	width: 100%;
	max-width: 300px;
}

.selectedFileIcon {
	color: var(--color-primary);
}

.editTagsContainer {
	display: flex;
}

.editTagsSelect {
	max-width: 400px;
}

.editTagsButton {
	height: fit-content;
	align-self: center;
	margin-inline-start: 3px;
}
.linksParameters {
	margin-top: 25px;
}
.emptyStateMessage {
    margin-block-start: 15px;
    text-align: center;
    display: flex;
    justify-content: center;
}

.paginationContainer {
	display: flex;
	justify-content: space-between;
	align-items: center;
	margin-block-start: 10px;
}

.pagination {
	margin-block-start: 0px !important;
}

.limitSelect {
	margin-block-end: 0px;
}

.checkboxListActionButton {
	margin-inline-start: auto;
}
.checkedItem {
	display: flex;
	align-items: center;
}
</style>
