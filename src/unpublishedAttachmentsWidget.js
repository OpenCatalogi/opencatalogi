import Vue from 'vue'
import UnpublishedAttachmentsWidget from './views/widgets/UnpublishedAttachmentsWidget.vue'

OCA.Dashboard.register('opencatalogi_unpublished_attachments_widget', async (el, { widget }) => {
	Vue.mixin({ methods: { t, n } })
	const View = Vue.extend(UnpublishedAttachmentsWidget)
	new View({
		propsData: { title: widget.title },
	}).$mount(el)
})
