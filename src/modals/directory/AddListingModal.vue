<script setup>
import { navigationStore, directoryStore } from '../../store/store.js'
</script>

<template>
    <NcModal
        v-if="navigationStore.modal === 'addListing'"
        ref="modalRef"
        label-id="addListingModal"
        @close="navigationStore.setModal(false)"
    >
        <div class="modal__content">
            <h2>Directory toevoegen</h2>
            <div v-if="success !== null || error">
                <NcNoteCard v-if="success" type="success">
                    <p>Listing succesvol toegevoegd</p>
                </NcNoteCard>
                <NcNoteCard v-if="!success" type="error">
                    <p>Er is iets fout gegaan bij het toevoegen van Listing</p>
                </NcNoteCard>
                <NcNoteCard v-if="error && !success" type="error">
                    <p>{{ error }}</p>
                </NcNoteCard>
            </div>
            <div v-if="success === null" class="form-group">
                <NcNoteCard v-if="validateUrlError" type="error">
                    <p>Er is geen valide URL ingevoerd.</p>
                </NcNoteCard>
                <NcTextField label="Url" v-model="directory.url" @input="validateUrl" />
            </div>
            <NcButton
                v-if="success === null"
                :disabled="!isUrlValid || loading || !directory.url"
                type="primary"
                @click="addDirectory"
            >
                <template #icon>
                    <NcLoadingIcon v-if="loading" :size="20" />
                    <ContentSaveOutline v-if="!loading" :size="20" />
                </template>
                Submit
            </NcButton>
        </div>
    </NcModal>
</template>

<script>
import { NcButton, NcModal, NcTextField, NcLoadingIcon, NcNoteCard } from '@nextcloud/vue';
import ContentSaveOutline from 'vue-material-design-icons/ContentSaveOutline.vue';

export default {
    name: 'AddListingModal',
    components: {
        NcModal,
        NcTextField,
        NcButton,
        NcLoadingIcon,
        NcNoteCard,
        ContentSaveOutline,
    },
    data() {
        return {
            directory: {
                url: '',
            },
            loading: false,
            success: null,
            error: false,
            validateUrlError: null,
            urlPattern: new RegExp(
                '^(https?:\\/\\/)' + // protocol
                '((([a-z\\d]([a-z\\d-]*[a-z\\d])*)\\.?)+[a-z]{2,}|' + // domain name
                '((\\d{1,3}\\.){3}\\d{1,3}))' + // OR ip (v4) address
                '(\\:\\d+)?(\\/[-a-z\\d%_.~+]*)*' + // port and path
                '(\\?[;&a-z\\d%_.~+=-]*)?' + // query string
                '(\\#[-a-z\\d_]*)?$',
                'i'
            ), // fragment locator
            testUrls: [
                'http://example.com',
                'https://example.com',
                'http://www.example.com',
                'https://www.example.com/path?query=string#fragment',
                'test',
                'http://',
                'http://test',
                'http::'
            ]
        };
    },
    computed: {
        isUrlValid() {
            return this.urlPattern.test(this.directory.url);
        }
    },
    methods: {
        addDirectory() {
            this.loading = true;
            this.$emit('metadata', this.title);
            fetch('/index.php/apps/opencatalogi/api/directory', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify({
                    title: this.title,
                    summary: this.summary,
                    description: this.description,
                    search: this.search,
                    metadata: this.metadata,
                    status: this.status,
                    lastSync: this.lastSync,
                    default: this.defaultValue,
                }),
            })
            .then((response) => {
                this.loading = false;
                this.success = response.ok;
                directoryStore.refreshListingList();
                response.json().then((data) => {
                    directoryStore.setListingItem(data);
                });
                navigationStore.setSelected('directory');
                setTimeout(() => {
                    this.success = null;
                    this.closeModal();
                }, 2500);
            })
            .catch((err) => {
                this.error = err;
                this.loading = false;
            });
        },
        closeModal() {
            navigationStore.setModal(false);
        },
        validateUrl(event) {
            this.testUrls.forEach(url => {
                console.log(`${url}: ${this.urlPattern.test(url)}`);
            });
            this.directory.url = event.target.value;
            if (!this.isUrlValid) {
                this.validateUrlError = 'Er is geen valide URL ingevoerd.';
            } else {
                this.validateUrlError = null;
            }
        }
    },
};
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
