/* eslint-disable no-console */
import { Attachment, Publication } from "../../entities/index.js";
import { defineStore } from "pinia";
import { PublicationsService } from "../../services/publications";

export const usePublicationStore = defineStore("publication", {
  state: () => ({
    publicationItem: false,
    publicationList: [],
    publicationDataKey: false,
    attachmentItem: false,
    publicationAttachments: [],
    conceptPublications: [],
    conceptAttachments: [],
  }),
  actions: {
    setPublicationItem(publicationItem) {
      // To prevent forms etc from braking we alway use a default/skeleton object
      this.publicationItem = new Publication(publicationItem);
      console.log("Active publication item set to " + publicationItem.id);
    },
    setPublicationList(publicationList) {
      this.publicationList = publicationList;
      console.log("Active publications fetched for a total of " + publicationList?.length ?? 0 + " publications");
    },
    refreshPublicationList() {
      this.setPublicationList(PublicationsService.getAll());
    },
    getPublicationAttachments(publication) {
      // @todo this might belong in a service?
      fetch("/index.php/apps/opencatalogi/api/attachments", {
        method: "GET",
      })
        .then((response) => {
          response.json().then((data) => {
            this.publicationAttachments = data.results.map(
              (attachmentItem) => new Attachment(attachmentItem)
            );
            return data;
          });
        })
        .catch((err) => {
          console.error(err);
          return err;
        });
    },
    getConceptPublications() {
      // @todo this might belong in a service?
      fetch("/index.php/apps/opencatalogi/api/publications?status=concept", {
        method: "GET",
      })
        .then((response) => {
          response.json().then((data) => {
            this.conceptPublications = data;
            return data;
          });
        })
        .catch((err) => {
          console.error(err);
          return err;
        });
    },
    getConceptAttachments() {
      // @todo this might belong in a service?
      fetch("/index.php/apps/opencatalogi/api/attachments?status=concept", {
        method: "GET",
      })
        .then((response) => {
          response.json().then((data) => {
            this.conceptAttachments = data;
            return data;
          });
        })
        .catch((err) => {
          console.error(err);
          return err;
        });
    },
    // @todo why does the following run through the store? -- because its impossible with props, and its vital information for the modal.
    setPublicationDataKey(publicationDataKey) {
      this.publicationDataKey = publicationDataKey;
      console.log("Active publication data key set to " + publicationDataKey);
    },
    setAttachmentItem(attachmentItem) {
      this.attachmentItem = new Attachment(attachmentItem);
      console.log("Active attachment item set to " + attachmentItem);
      console.log(attachmentItem);
    },
  },
});
