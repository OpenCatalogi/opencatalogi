<template>
	<!-- Placehodel div for all of the vieuws-->
	<div>		
		<Catalogi v-if="selected === 'catalogi'" :selected="selected" :item="item" :modal="modal" @selected="setSelected" @item="setItem" @modal="setModal" />
		<Dashboard v-if="selected === ''" :selected="selected" :item="item" :modal="modal" @selected="setSelected" @item="setItem" @modal="setModal" />
		<Directory v-if="selected === 'directory'" :selected="selected" :item="item" :modal="modal" @selected="setSelected" @item="setItem" @modal="setModal" />
		<Matadata v-if="selected === 'metaData'" :selected="selected" :item="item" :modal="modal" @selected="setSelected" @item="setItem" @modal="setModal" />
		<Search v-if="selected === 'search'" :selected="selected" :item="item" :modal="modal" @selected="setSelected" @item="setItem" @modal="setModal" />
	</div>
</template>

<script>
import Catalogi from './views/catalogi/index.vue'
import Dashboard from './views/dashboard/index.vue'
import Directory from './views/directory/index.vue'
import Matadata from './views/metData/index.vue'
import Publications from './views/publications/index.vue'
import Search from './views/search/index.vue'

export default {
	name: 'Modals',
	components: {
		Catalogi,
		Dashboard,
		Directory,
		Matadata,
		Publications,
		Search
	},
	props: {
		modal: {
			type: string,
			required: true,
		},
		item: {
			type: string,
			required: true,
		},
		selected: {
			type: string,
			required: true,
		}
	},
	methods: {
		setModal(modal) {
			this.modal = modal
			this.$emit('modal', modal)
		},			
		setSelected(selected) {
			this.selected = selected
			this.$emit('selected', selected)
		},
		setItem(item) {
			this.item = item
			this.$emit('item', item)
		}
	},
}
</script>


