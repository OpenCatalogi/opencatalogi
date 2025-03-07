<script setup>
import { navigationStore, searchStore, pageStore } from '../../store/store.js'
</script>

<template>
	<NcAppContent>
		<template #list>
			<PageList :search="searchStore.search" />
		</template>
		<template #default>
			<NcEmptyContent v-if="!pageStore.pageItem?.id || navigationStore.selected != 'pages'"
				class="detailContainer"
				name="Geen pagina"
				description="Nog geen pagina geselecteerd">
				<template #icon>
					<Web />
				</template>
				<template #action>
					<NcButton type="primary" @click="pageStore.setPageItem(null); navigationStore.setModal('pageForm')">
						Pagina toevoegen
					</NcButton>
				</template>
			</NcEmptyContent>
			<PageDetail v-if="pageStore.pageItem?.id && navigationStore.selected === 'pages'" :page-item="pageStore.pageItem" />
		</template>
	</NcAppContent>
</template>

<script>
import { NcAppContent, NcEmptyContent, NcButton } from '@nextcloud/vue'
import PageList from './PageList.vue'
import PageDetail from './PageDetail.vue'
import Web from 'vue-material-design-icons/Web.vue'

export default {
	name: 'PageIndex',
	components: {
		NcAppContent,
		NcEmptyContent,
		PageList,
		PageDetail,
		NcButton,
		// Icons
		Web,
	},
	data() {
		return {

		}
	},
}
</script>
