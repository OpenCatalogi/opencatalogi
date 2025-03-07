<script setup>
import { pageStore, navigationStore } from '../../store/store.js'
import { getTheme } from '../../services/getTheme.js'
import { EventBus } from '../../eventBus.js'
</script>

<template>
	<NcModal ref="modalRef"
		label-id="addPageContents"
		@close="closeModal">
		<div class="modal__content">
			<h2 v-if="!IS_EDIT">
				Content toevoegen aan {{ pageStore.pageItem.name }}
			</h2>
			<h2 v-else>
				{{ _.upperFirst(contentsItem.type) }} content bewerken van {{ pageStore.pageItem.name }}
			</h2>

			<div v-if="success !== null || error">
				<NcNoteCard v-if="success" type="success">
					<p>Content succesvol toegevoegd</p>
				</NcNoteCard>
				<NcNoteCard v-if="!success" type="error">
					<p>Er is iets fout gegaan bij het toevoegen van Content</p>
				</NcNoteCard>
				<NcNoteCard v-if="error" type="error">
					<p>{{ error }}</p>
				</NcNoteCard>
			</div>

			<div v-if="success === null" class="form-group">
				<p>
					De volgorde waarin je contents toevoegt maakt uit, let goed op de volgorde.
				</p>

				<NcSelect v-if="!IS_EDIT"
					v-bind="typeOptions"
					v-model="contentsItem.type"
					input-label="Content type"
					required />

				<!-- RichText -->
				<NcTextArea v-if="contentsItem.type === 'RichText'"
					:value.sync="contentsItem.richTextData"
					label="RichText"
					required />

				<!-- Faq -->
				<div v-if="contentsItem.type === 'Faq'">
					<VueDraggable v-model="contentsItem.faqData" easing="ease-in-out" draggable="div:not(:last-child)">
						<div v-for="item in contentsItem.faqData" :key="item.id" class="draggable-item-container">
							<div :class="`draggable-form-item ${getTheme()}`">
								<Drag class="drag-handle" :size="40" />
								<NcTextField label="Vraag" :value.sync="item.question" />
								<NcTextField label="Antwoord" :value.sync="item.answer" />
							</div>
						</div>
					</VueDraggable>
				</div>
			</div>

			<NcButton v-if="success === null"
				:disabled="!contentsItem.type || loading"
				type="primary"
				@click="addPageContent">
				<template #icon>
					<NcLoadingIcon v-if="loading" :size="20" />
					<Plus v-if="!loading" :size="20" />
				</template>
				{{ IS_EDIT ? 'Bewerken' : 'Toevoegen' }}
			</NcButton>
		</div>
	</NcModal>
</template>

<script>
import { NcButton, NcModal, NcLoadingIcon, NcNoteCard, NcSelect, NcTextArea, NcTextField } from '@nextcloud/vue'
import { VueDraggable } from 'vue-draggable-plus'
import _ from 'lodash'

import Plus from 'vue-material-design-icons/Plus.vue'
import Drag from 'vue-material-design-icons/Drag.vue'

import { Page } from '../../entities/index.js'

export default {
	name: 'AddPageContents',
	components: {
		NcModal,
		NcButton,
		NcLoadingIcon,
		NcNoteCard,
		NcSelect,
		NcTextArea,
		VueDraggable,
		NcTextField,
		// Icons
		Plus,
	},
	data() {
		return {
			IS_EDIT: !!pageStore.contentId,
			contentsItem: {
				type: '',
				richTextData: '',
				id: Math.random().toString(36).substring(2, 12),
				faqData: [
					{
						id: Math.random().toString(36).substring(2, 12),
						question: '',
						answer: '',
					},
				],
			},
			typeOptions: {
				options: ['RichText', 'Faq'],
			},
			loading: false,
			success: null,
			error: false,
			errorCode: '',
			hasUpdated: false,
		}
	},
	watch: {
		'contentsItem.faqData': {
			handler(newVal) {
				const currentFaqLength = newVal.length

				// check if last item is full, then add a new one to the list
				if (newVal[currentFaqLength - 1].question !== '' || newVal[currentFaqLength - 1].answer !== '') {
					newVal.push({
						id: Math.random().toString(36).substring(2, 12),
						question: '',
						answer: '',
					})
				}

				// Remove any empty FAQ items except the last one
				if (currentFaqLength > 1) {
					for (let i = currentFaqLength - 2; i >= 0; i--) {
						if (newVal[i].question === '' && newVal[i].answer === '') {
							newVal.splice(i, 1)
						}
					}
				}
			},
			deep: true,
		},
	},
	mounted() {
		if (pageStore.contentId) {
			const contentItem = pageStore.pageItem.contents.find((content) => content.id === pageStore.contentId)

			// put in all data that does not require special handeling
			this.contentsItem = {
				...this.contentsItem,
				type: contentItem.type,
				richTextData: contentItem.data.content || '',
				id: contentItem.id,
			}

			// if faqs are present, prepend them to the contentsItem
			if (contentItem.data.faqs && contentItem.data.faqs.length > 0) {
				this.contentsItem.faqData = contentItem.data.faqs.map((faq) => ({
					id: Math.random().toString(36).substring(2, 12),
					question: faq.question,
					answer: faq.answer,
				})).concat(this.contentsItem.faqData)
			}
		}
	},
	methods: {
		closeModal() {
			navigationStore.setModal(false)
			pageStore.contentId = null
		},
		addPageContent() {
			this.loading = true

			const pageItemClone = _.cloneDeep(pageStore.pageItem)

			// Create the content item
			// a different data format is needed for the type of content
			let contentItem
			if (this.contentsItem.type === 'RichText') {
				contentItem = {
					type: this.contentsItem.type,
					id: this.contentsItem.id || Math.random().toString(36).substring(2, 12),
					data: {
						content: this.contentsItem.richTextData,
					},
				}
			} else if (this.contentsItem.type === 'Faq') {
				contentItem = {
					type: this.contentsItem.type,
					id: this.contentsItem.id || Math.random().toString(36).substring(2, 12),
					data: {
						// remove the last item since it's a placeholder and is always empty no matter what
						faqs: this.contentsItem.faqData.slice(0, -1).map((faq) => ({
							question: faq.question,
							answer: faq.answer,
						})),
					},
				}
			}

			if (!Array.isArray(pageItemClone.contents)) {
				pageItemClone.contents = []
			}

			// Check if it's an edit modal by checking if contentId exists
			if (pageStore.contentId) {
				const index = pageItemClone.contents.findIndex(content => content.id === pageStore.contentId)
				if (index !== -1) {
					pageItemClone.contents[index] = contentItem
				}
			} else {
				pageItemClone.contents.push(contentItem)
			}

			const newPageItem = new Page(pageItemClone)
			pageStore.savePage(newPageItem)
				.then(({ response }) => {
					this.success = response.ok

					// Wait for the user to read the feedback then close the model
					setTimeout(this.closeModal, 2000)

					EventBus.$emit('edit-page-content-success')

					this.hasUpdated = false
				})
				.catch((err) => {
					this.error = err
					this.loading = false
					this.hasUpdated = false
				})
				.finally(() => {
					this.loading = false
				})
		},
	},
}
</script>

<style>
.modal__content {
    margin: var(--OC-margin-50);
    text-align: center;
}

.zaakDetailsContainer {
    margin-block-start: var(--OC-margin-20);
    margin-inline-start: var(--OC-margin-20);
    margin-inline-end: var(--OC-margin-20);
}

.success {
    color: green;
}
</style>

<style scoped>
.draggable-form-item {
    display: flex;
    align-items: center;
    gap: 3px;

    background-color: rgba(255, 255, 255, 0.05);
    padding: 4px;
    border-radius: 12px;

    margin-block: 8px;
}
.draggable-form-item.light {
    background-color: rgba(0, 0, 0, 0.05);
}
.draggable-form-item :deep(.v-select) {
    min-width: 150px;
}
.draggable-form-item :deep(.input-field__label) {
    margin-block-start: 0 !important;
}
.draggable-form-item .input-field {
    margin-block-start: 0 !important;
}

.draggable-item-container:last-child .drag-handle {
    cursor: not-allowed;
}
</style>
