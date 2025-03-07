<script setup>
import { navigationStore, searchStore, themeStore } from '../../store/store.js'
</script>

<template>
	<NcAppContent>
		<template #list>
			<ThemeList :search="searchStore.search" />
		</template>
		<template #default>
			<NcEmptyContent v-if="!themeStore.themeItem?.id || navigationStore.selected != 'themes'"
				class="detailContainer"
				name="Geen thema"
				description="Nog geen thema geselecteerd">
				<template #icon>
					<ShapeOutline />
				</template>
				<template #action>
					<NcButton type="primary" @click="navigationStore.setModal('themeAdd')">
						Thema toevoegen
					</NcButton>
				</template>
			</NcEmptyContent>
			<ThemeDetail v-if="themeStore.themeItem?.id && navigationStore.selected === 'themes'" :theme-item="themeStore.themeItem" />
		</template>
	</NcAppContent>
</template>

<script>
import { NcAppContent, NcEmptyContent, NcButton } from '@nextcloud/vue'
import ThemeList from './ThemeList.vue'
import ThemeDetail from './ThemeDetail.vue'
import ShapeOutline from 'vue-material-design-icons/ShapeOutline.vue'

export default {
	name: 'ThemeIndex',
	components: {
		NcAppContent,
		NcEmptyContent,
		ThemeList,
		ThemeDetail,
		NcButton,
		// Icons
		ShapeOutline,
	},
	data() {
		return {

		}
	},
}
</script>
