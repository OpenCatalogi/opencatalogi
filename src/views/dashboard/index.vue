<template>
	<div class="dashboardContainer">
		<h1>Titel Dashboard</h1>
		<p>grafiek hier</p>
	</div>
</template>
<script>
export default {
	name: 'Dashboard',
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
	},,
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
<style>
.dashboardContainer {
    margin-inline-start: 75px;
    margin-block-start: 20px
}
</style>
