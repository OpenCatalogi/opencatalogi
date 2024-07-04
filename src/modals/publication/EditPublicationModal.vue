<script setup>
import { store } from '../../store.js'
</script>
<template>
	<NcModal
		v-if="store.modal === 'publicationEdit'"
		ref="modalRef"
		@close="store.setModal(false)">
		<div class="modal__content">
			<h2>Edit publication</h2>
			<h1>title: {{ publication.title }}</h1>
			<div class="form-group">
				<NcTextField label="Naam" :value.sync="publication.title" :loading="publicationLoading" />
			</div>
			<div class="form-group">
				<NcTextArea label="Beschrijving" :value.sync="publication.description" />
			</div>
			<div class="form-group">
				<NcSelect v-bind="catalogi"
					v-model="catalogi.value"
					:loading="catalogiLoading"
					required
					:value.sync="test" />
			</div>
			<div class="form-group">
				<NcSelect v-bind="metaData"
					v-model="metaData.value"
					:loading="metaDataLoading"
					required
					:value.sync="test2" />
			</div>
			<div v-if="succesMessage" class="success">
				Succesfully added publication
			</div>

			<NcButton :disabled="!title" type="primary" @click="addPublication">
				Submit
			</NcButton>
		</div>
	</NcModal>
</template>

<script>
import {
	NcButton,
	NcModal,
	NcTextField,
	NcTextArea,
	NcSelect,
} from '@nextcloud/vue'

export default {
	name: 'EditPublicationModal',
	components: {
		NcModal,
		NcTextField,
		NcTextArea,
		NcButton,
		NcSelect,
	},
	data() {
		return {
			publication: {
				title: '',
				description: '',
			},
			catalogi: {},
			metaData: {},
			succesMessage: false,
			catalogiLoading: false,
			metaDataLoading: false,
			hasUpdated: false,
			publicationLoading: false,
		}
	},
	updated() {
		if (!this.hasUpdated) {
			this.fetchData(store.publicationItem)
			this.fetchCatalogi()
			this.fetchMetaData()
			this.hasUpdated = true
		}
	},
	methods: {
		fetchData(id) {
			this.publicationLoading = true
			fetch(
				`/index.php/apps/opencatalog/publications/api/${id}`,
				{
					method: 'GET',
				},
			)
				.then((response) => {
					response.json().then((data) => {
						this.publication = data
						console.log(data)
						// this.oldZaakId = id
					})
					this.publicationLoading = false
				})
				.catch((err) => {
					console.error(err)
					// this.oldZaakId = id
					this.publicationLoading = false
				})
		},
		fetchCatalogi() {
			this.catalogiLoading = true
			fetch('/index.php/apps/opencatalog/catalogi/api', {
				method: 'GET',
			})
				.then((response) => {
					response.json().then((data) => {

						this.catalogi = {
							inputLabel: 'Catalogi',
							options: Object.entries(data.results).map((catalog) => ({
								id: catalog[0],
								label: catalog[1].name,
							})),

						}
					})
					this.catalogiLoading = false
				})
				.catch((err) => {
					console.error(err)
					this.catalogiLoading = false
				})
		},
		fetchMetaData() {
			this.metaDataLoading = true
			fetch('/index.php/apps/opencatalog/metadata/api', {
				method: 'GET',
			})
				.then((response) => {
					response.json().then((data) => {

						this.metaData = {
							inputLabel: 'MetaData',
							options: Object.entries(data.results).map((metaData) => ({
								id: metaData[0],
								label: metaData[1].name,
							})),

						}
					})
					this.metaDataLoading = false
				})
				.catch((err) => {
					console.error(err)
					this.metaDataLoading = false
				})
		},
		closeModal() {
			store.modal = false
		},
		addPublication() {
			this.$emit('publication', this.name)
			fetch(
				'/index.php/apps/opencatalog/publications/api',
				{
					method: 'POST',
					body: JSON.stringify({
						title: this.title,
						description: this.description,
						catalogi: this.catalogi.value.id,
						metaData: this.metaData.value.id,
					}),
				},
			)
				.then((response) => {
					this.succesMessage = true
					setTimeout(() => (this.succesMessage = false), 2500)
				})
				.catch((err) => {
					console.error(err)
				})
		},
	},
}
</script>

<style>
.modal__content {
  margin: var(--zaa-margin-50);
  text-align: center;
}

.zaakDetailsContainer {
  margin-block-start: var(--zaa-margin-20);
  margin-inline-start: var(--zaa-margin-20);
  margin-inline-end: var(--zaa-margin-20);
}

.success {
  color: green;
}
</style>
