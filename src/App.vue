<template>
	<NcContent appName="opencatalog">
		<MainMenu selected="dashboard" />
		<NcAppContent>
			<template #default>
				<Dashboard />
			</template>
		</NcAppContent>
		<!-- <ZaakSidebar /> -->
	</NcContent>
</template>

<script>
import { NcAppContent, NcContent } from '@nextcloud/vue';
import isModalOpen from './modals/modalContext.js';
import MainMenu from './navigation/MainMenu.vue';
import Dashboard from './views/dashboard.vue';

export default {
	name: 'App',
	components: {
		NcContent,
		NcAppContent,
		MainMenu,
		Dashboard,
	},
}
</script>
