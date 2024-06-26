
<template>
	<NcContent appName="opencatalog">
		<MainMenu selected="catalogi"/>
		<NcAppContent>
			<template #list>
			</template>
			<template #default>
				<h1>Hello world!</h1>
			</template>
		</NcAppContent>
		<!-- <ZaakSidebar /> -->
	</NcContent>
</template>

<script>
import { NcAppContent, NcContent  } from '@nextcloud/vue';
import isModalOpen from './modals/modalContext.js';
import MainMenu from './navigation/MainMenu.vue';

export default {
	name: 'App',
	components: {
		NcContent,
		NcAppContent,
		MainMenu,
	},
}
</script>

