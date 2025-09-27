<template>
    <div
      v-if="isModalOpen"
      class="fixed inset-0 z-100 flex items-start justify-center bg-black bg-opacity-50 pt-[160px] overflow-y-auto mb-[50px]"
    >
      <div
        class="bg-layout-sun-100 dark:bg-layout-night-100 rounded-lg shadow-lg w-full max-w-lg p-6 max-h-[calc(100vh-200px)]"
      >
        <!-- Tabs & Inhalte -->
        <div v-if="!pxa">
          <!-- Tabs -->
          <div v-if="is_imgdir" class="mb-4 border-b border-layout-sun-300 dark:border-layout-night-300">
            <nav class="-mb-px flex space-x-8" aria-label="Tabs">
              <button :class="tabClass('upload')" @click="activeTab = 'upload'" type="button">
                Upload
              </button>
              <button :class="tabClass('gallery')" @click="activeTab = 'gallery'" type="button">
                Galerieverwaltung
              </button>
            </nav>
          </div>

          <!-- Upload Tab -->
          <div v-show="activeTab === 'upload'">
            <form @submit.prevent="uploadImage">
              <h3 class="text-2xl font-semibold text-center mb-4">Bild hochladen</h3>

              <CopyLeftSelect
                v-if="isImages && !this.Message && !column.includes('img_thumb')"
                v-model="form.copyleft"
                label="Wasserzeichen"
                name="copyleft"
              />

              <!-- Dropzone -->
              <div
                class="border-2 border-dashed border-layout-sun-500 dark:border-layout-night-500 rounded-lg p-6 text-center cursor-pointer hover:bg-layout-sun-200 dark:hover:bg-layout-night-200"
                @dragover.prevent
                @drop.prevent="handleDrop"
                @click="triggerFileInput"
              >
                <input
                  ref="fileInput"
                  multiple
                  type="file"
                  accept="image/*"
                  class="hidden"
                  @change="handleFileChange"
                />
                <p class="text-layout-sun-700 dark:text-layout-night-700">
                  Ziehe Bilder hierher oder klicke, um Dateien auszuwählen.
                </p>
              </div>

              <!-- Vorschau -->
              <div v-if="previewImages[column]" class="mt-4 text-center">
                <img
                  :src="previewImages[column]"
                  alt="Bildvorschau"
                  class="w-full h-auto rounded-lg shadow-md"
                />
              </div>

              <!-- Upload-Fortschritt -->
              <div v-if="uploading" class="mt-4">
                <progress :value="progress" max="100" class="w-full"></progress>
                <p class="text-center mt-2">{{ progress }}%</p>
              </div>

              <!-- Buttons -->
              <div class="mt-6 flex justify-between">
                <button
                  type="button"
                  @click="closeModal"
                  class="px-4 py-2 bg-red-500 text-white rounded-lg hover:bg-red-600"
                >
                  Schliessen
                </button>
                <button
                  type="submit"
                  v-if="selectedImages[column]"
                  class="px-4 py-2 bg-blue-500 text-white rounded-lg hover:bg-blue-600"
                >
                  Hochladen
                </button>
              </div>
            </form>
          </div>

          <!-- Galerieverwaltung -->
          <div v-show="activeTab === 'gallery' && is_imgdir">
            <ImageJsonEditor
              :folder="path"
              :column="column"
              @jsonUpdated="onJsonUpdated"
              @imageUploaded="refreshGallery"
              @refresh-gallery="$emit('refresh-preview')"
              ref="editor2"
              @close="closeModal"
            />
          </div>
        </div>

        <!-- Alternative Ansicht, wenn pxa gesetzt ist -->
        <div v-else class="space-y-4">
          <label
            class="block text-sm font-medium text-layout-sun-700 dark:text-layout-night-300"
          >
            Bilderordner
          </label>

          <input
            type="text"
            v-model="field.value"
            id="folder_save"
            class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-800 dark:text-white"
          />
          <div class="flex justify-between mt-6">
            <button
              type="button"
              @click="closeModal"
              class="px-4 py-2 bg-red-500 text-white rounded-lg hover:bg-red-600"
            >
              Schliessen
            </button>

            <button
              type="button"
              @click="savedir"
              class="px-4 py-2 bg-blue-500 text-white rounded-lg hover:bg-blue-600"
            >
              Weiter
            </button>
          </div>
        </div>
      </div>
    </div>
  </template>

  <script>
  import { CleanTable as cleanTableFn, GetAuth } from '@/helpers';
  import CopyLeftSelect from '@/Application/Components/Content/CopyLeftSelect.vue';
  import ImageJsonEditor from '@/Application/Admin/ImageJsonEditor.vue';

  export default {
    name: 'ImageUploadModal',
    components: {
      CopyLeftSelect,
      ImageJsonEditor,
    },
    props: {
      is_imgdir: { type: Boolean, default: false },
      isModalOpen: { type: [Boolean, Number, String], default: false },
      column: String,
      domain: String,
      path: String,
      namee: String,
      namee2: String,
      Message: { type: [Boolean, Number], default: false },
      alt_path: String,
      pxa: { type: Boolean, default: false },
      field: { type: Object, default: () => ({ value: '' }) },
      modelValue: String, // v-model Unterstützung
    },

    emits: [
      'update:modelValue',
      'imageUploaded',
      'close',
      'previewUpdated',
      'insertImage',
      'jsonUpdated',
      'refresh-preview',
    ],

    data() {
      return {
        activeTab: 'upload',
        form: { copyleft: '' },
        previewImages: {},
        selectedImages: {},
        uploading: false,
        progress: 0,
        tablex: cleanTableFn(),
        ulpath: this.alt_path,
        GetAuth: null,
        localPath: this.path,
        finalPath: this.path + this.field.value,
      };
    },

    computed: {
      isImages() {
        return cleanTableFn() === 'images';
      },
      uploadPath() {
        return `${this.domain}/${cleanTableFn()}/${this.column}`;
      },
    },

    async mounted() {
      this.GetAuth = await GetAuth();
      console.log("msg:" + this.Message);
    },

    methods: {
      tabClass(tab) {
        return [
          'whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm',
          this.activeTab === tab
            ? 'border-blue-500 text-blue-600'
            : 'border-transparent text-layout-sun-500 dark:text-layout-night-500 hover:text-layout-sun-700 dark:hover:text-layout-night-700 hover:border-layout-sun-300 dark:hover:border-layout-night-300',
        ];
      },

      triggerFileInput() {
        this.$refs.fileInput.click();
      },

      handleDrop(event) {
        const files = event.dataTransfer.files;
        if (files.length > 0) {
          this.handleFileChange({ target: { files } });
        }
      },

      handleFileChange(event) {
        const file = event.target.files[0];
        if (file) {
          this.selectedImages[this.column] = file;
          const previewUrl = URL.createObjectURL(file);
          this.previewImages[this.column] = previewUrl;

          this.$emit('previewUpdated', {
            column: this.column,
            url: previewUrl,
          });

        //   if (this.Message) {
        //     const extension = file.name.split('.').pop();
        //     const newfg = `${file.name}_${this.GetAuth}.${extension}`;
        //     this.$emit('insertImage', newfg);
        //   }
        }
      },

      closeModal() {
        this.selectedImages[this.column] = null;
        this.previewImages[this.column] = null;
        this.uploading = false;
        this.progress = 0;
        this.activeTab = 'upload';
        this.$emit('close');
      },

      async uploadImage() {
  const selectedImage = this.selectedImages[this.column];
  if (!selectedImage) return;

  this.uploading = true;
  this.progress = 0;

  const formData = new FormData();
  formData.append('image', selectedImage);
  formData.append('ulpath', this.ulpath);
  formData.append('column', this.column);
  formData.append('namee', this.namee);
  formData.append('_token', document.querySelector('meta[name="csrf-token"]').getAttribute('content'));
  formData.append('table', this.tablex);
  formData.append('copyleft', this.form.copyleft);
  formData.append('Message', this.Message ? 1 : 0);
  formData.append('is_imgdir', this.finalPath);

  const xhr = new XMLHttpRequest();

  xhr.upload.addEventListener('progress', (event) => {
    if (event.lengthComputable) {
      this.progress = Math.round((event.loaded / event.total) * 100);
    }
  });

  xhr.onload = () => {
    this.uploading = false;
    if (xhr.status === 200) {
      let response = {};
      try {
        response = JSON.parse(xhr.responseText);
      } catch (e) {
        console.error('Ungültige Antwort vom Server:', xhr.responseText);
        return;
      }

      const filePath = response.image_url; // serverseitiger fullPath

      // ModelValue und Upload-Event
      this.$emit('update:modelValue', filePath);
      this.$emit('imageUploaded', filePath);

      // Bei Messages: InsertImage mit korrektem Pfad
      if (this.Message) {
        this.$emit('insertImage', filePath);
      } else if (!this.is_imgdir) {
        const el = document.getElementById(this.column);
        if (el) el.value = `/images/${this.ulpath.replace('//', '/')}/thumbs/${filePath}`;

        const imgEl = document.getElementById(`com_${this.column}`);
        if (imgEl) imgEl.src = `/images/${this.ulpath.replace('//', '/')}/thumbs/${filePath}`;
      } else {
        const el = document.getElementById(this.column);
        if (el) el.value = filePath;

        if (this.$refs.editor2) this.$refs.editor2.fetchImages();
        this.$emit('refresh-preview');
      }

      // Modal schließen
      this.closeModal();
    } else {
      console.error('Fehler beim Upload:', xhr.status, xhr.responseText);
    }
  };

  xhr.onerror = () => {
    this.uploading = false;
    console.error('Fehler beim Hochladen:', xhr.status);
  };

  const isw = this.isImages && !this.Message ? '1' : '0';
  const endpoint =
    typeof this.oripath === 'undefined' || this.oripath == '0'
      ? `/upload-image/${this.tablex}/${isw}`
      : `/upload-image_alt/${this.tablex}/${isw}/${this.oripath}`;

  xhr.open('POST', endpoint, true);
  xhr.send(formData);
},


      onJsonUpdated(newJson) {
        this.$emit('jsonUpdated', newJson);
      },

      savedir() {
        if (this.field.value && this.field.value.trim() !== '') {
          this.pxa = false;
        }
      },

      refreshGallery() {
        if (this.$refs.editor2) {
          this.$refs.editor2.fetchImages();
        }
      },
    },
  };
  </script>

  <style scoped>
  nav button {
    outline: none;
    transition: all 0.3s ease;
  }
  </style>
