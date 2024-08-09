<script setup>
import { searchStore } from '../../store/store.js'
</script>

<template>
	<NcAppContent>
		<h2 class="pageHeader">
			Directory
		</h2>
		<DirectoryList :search="searchStore.search" />
	</NcAppContent>
</template>

<script>
import { NcAppContent } from '@nextcloud/vue'
import DirectoryList from './DirectoryList.vue'

export default {
	name: 'DirectoryIndex',
	components: {
		NcAppContent,
		DirectoryList,
	},
	data() {
		return {

		}
	},
	methods: {
		openLink(url, type = '') {
			window.open(url, type)
		},
	},
}
</script>
