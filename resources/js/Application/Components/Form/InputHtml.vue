    <template>
        <div>
        <!-- Menü -->
        <div class="mb-4 h-14 p-4 flex items-center bg-layout-sun-300 dark:bg-layout-night-300 rounded-lg edit0R">
            <p class='border rounded label p-1'><nobr>{{getLabel(name)}}</nobr></p>

            <!-- Formatierungsbuttons -->
            <button type="button" @click="toggleBold()" class="icon-btn" v-tippy aria-label="Fett">
            <b>B</b>
            </button>
            <tippy>Fett</tippy>

            <button type="button" @click="toggleItalic()" class="icon-btn" v-tippy aria-label="Kursiv">
            <b><i>I</i></b>
            </button>
            <tippy>Kursiv</tippy>

            <template v-for="i in 6" :key="i">
            <button type="button" @click="toggleHeading(`${i}`)" class="icon-btn" v-tippy :aria-label="'Überschrift ' + i">
                <b>H{{ i }}</b>
            </button>
            <tippy>Überschrift {{ i }}</tippy>
            </template>

            <button type="button" @click="openModal_alt2" class="icon-btn" v-tippy aria-label="Bildupload">
            <b><IconPictures stroke="currentColor"/></b>
            </button>
            <tippy>Bildupload</tippy>

            <button type="button" @click="toggleCode()" class="icon-btn" v-tippy aria-label="Code">
            <b><IconCode /></b>
            </button>
            <tippy>Code</tippy>

            <button type="button" @click="AddHr" class="icon-btn" v-tippy  aria-label="Trennlinie    ">
            <b><nobr>---</nobr></b>
            </button>
            <tippy>Trennlinie</tippy>

            <button type="button" @click="toggleFormat('email')" class="icon-btn" v-tippy  aria-label="Email">
            <b>@</b>
            </button>
                <tippy>Email Link</tippy>

            <button type="button" @click="toggleFormat('link')" class="icon-btn" v-tippy aria-label="Hyperlink">
            <b><IconHyperLink/></b>
            </button>
            <tippy>Hyperlink</tippy>

            <button type="button" @click="toggleList('ul')" class="icon-btn" v-tippy  aria-label="Ungeordnete Liste">
            <b><IconList/></b>
            </button>
            <tippy>Ungeordnete Liste</tippy>

            <button type="button" @click="toggleList('ol')" class="icon-btn" v-tippy aria-label="Geordnete Liste">
            <b><IconOrdList/></b>
            </button>
            <tippy>Geordnete Liste</tippy>
        </div>
        <div>
        <smileys :editor="name"></smileys>
        </div>
        <div v-if="hasError && required" class="text-red-500 text-sm mt-1">
                Dieses Feld darf nicht leer sein.
            </div>
        <!-- Textfeld -->
        <div class="mb-4 p-4 bg-layout-sun-0 dark:bg-layout-night-0 rounded-lg edit0R editor">
            <div ref="editor" :id="name" contenteditable="true" class="editor rounded p-3 min-h-[150px] max-h-[350px] focus:outline-none"
            :required="required"
            @focus="isFocused = true"
            @blur="isFocused = false"
            :placeholder="placeholder"
            :class="{
                'border-red-500 ring-2 ring-red-300': hasError && required,
                'border-gray-300': !hasError
            }"
            @input="onInput"
            ></div>

            <input type="hidden" :id="name + '_alt'">
        </div>


            <ImageUploadModal
            v-show="isModalOpen"
            :tablex="table_x"
            :column="name"
            :path="tablex"
            :ref="name"
            :value="imageId"
            :image="value"
            :namee="value"
            :namee2="name"
            :Message="true"

            @insertImage="insertImage"
            @close="closeModal"
            @uploaded="insertImageIntoEditor"
            />

        </div>
    </template>

    <script>
    import tippy from 'tippy.js';
    import 'tippy.js/dist/tippy.css';
    import ImageUploadModal from '@/Application/Components/ImageUploadModal.vue';
    import { selectionHelper, GetSettings,rumLaut } from "@/helpers";
    import IconPictures from "@/Application/Components/Icons/IconPictures.vue";
    import IconList from "@/Application/Components/Icons/IconList.vue";
    import IconOrdList from "@/Application/Components/Icons/IconOrdList.vue";
    import smileys from "@/Application/Components/Social/smileys.vue";
    import IconCode from "@/Application/Components/Icons/IconCode.vue";
    import IconHyperLink from "@/Application/Components/Icons/IconHyperLink.vue";
    import { Tippy } from 'tippy.vue';

    export default {
        name: "Editor",
        components: {
        IconPictures,
        IconCode,
        IconHyperLink,
        ImageUploadModal,
        IconList,
        IconOrdList,
        selectionHelper,
        GetSettings,
        smileys,
        Tippy,
        rumLaut,
    },
        props: {
        imageId: [String, Number],
        modelValue: [String, Number],
        name: String,
        id: [String, Number],
        value: [String, Number],
        tablex: String,
        table_x: String,
        placeholder: String,
        required: {
            type:[Number,Boolean,String],
            default:false
        },
        vcol:String,
        },
        data() {
        return {
            isFocused: false,
            isModalOpen: false,
            nf2: null,
            settings: {},
            savedRange: null,
            isRequiredError: false,
            hasError: false,
            // selectionHelper: new SelectionHelper(),
        };
        },
        emits: ['update:modelValue'],

        async mounted() {
            this.settings = await GetSettings();
            // this.setInitialContent();


            this.$nextTick(() => {
    tippy('[data-tippy-content]', { placement: 'right', animation: 'scale' });
    // this.setCursorAtEnd();
  });
        if (this.$refs.editor) {
            this.$refs.editor.innerHTML =   (this.modelValue) || "";
        }
        // this.$nextTick(() => this.setCursorAtEnd());
        // this.setInitialContent();
        this.updateValue();
    },
        computed: {
        content: {
            get() {
            return this.modelValue;
            },
            set(value) {
            this.$emit("update:modelValue", rumLaut(value).replace('%5B', '[').replace('%5D', ']'));
            },
        },
        },
        watch: {
            modelValue(newVal) {
                if (this.$refs.editor && this.$refs.editor.innerHTML !== newVal) {
                this.$refs.editor.innerHTML = this.decodeBrackets(rumLaut(newVal));
                this.setCursorAtEnd();
                }
            },
                    },
        methods: {
  isValid() {
      if (!this.required) return true;

      let html = this.$refs.editor?.innerHTML || '';
      html = rumLaut(html).replace('%5B', '[').replace('%5D', ']');
      const plain = html.replace(/<[^>]*>/g, '').trim();

      const isValid = plain.length > 0;
      this.isRequiredError = !isValid;

      return isValid;
    },
    //         onInput(event) {

    //   const content = event.target.innerText.trim();
    //   this.$emit('update:modelValue', content);

    //   // Optional: lokale Validierung
    //   if (this.required && !content) {
    //     this.showError = true;
    //     this.$emit('validationFailed', 'Field is required');
    //   } else {
    //     this.showError = false;
    //     this.$emit('validationPassed');
    //   }
    // },
    decodeBrackets(str) {
    if (!str) return "";
    return str.replace(/%5B/g, "[").replace(/%5D/g, "]");
  },
  setInitialContent() {
    this.$refs.editor.innerHTML = this.decodeBrackets(rumLaut(this.content));
    },

            getLabel(name) {
    return this.settings?.exl?.[name] ?? name;
  },

  updateValue() {
    let html = this.$refs.editor.innerHTML;
    html = rumLaut(this.decodeBrackets(html));
    this.$emit('update:modelValue', html);
    document.getElementById(this.name + "_alt").value = html;
    },

  setCursorAtEnd() {
    const el = this.$refs.editor;
    if (!el) return;
    el.focus();
    const range = document.createRange();
    range.selectNodeContents(el);
    range.collapse(false);
    const sel = window.getSelection();
    sel.removeAllRanges();
    sel.addRange(range);
  },
   getSelection() {
    const sel = window.getSelection();
    return sel.rangeCount > 0 ? sel.getRangeAt(0) : null;
  },
  cleanupEmptyTags() {
    const editor = document.getElementById('content');
    editor.querySelectorAll('b:empty, i:empty, code:empty, h1:empty, h2:empty, h3:empty, h4:empty, h5:empty, h6:empty, p:empty, div:empty')
      .forEach(el => el.remove());
    // alert("cleaned");
    // Entferne doppelte <br>
    editor.innerHTML = editor.innerHTML.replace(/(<br\s*\/?>\s*){2,}/g, '<br>').replace('%5B', '[').replace('%5D', ']');
  },

  toggleFormat(format) {
  this.saveSelection();        // 1. Aktuelle Selektion speichern
//   this.$refs.editor.focus();   // 2. Editor wieder aktivieren
//   this.restoreSelection();     // 3. Selektion wiederherstellen


requestAnimationFrame(() => {
    this.restoreSelection();
  const sel = window.getSelection(); // ⬅️ Jetzt ist Selektion korrekt
  if (!sel || sel.rangeCount === 0) return;

  const range = sel.getRangeAt(0);
  if (range.collapsed && !['email', 'link'].includes(format)) return;

  const selectedText = range.toString();

  const parent = range.commonAncestorContainer.nodeType === 3
    ? range.commonAncestorContainer.parentNode
    : range.commonAncestorContainer;

  // HEADINGS
  if (/^h[1-6]$/.test(format)) {
    if (parent.nodeName.toLowerCase() === format) {
      const p = document.createElement('p');
      p.innerHTML = parent.innerHTML;
      parent.replaceWith(p);
    } else {
      const h = document.createElement(format);
      h.textContent = selectedText;
      range.deleteContents();
      range.insertNode(h);

      // Cursor setzen
      const newRange = document.createRange();
      newRange.setStartAfter(h);
      newRange.collapse(true);
      sel.removeAllRanges();
      sel.addRange(newRange);
    }
    this.updateValue();
    return;
  }

  // EMAIL
  if (format === 'email') {
    const email = prompt("E-Mail-Adresse eingeben:");
    if (email) {
      const a = document.createElement("a");
      a.href = `mailto:${email}`;
      a.textContent = selectedText || email;
      range.deleteContents();
      range.insertNode(a);

      const newRange = document.createRange();
      newRange.setStartAfter(a);
      newRange.collapse(true);
      sel.removeAllRanges();
      sel.addRange(newRange);

      this.updateValue();
    }
    return;
  }

  // LINK
  if (format === 'link') {
    const url = prompt("URL eingeben:", "https://");
    if (url) {
      const a = document.createElement("a");
      a.href = url;
      a.target = "_blank";
      a.rel = "noopener noreferrer";
      a.textContent = selectedText || url;
      range.deleteContents();
      range.insertNode(a);

      const newRange = document.createRange();
      newRange.setStartAfter(a);
      newRange.collapse(true);
      sel.removeAllRanges();
      sel.addRange(newRange);

      this.updateValue();
    }
}
selectionHelper.restore();

    });

  },
  toggleBold() {
    selectionHelper.save();
    document.execCommand("bold");
    selectionHelper.restore();
    this.updateValue();
  },

  toggleItalic() {
    selectionHelper.save();
    document.execCommand("italic");
    selectionHelper.restore();
    this.updateValue();
  },

  toggleHeading(level) {
    selectionHelper.save();
    this.toggleFormat("h" + level);
    selectionHelper.restore();
    this.updateValue();
  },

  toggleCode() {
    selectionHelper.save();
    this.toggleFormat('code');
    selectionHelper.restore();

  },
  saveSelection() {
  const sel = window.getSelection();
  if (sel && sel.rangeCount > 0) {
    this.savedRange = sel.getRangeAt(0).cloneRange();
  }
},
restoreSelection() {
  const sel = window.getSelection();
  sel.removeAllRanges();
  if (this.savedRange) {
    sel.addRange(this.savedRange);
  }
},
  AddHr() {
    const hr = document.createElement("hr");
    const range = this.getSelection();
    if (range) {
      range.collapse(false);
      range.insertNode(hr);
    } else {
      this.$refs.editor.appendChild(hr);
    }
    this.updateValue();
  },
  toggleList(type) {
    const cmd = type === "ul" ? "insertUnorderedList" : "insertOrderedList";
    document.execCommand(cmd);
    this.updateValue();
  },

  openModal_alt2() {
    this.saveSelection();
    this.isModalOpen = true;
  },

  closeModal() {
    this.isModalOpen = false;
  },
  insertImageIntoEditor(imageUrl) {
        const editor = this.$refs.editor;
        if (!editor) return alert("NO EDITOR");
        const img = document.createElement("img");
        img.src = imageUrl;
        img.alt = "Bild";
        img.class = "inline";
        img.style.display = "inline";
        const br = document.createElement("br");
        this.$nextTick(() => {
          const selection = window.getSelection();
          selection.removeAllRanges();
          const range = this.savedRange ?? document.createRange();
          range.collapse(false);
          range.insertNode(br);
          range.insertNode(img);
          const newRange = document.createRange();
          newRange.setStartAfter(br);
          newRange.setEndAfter(br);
          selection.addRange(newRange);
          editor.focus();
          this.updateValue();
          this.savedRange = null;
        });
      },
      handleImageUpload(imageUrl) {
        this.nf2 = imageUrl;
        this.insertImageIntoEditor(this.nf2);
      },
      insertImage(imageUrl) {
        this.insertImageIntoEditor(imageUrl);
      },

      onInput(e) {
        let html = e.target.innerHTML.trim();
        html = this.decodeBrackets(html);
        this.$emit('update:modelValue', html);
        this.hasError = html.replace(/<[^>]*>/g, '').trim().length === 0;
        },

    get content() {
      return this.modelValue;
    },
    },
    };
    </script>

    <style>
    .edit0R {
        max-width: 1177px;
        overflow: auto;
        height: auto;
    }
    .editor h1 {
        font-weight: 800;
        font-size: 2.25em;
        margin-top: 0;
        margin-bottom: 0.8888889em;
        line-height: 1.1111111;

    }
    .editor h2 {
        font-size: 1.875rem;
        font-weight: 600;
        margin-top: 1rem;
        margin-bottom: 0.5rem;
    }
    .editor h3 {
        font-size: 1.5rem;
        font-weight: 600;
        margin-top: 0.75rem;
        margin-bottom: 0.25rem;
    }
    .editor h4{
        font-weight: 600;
        font-size: 1.25rem;
    }
    .editor h5{
        font-weight: 600;
        font-size: 1.1rem;
    }
    .editor h6{
        font-weight: 500;
        font-size: 1.05rem;
    }
    .icon-btn {
        padding: 0.4rem 0.6rem;
        border-radius: 9999px;
        transition: background 0.2s;
        cursor: pointer;
        margin-right: 0.25rem;
        background: transparent;
    }
    .icon-btn:hover {
        background-color: var(--tw-bg-opacity);
    }
    .editor a:link,a:visited,a:active{
        text-decoration: underline;
    }
    .editor OL LI{
        list-style-type: decimal;
    }
    .editor-error-message{
        color:#a00;
    }
    </style>
