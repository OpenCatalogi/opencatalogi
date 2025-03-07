<script setup>
import { navigationStore, searchStore, menuStore } from '../../store/store.js'
</script>

<template>
	<NcAppContent>
		<template #list>
			<MenuList :search="searchStore.search" />
		</template>
		<template #default>
			<NcEmptyContent v-if="!menuStore.menuItem?.id || navigationStore.selected != 'menus'"
				class="detailContainer"
				name="Geen menu"
				description="Nog geen menu geselecteerd">
				<template #icon>
					<MenuClose />
				</template>
				<template #action>
					<NcButton type="primary" @click="menuStore.setMenuItem(false); navigationStore.setModal('editMenu')">
						Menu toevoegen
					</NcButton>
				</template>
			</NcEmptyContent>
			<MenuDetail v-if="menuStore.menuItem?.id && navigationStore.selected === 'menus'" />
		</template>
	</NcAppContent>
</template>

<script>
import { NcAppContent, NcEmptyContent, NcButton } from '@nextcloud/vue'
import MenuList from './MenuList.vue'
import MenuDetail from './MenuDetail.vue'
import MenuClose from 'vue-material-design-icons/MenuClose.vue'

export default {
	name: 'MenuIndex',
	components: {
		NcAppContent,
		NcEmptyContent,
		MenuList,
		MenuDetail,
		NcButton,
		// Icons
		MenuClose,
	},
	data() {
		return {

		}
	},
}
</script>
