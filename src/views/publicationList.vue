<template>
	<NcAppContentList>
	  <ul>
      <div class="listHeader">
        <NcTextField class="searchField" disabled :value.sync="search" label="Search" trailing-button-icon="close"
          :show-trailing-button="search !== ''" @trailing-button-click="clearText">
          <Magnify :size="20" />
        </NcTextField>
      </div>      
	  
	  <NcLoadingIcon v-if="loading" class="loadingIcon" :size="64" appearance="dark"
        name="Zaken aan het laden" />
    </ul>
  </NcAppContentList>
</template>

<script>

import { NcListItem, NcListItemIcon, NcActionButton, NcAvatar, NcAppContentList, NcTextField, NcLoadingIcon } from '@nextcloud/vue';

import MainMenu from './navigation/MainMenu.vue';

export default {
	name: 'PublicationList',
	components: {
		NcListItem,
		NcListItemIcon,
		NcActionButton,
		NcAvatar,
		NcAppContentList,
		NcTextField,
		NcLoadingIcon
	},
}
</script>


<style>
.listHeader {
  position: sticky;
  top: 0;
  z-index: 1000;
  background-color: var(--color-main-background);
  border-bottom: 1px solid var(--color-border);
}

.searchField {
  padding-inline-start: 65px;
  padding-inline-end: 20px;
  margin-block-end: 6px;
}

.selectedZaakIcon>svg {
  fill: white;
}

.loadingIcon {
  margin-block-start: var(--zaa-margin-20);
}
</style>

