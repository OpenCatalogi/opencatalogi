<script setup>
import { store } from '../../store.js'
</script>

<template>
	<NcAppSidebar
		name="Snelle start"
		subname="Schakel snel naar waar u nodig bent">
		<NcAppSidebarTab id="search-tab" name="Zoeken" :order="1">
			<template #icon>
				<Magnify :size="20" />
			</template>
			Zoek snel in het voor uw beschickbare federatieve netwerk
			<NcTextField class="searchField"
				:value.sync="store.search"
				label="Search" />
		</NcAppSidebarTab>
		<NcAppSidebarTab id="settings-tab" name="Publicaties" :order="2">
			<template #icon>
				<ListBoxOutline :size="20" />
			</template>
			Welke publicaties vereisen uw aandacht?
			<NcListItem v-for="(publication, i) in store.conceptPublications.results"
				:key="`${publication}${i}`"
				:name="publication.name ?? publication.title"
				:bold="false"
				:force-display-actions="true"
				:active="store.publicationItem.id === publication.id"
				:details="publication?.status">
				<template #icon>
					<ListBoxOutline :class="store.publicationItem.id === publication.id && 'selectedZaakIcon'"
						disable-menu
						:size="44" />
				</template>
				<template #subname>
					{{ publication?.description }} wdsfdf
				</template>
				<template #actions>
					<NcActionButton @click="store.setPublicationItem(publication); store.setSelected('publication');">
						<template #icon>
							<ListBoxOutline :size="20" />
						</template>
						Bekijken
					</NcActionButton>
					<NcActionButton @click="store.setPublicationItem(publication); store.setModal('editPublication')">
						<template #icon>
							<Pencil :size="20" />
						</template>
						Bewerken
					</NcActionButton>
					<NcActionButton @click="store.setPublicationItem(publication); store.setDialog('publishPublication')">
						<template #icon>
							<Publish :size="20" />
						</template>
						Publiseren
					</NcActionButton>
					<NcActionButton @click="store.setPublicationItem(publication); store.setDialog('deletePublication')">
						<template #icon>
							<Delete :size="20" />
						</template>
						Verwijderen
					</NcActionButton>
				</template>
			</NcListItem>
			<NcNoteCard v-if="!store.conceptPublications.results.length > 0" type="success">
				<p>Er zijn op dit moment geen publicaties die uw aandacht vereisen</p>
			</NcNoteCard>
		</NcAppSidebarTab>
		<NcAppSidebarTab id="share-tab" name="Bijlagen" :order="3">
			<template #icon>
				<FileOutline :size="20" />
			</template>
			Welke bijlagen vereisen uw aandacht?
			<NcListItem v-for="(attachment, i) in store.conceptAttachments.results"
				:key="`${attachment}${i}`"
				:name="attachment.name ?? attachment.title"
				:bold="false"
				:force-display-actions="true"
				:active="store.attachmentItem.id === attachment.id"
				:details="attachment?.status">
				<template #icon>
					<ListBoxOutline :class="store.publicationItem.id === attachment.id && 'selectedZaakIcon'"
						disable-menu
						:size="44" />
				</template>
				<template #subname>
					{{ publication?.description }}
				</template>
				<template #actions>
					<NcActionButton @click="store.setAttachmentItem(attachment); store.setModal('editAttachment')">
						<template #icon>
							<Pencil :size="20" />
						</template>
						Bewerken
					</NcActionButton>
					<NcActionButton @click="store.setAttachmentItem(attachment); store.setDialog('publishAttachment')">
						<template #icon>
							<Publish :size="20" />
						</template>
						Publiseren
					</NcActionButton>
					<NcActionButton @click="store.setAttachmentItem(attachment); store.setDialog('deleteAttachment')">
						<template #icon>
							<Delete :size="20" />
						</template>
						Verwijderen
					</NcActionButton>
				</template>
			</NcListItem>
			<NcNoteCard v-if="!store.conceptAttachments.results.length > 0" type="success">
				<p>Er zijn op dit moment geen bijlagen die uw aandacht vereisen</p>
			</NcNoteCard>
		</NcAppSidebarTab>
	</NcAppSidebar>
</template>
<script>

import { NcAppSidebar, NcAppSidebarTab, NcTextField, NcNoteCard, NcListItem, NcActionButton } from '@nextcloud/vue'
import Magnify from 'vue-material-design-icons/Magnify.vue'
import ListBoxOutline from 'vue-material-design-icons/ListBoxOutline.vue'
import FileOutline from 'vue-material-design-icons/FileOutline.vue'
import Pencil from 'vue-material-design-icons/Pencil.vue'
import Publish from 'vue-material-design-icons/Publish.vue'
import Delete from 'vue-material-design-icons/Delete.vue'

export default {
	name: 'DashboardSideBar',
	components: {
		NcAppSidebar,
		NcAppSidebarTab,
		NcTextField,
		NcNoteCard,
		NcListItem,
		NcActionButton,
		// Icons
		Magnify,
		ListBoxOutline,
		FileOutline,
		Pencil,
		Publish,
		Delete,
	},
	data() {
		return {
			publications: false,
			attachments: false,
		}
	},
	mounted() {
		store.getConceptPublications()
		store.getConceptAttachments()
	},
}
</script>
