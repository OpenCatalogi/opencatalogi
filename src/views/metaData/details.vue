<template>
  <div>
    <div v-if="!loading" id="app-content" class="zakenDetailContainer">
      bla bla
      <!-- app-content-wrapper is optional, only use if app-content-list  -->
      <div class="zakenContainer">
        <h1 class="h1">{{ publication.name }}</h1>
        <div class="grid">
          <div class="gridContent">
            <h4>Sammenvatting:</h4>
            <span>{{ publication.summary }}</span>
          </div>          
        </div>
      </div>
    </div>
    <NcLoadingIcon v-if="loading" class="zakenDetailLoadingContainer" :size="100" appearance="dark"
      name="Publicatie details aan het laden" />
  </div>
</template>

<script>
import { BTabs, BTab } from 'bootstrap-vue'
import { NcLoadingIcon } from '@nextcloud/vue';

export default {
  name: "metadDataDetail",
  props: {
    metadDataId: {
      type: String,
      required: true
    },
  },
  watch: {
    metadDataId: {
      handler(metadDataId) {
        this.fetchData(metadDataId)
      },
      deep: true
    }
  },
  components: {
    NcLoadingIcon,
    BTabs,
    BTab,
  },
  data() {
    return {
      metadData: [],
      metadDataId: '',
      loading: false,
			activePublication: false,
			activePublicationId: '',
    }
  },
  mounted() {
    this.fetchData(this.metadDataId)
  },
  methods: {
    fetchData(id) {
      this.loading = true,
        fetch(
          `/index.php/apps/opencatalog/metadata/api/${id}`,
          {
            method: 'GET'
          },
        )
          .then((response) => {
            response.json().then((data) => {
              this.metadDataId = data
              //this.oldZaakId = id
            })
            this.loading = false
          })
          .catch((err) => {
            console.error(err)
            //this.oldZaakId = id
            this.loading = false
          })
    },
  },
}
</script>

<style>
.zakenDetailContainer,
.zakenDetailLoadingContainer {
  margin-block-start: var(--zaa-margin-20);
  margin-inline-start: var(--zaa-margin-20);
  margin-inline-end: var(--zaa-margin-20);
}

h4 {
  font-weight: bold
}

.h1 {
  display: block !important;
  font-size: 2em !important;
  margin-block-start: 0.67em !important;
  margin-block-end: 0.67em !important;
  margin-inline-start: 0px !important;
  margin-inline-end: 0px !important;
  font-weight: bold !important;
  unicode-bidi: isolate !important;
}

.zakenContainer {
  margin-block-start: var(--zaa-margin-20);
  margin-block-end: var(--zaa-margin-50);

}

.grid {
  display: grid;
  grid-gap: 24px;
  grid-template-columns: 1fr 1fr;
  margin-block-start: var(--zaa-margin-50);
  margin-block-end: var(--zaa-margin-50);
}

.gridContent {
  display: flex;
  gap: 25px;
}


.tabContainer>* ul>li {
  display: flex;
  flex: 1;
}

.tabContainer>* ul>li:hover {
  background-color: var(--color-background-hover);
}

.tabContainer>* ul>li>a {
  flex: 1;
  text-align: center;
}

.tabContainer>* ul>li>.active {
  background: transparent !important;
  color: var(--color-main-text) !important;
  border-bottom: var(--default-grid-baseline) solid var(--color-primary-element) !important;
}

.tabContainer>* ul {
  display: flex;
  margin: 10px 8px 0 8px;
  justify-content: space-between;
  border-bottom: 1px solid var(--color-border);
}

.tabPanel {
  padding: 20px 10px;
  min-height: 100%;
  max-height: 100%;
  height: 100%;
  overflow: auto;
}
</style>
