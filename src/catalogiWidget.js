import Vue from 'vue'
import CatalogiWidget from './views/widgets/CatalogiWidget.vue'

OCA.Dashboard.register('opencatalogi_catalogi_widget', async (el, { widget }) => {
	Vue.mixin({ methods: { t, n } })
	const View = Vue.extend(CatalogiWidget)
	new View({
		propsData: { title: widget.title },
	}).$mount(el)
})
