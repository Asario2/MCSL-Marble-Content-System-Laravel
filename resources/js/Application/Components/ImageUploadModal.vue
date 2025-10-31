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
            <form @submit.prevent="uploadImage">
              <h3 class="text-2xl font-semibold text-center mb-4">Bild hochladen</h3>

              <CopyLeftSelect
                v-if="isImages && !Message && !column.includes('img_thumb')"
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
                <button type="button" @click="closeModal" class="px-4 py-2 bg-red-500 text-white rounded-lg hover:bg-red-600">
                  Schliessen
                </button>
                <button type="submit" v-if="selectedImages[column]" class="px-4 py-2 bg-blue-500 text-white rounded-lg hover:bg-blue-600">
                  Hochladen
                </button>
              </div>
            </form>

        </div>

        <!-- Alternative Ansicht, wenn pxa gesetzt ist -->
        <div v-else class="space-y-4">
          <label class="block text-sm font-medium text-layout-sun-700 dark:text-layout-night-300">Bilderordner</label>
          <input
            type="text"
            v-model="field.value"
            id="folder_save"
            class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-800 dark:text-white"
          />
          <div class="flex justify-between mt-6">
            <button type="button" @click="closeModal" class="px-4 py-2 bg-red-500 text-white rounded-lg hover:bg-red-600">Schliessen</button>
            <button type="button" @click="savedir" class="px-4 py-2 bg-blue-500 text-white rounded-lg hover:bg-blue-600">Weiter</button>
          </div>
        </div>
      </div>
    </div>
  </template>

  <script>
  import CopyLeftSelect from '@/Application/Components/Content/CopyLeftSelect.vue';
  import ImageJsonEditor from '@/Application/Admin/ImageJsonEditor.vue';
  import { CleanTable as cleanTableFn, GetAuth, SD } from '@/helpers';
  import { nextTick } from 'vue';

  export default {
    name: 'ImageUploadModal',
    components: { CopyLeftSelect, ImageJsonEditor },
    props: {
      is_imgdir: { type: String,Array,Object,Boolean, default: false },
      isModalOpen: { type: Boolean, default: false },
      column: String,
      domain: String,
      path: String,
      jspath: String,
      namee: String,
      Message: { type: [Boolean, Number], default: false },
      alt_path: String,
      pxa: { type: Boolean, default: false },
      field: { type: Object, default: () => ({ value: '' }) },
      modelValue: String,
    },
    emits: [
      'update:modelValue',
      'imageUploaded',
      'close',
      'previewUpdated',
      'insertImage',
      'jsonUpdated',
      'refresh-preview',
      'refresh-gallery',
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
        finalPath: this.path + this.field.value,
      };
    },
    computed: {
      isImages() {
        return cleanTableFn() === 'images';
      },
      jspath_alt() {
        return `images/_${this.SD()}/${this.tablex}/${this.column}/index.json`;
      },
    },
    watch: {
      field: {
        deep: true,
        handler() {
          this.finalPath = this.path + this.field.value;
        },
      },
    },
    async mounted() {
      this.GetAuth = await GetAuth();
    },
    methods: {
      SD,

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
        if (event.dataTransfer.files.length) {
          this.handleFileChange({ target: { files: event.dataTransfer.files } });
        }
      },
      handleFileChange(event) {
        const file = event.target.files[0];
        if (!file) return;

        this.selectedImages = { ...this.selectedImages, [this.column]: file };
        this.previewImages = { ...this.previewImages, [this.column]: URL.createObjectURL(file) };

        this.$emit('previewUpdated', { column: this.column, url: this.previewImages[this.column] });
      },

      closeModal() {
        this.selectedImages = { ...this.selectedImages, [this.column]: null };
        this.previewImages = { ...this.previewImages, [this.column]: null };
        this.uploading = false;
        this.progress = 0;
        this.activeTab = 'upload';
        this.$emit('close');


      },

      async uploadImage() {
        const file = this.selectedImages[this.column];
        if (!file) return;
        const sizeInMB = (file.size / (1024 * 1024)).toFixed(2);
        if(sizeInMB > 5)
        {
            alert("Bild zu Groß, Maximal 5 MB erlaubt");
            return;
        }
        this.uploading = true;
        this.progress = 0;

        const formData = new FormData();
        formData.append('image', file);
        formData.append('_token', document.querySelector('meta[name="csrf-token"]').getAttribute('content'));
        formData.append('ulpath', this.ulpath);
        formData.append('column', this.column);
        formData.append('namee', this.namee);
        formData.append('table', this.tablex);
        formData.append('copyleft', this.form.copyleft);
        formData.append('Message', this.Message ? 1 : 0);
        formData.append('is_imgdir', this.finalPath);

        try {
          const res = await fetch(`/upload-image/${this.tablex}/1`, { method: 'POST', body: formData });
          const data = await res.json();

          if (!data.image_url) throw new Error('Upload fehlgeschlagen');

          const filePath = data.image_url;
          this.$emit('update:modelValue', filePath);
          this.$emit('imageUploaded', filePath);

          if (this.Message) this.$emit('insertImage', filePath);

          if (this.is_imgdir) {

            // Reset upload state
            this.selectedImages = { ...this.selectedImages, [this.column]: null };
            this.previewImages = { ...this.previewImages, [this.column]: null };
            this.uploading = false;
            this.progress = 0;

            await nextTick();
            if (this.$refs.editor3) {
              await this.$refs.editor3.fetchImages();
            }

            this.$emit('refresh-gallery');
            this.$emit('refresh-preview');

          } else {
            this.closeModal();
          }
        } catch (e) {
          console.error(e);
          this.uploading = false;
        }
      },

      onJsonUpdated(newJson) {
        this.$emit('jsonUpdated', newJson);
      },
      savedir() {
        if (this.field.value.trim()) this.pxa = false;
      },
      refreshGallery() {
        if (this.$refs.editor3) this.$refs.editor3.fetchImages();
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
