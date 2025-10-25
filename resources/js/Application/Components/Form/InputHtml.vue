<template>
    <div>
        <!-- Menü -->
        <div class="mb-4 h-14 p-4 flex items-center bg-layout-sun-300 dark:bg-layout-night-300 rounded-lg edit0R">
            <p class='border rounded label p-3'><nobr>{{name ? getLabel(name): ''}}</nobr></p>

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

            <button type="button" @click="AddHr" class="icon-btn" v-tippy  aria-label="Trennlinie">
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
            <smileys v-if="!nosmilies" :editor="name"></smileys>
        </div>

        <div v-if="hasError && required" class="text-red-500 text-sm mt-1">
            Dieses Feld darf nicht leer sein.
        </div>

        <!-- Textfeld -->
        <div class="mb-4 p-4 bg-layout-sun-0 dark:bg-layout-night-0 rounded-lg edit0R editor">
            <div
                ref="editor"
                :id="name"
                contenteditable="true"
                dir="ltr"
                class="editor rounded p-3 min-h-[150px] max-h-[350px] focus:outline-none"
                :required="required"
                @focus="onFocus"
                @blur="onBlur"
                :placeholder="placeholder"
                @input="onInput"
                @keydown="handleKeyDown"
                @keyup="handleKeyUp"
                @mouseup="saveSelection"
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
            :isModalOpen="modals[name]"
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
import { GetSettings, rumLaut,nl2br } from "@/helpers";
import IconPictures from "@/Application/Components/Icons/IconPictures.vue";
import IconList from "@/Application/Components/Icons/IconList.vue";
import IconOrdList from "@/Application/Components/Icons/IconOrdList.vue";
import smileys from "@/Application/Components/Social/smileys.vue";
import IconCode from "@/Application/Components/Icons/IconCode.vue";
import IconHyperLink from "@/Application/Components/Icons/IconHyperLink.vue";
import { Tippy } from 'tippy.vue';

export default {
    name: "EditorRad",
    components: {
        IconPictures,
        IconCode,
        IconHyperLink,
        ImageUploadModal,
        IconList,
        IconOrdList,
        // SelectionHelper,
        // GetSettings,
        smileys,
        Tippy,
        // rumLaut,
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
            type: [Number, Boolean, String],
            default: false
        },
        vcol: String,
        nosmilies: {
            type: String,
            default: '',
        }
    },
    data() {
        return {
            isFocused: false,
            isModalOpen: false,
            modals: [],
            nf2: null,
            settings: {},
            savedSelection: null,
            isRequiredError: false,
            hasError: false,
            isComposing: false,
        };
    },
    emits: ['update:modelValue', 'validationFailed', 'validationPassed'],

    async mounted() {
        this.settings = await GetSettings();

        if (this.$refs.editor) {
            this.$refs.editor.innerHTML = this.decodeBrackets(nl2br(rumLaut(this.modelValue))) || "";
        }

        this.$nextTick(() => {
            tippy('[data-tippy-content]', { placement: 'right', animation: 'scale' });
            // this.forceLTR();
        });

        this.updateValue();
    },

    computed: {
        content: {
            get() {
                return this.modelValue;
            },
            set(value) {
                this.$emit("update:modelValue", nl2br(rumLaut(value)).replace('%5B', '[').replace('%5D', ']'));
            },
        },
    },

    watch: {
        modelValue(newVal) {
            const editor = this.$refs.editor;
            if (!editor) return;

            const currentHtml = editor.innerHTML;
            let decodedNewVal = nl2br(this.decodeBrackets(rumLaut(newVal)));

            if (currentHtml !== decodedNewVal) {
                this.saveSelection();
                editor.innerHTML = decodedNewVal;
                this.restoreSelection();
                ///this.forceLTR();
            }
        },
    },

    methods: {
        // forceLTR() {
        //     const el = this.$refs.editor;
        //     if (!el) return;

        //     el.setAttribute("dir", "ltr");
        //     el.style.direction = "ltr";
        //     el.style.unicodeBidi = "plaintext";
        //     el.style.textAlign = "left";

        //     const allElements = el.getElementsByTagName('*');
        //     for (let i = 0; i < allElements.length; i++) {
        //         const element = allElements[i];
        //         element.style.direction = 'ltr';
        //         element.style.textAlign = 'left';
        //         element.style.unicodeBidi = 'plaintext';
        //     }
        // },

        onFocus() {
            this.isFocused = true;
            // this.forceLTR();
        },

        onBlur() {
            this.isFocused = false;
        },

        handleKeyDown(e) {
            if (e.key === 'Enter' && !e.shiftKey) {
                document.execCommand('insertParagraph', false, null);
                e.preventDefault();
                this.updateValue();
                return;
            }

            if (e.key === ' ' && !e.ctrlKey && !e.altKey && !e.metaKey) {
                this.saveSelection();
                setTimeout(() => {
                    // this.forceLTR();
                    this.restoreSelection();
                }, 0);
                return;
            }

            if (e.key === 'Process' || e.isComposing) {
                this.isComposing = true;
                return;
            }

            if (['Tab', 'Backspace', 'Delete'].includes(e.key)) {
                this.saveSelection();
            }
        },

        handleKeyUp(e) {
            if (e.key === 'Process' || !e.isComposing) {
                this.isComposing = false;
            }

            // Nach JEDEM Tastendruck LTR erzwingen – außer Enter
            this.$nextTick(() => {
                if (e.key !== 'Enter') {
                    // this.forceLTR();
                }
            });
        },

        // Vereinfachte Text-Einfügung (nicht mehr für Space benötigt)
        insertTextAtCursor(text) {
            const sel = window.getSelection();
            if (sel.rangeCount > 0) {
                const range = sel.getRangeAt(0);
                const textNode = document.createTextNode(text);
                range.insertNode(textNode);

                range.setStartAfter(textNode);
                range.setEndAfter(textNode);
                range.collapse(false);

                sel.removeAllRanges();
                sel.addRange(range);

                this.updateValue();
            }
        },

        saveSelection() {
            const sel = window.getSelection();
            if (sel.rangeCount > 0) {
                const range = sel.getRangeAt(0);
                const editor = this.$refs.editor;

                if (editor.contains(range.commonAncestorContainer)) {
                    this.savedSelection = {
                        startContainer: range.startContainer,
                        startOffset: range.startOffset,
                        endContainer: range.endContainer,
                        endOffset: range.endOffset
                    };
                }
            }
        },

        restoreSelection() {
            if (!this.savedSelection) {
                this.setCursorToEnd();
                return;
            }

            try {
                const range = document.createRange();
                range.setStart(this.savedSelection.startContainer, this.savedSelection.startOffset);
                range.setEnd(this.savedSelection.endContainer, this.savedSelection.endOffset);

                const sel = window.getSelection();
                sel.removeAllRanges();
                sel.addRange(range);

                this.$refs.editor.focus();
            } catch {
                this.setCursorToEnd();
            }
        },

        setCursorToEnd() {
            const editor = this.$refs.editor;
            if (!editor) return;

            editor.focus();
            const range = document.createRange();
            const sel = window.getSelection();

            range.selectNodeContents(editor);
            range.collapse(false);

            sel.removeAllRanges();
            sel.addRange(range);
        },

        isValid() {
            if (!this.required) return true;

            let html = this.$refs.editor?.innerHTML || '';
            html = nl2br(rumLaut(html)).replace('%5B', '[').replace('%5D', ']');
            const plain = html.replace(/<[^>]*>/g, '').trim();

            const isValid = plain.length > 0;
            this.isRequiredError = !isValid;

            return isValid;
        },

        decodeBrackets(str) {
            if (!str) return "";
            return str.replace(/%5B/g, "[").replace(/%5D/g, "]");
        },

        getLabel(name) {
            return this.settings?.exl?.[name] ?? name;
        },

        onInput() {
            if (this.isComposing) return;

            requestAnimationFrame(() => {
                this.updateValue();
                // this.forceLTR();
                this.saveSelection();
            });
        },
        validate() {
            if (!this.required) return true;

            const editor = this.$refs.editor;
            if (!editor) return true;

            // Inhalt ohne HTML-Tags prüfen
            const text = (editor.innerHTML || '').replace(/<[^>]*>/g, '').trim();

            if (text.length === 0) {
                this.hasError = true;
                this.$emit('validationFailed');
                return false;
            } else {
                this.hasError = false;
                this.$emit('validationPassed');
                return true;
            }
        },
        updateValue() {
        const editor = this.$refs.editor;
        if (!editor) return;

        let html = editor.innerHTML.trim();
        html = html.replace(/[\u202A-\u202E\u200E\u200F\u061C]/g, '');
        html = nl2br(rumLaut(this.decodeBrackets(html)));

        this.$emit('update:modelValue', html);

        const altInput = document.getElementById(this.name + "_alt");
        if (altInput) altInput.value = html;
        },

        getSelection() {
            const sel = window.getSelection();
            return sel.rangeCount > 0 ? sel.getRangeAt(0) : null;
        },

        cleanupEmptyTags() {
            const editor = document.getElementById('content');
            editor.querySelectorAll('b:empty, i:empty, code:empty, h1:empty, h2:empty, h3:empty, h4:empty, h5:empty, h6:empty, p:empty, div:empty')
                .forEach(el => el.remove());
            editor.innerHTML = editor.innerHTML.replace(/(<br\s*\/?>\s*){2,}/g, '<br>').replace('%5B', '[').replace('%5D', ']');
        },

        toggleFormat(format) {
            this.saveSelection();

            requestAnimationFrame(() => {
                this.restoreSelection();
                const sel = window.getSelection();
                if (!sel || sel.rangeCount === 0) return;

                const range = sel.getRangeAt(0);
                if (range.collapsed && !['email', 'link'].includes(format)) return;

                const selectedText = range ? range.toString() : '';

                const parent = range.commonAncestorContainer.nodeType === 3
                    ? range.commonAncestorContainer.parentNode
                    : range.commonAncestorContainer;

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

                        const newRange = document.createRange();
                        newRange.setStartAfter(h);
                        newRange.collapse(true);
                        sel.removeAllRanges();
                        sel.addRange(newRange);
                    }
                    this.updateValue();
                    // this.forceLTR();
                    return;
                }








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
                        // this.forceLTR();
                    }
                    return;
                }

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
                        // this.forceLTR();
                    }
                }

                // this.forceLTR();
            });
        },

        toggleBold() {
            this.saveSelection();
            document.execCommand("bold");
            this.$nextTick(() => {
                this.restoreSelection();
                this.updateValue();
                // this.forceLTR();
            });
        },

        toggleItalic() {
            this.saveSelection();
            document.execCommand("italic");
            this.$nextTick(() => {
                this.restoreSelection();
                this.updateValue();
                // this.forceLTR();
            });
        },

        toggleHeading(level) {
            this.toggleFormat("h" + level);
        },

        toggleCode() {
        this.saveSelection();

        requestAnimationFrame(() => {
            this.restoreSelection();
            const sel = window.getSelection();
            if (!sel || sel.rangeCount === 0) return;

            const range = sel.getRangeAt(0);

            // Prüfen, ob die Auswahl in einem <code> ist
            let parent = range.commonAncestorContainer;
            while (parent && parent.nodeType === 3) parent = parent.parentNode;

            if (parent && parent.tagName && parent.tagName.toLowerCase() === "code") {
                // Nur den selektierten Teil aus dem Code entfernen
                const selectedText = range.toString();

                if (selectedText.length > 0) {
                    const before = parent.textContent.substring(0, range.startOffset);
                    const after = parent.textContent.substring(range.endOffset);

                    // Neues Textkonstrukt zusammenbauen:
                    const newFragment = document.createDocumentFragment();

                    if (before) {
                        const beforeNode = document.createElement("code");
                        beforeNode.textContent = before;
                        newFragment.appendChild(beforeNode);
                    }

                    newFragment.appendChild(document.createTextNode(selectedText));

                    if (after) {
                        const afterNode = document.createElement("code");
                        afterNode.textContent = after;
                        newFragment.appendChild(afterNode);
                    }

                    parent.replaceWith(newFragment);
                } else {
                    // Wenn nichts markiert → gesamten Code entfernen
                    const textNode = document.createTextNode(parent.textContent);
                    parent.replaceWith(textNode);
                }

                this.updateValue();
                // this.forceLTR();
                return;
            }

            // Wenn kein Text markiert → abbrechen
            if (range.collapsed) return;

            // <code>-Tag um Auswahl herumsetzen
            const selectedText = range.toString();
            const code = document.createElement("code");
            code.textContent = selectedText;

            range.deleteContents();
            range.insertNode(code);

            // Cursor hinter das Code-Element setzen
            const newRange = document.createRange();
            newRange.setStartAfter(code);
            newRange.collapse(true);
            sel.removeAllRanges();
            sel.addRange(newRange);

            this.updateValue();
            // this.forceLTR();
        });
    },

        AddHr() {
            this.saveSelection();
            const hr = document.createElement("hr");
            const range = this.getSelection();
            if (range) {
                range.collapse(false);
                range.insertNode(hr);
            } else {
                this.$refs.editor.appendChild(hr);
            }
            this.updateValue();
            // this.forceLTR();
            this.restoreSelection();
        },

        toggleList(type) {
            this.saveSelection();
            const cmd = type === "ul" ? "insertUnorderedList" : "insertOrderedList";
            document.execCommand(cmd);
            this.$nextTick(() => {
                this.restoreSelection();
                this.updateValue();
                // this.forceLTR();
            });
        },

        openModal_alt2() {
            this.saveSelection();
            this.isModalOpen = true;
            this.modals[this.name] = true;
        },

        closeModal() {
            this.isModalOpen = false;
            this.modals[this.name] = false;
        },

        insertImageIntoEditor(imageUrl) {
            const editor = this.$refs.editor;
            if (!editor) return;

            const img = document.createElement("img");
            img.src = imageUrl;
            img.alt = "Bild";
            img.className = "inline";
            img.style.display = "inline";

            const br = document.createElement("br");

            this.$nextTick(() => {
                let range;
                const selection = window.getSelection();

                if (this.savedSelection) {
                    try {
                        range = document.createRange();
                        range.setStart(this.savedSelection.startContainer, this.savedSelection.startOffset);
                        range.setEnd(this.savedSelection.endContainer, this.savedSelection.endOffset);
                    } catch {
                        range = document.createRange();
                        range.selectNodeContents(editor);
                        range.collapse(false);
                    }
                } else {
                    range = document.createRange();
                    range.selectNodeContents(editor);
                    range.collapse(false);
                }

                selection.removeAllRanges();
                selection.addRange(range);

                range.insertNode(br);
                range.insertNode(img);

                const newRange = document.createRange();
                newRange.setStartAfter(img);
                newRange.collapse(true);
                selection.removeAllRanges();
                selection.addRange(newRange);

                editor.focus();
                this.updateValue();
                // this.forceLTR();
                this.savedSelection = null;
            });
        },

        handleImageUpload(imageUrl) {
            this.nf2 = imageUrl;
            this.insertImageIntoEditor(this.nf2);
        },

        insertImage(imageUrl) {
            this.insertImageIntoEditor(imageUrl);
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
    color: #a7dff5 !important;

}
.editor OL LI{
    list-style-type: decimal;
}
.editor-error-message{
    color:#a00;
}

/* Wichtig: Whitespace handling für Leerzeichen */
.editor {
    white-space: pre-wrap !important;
    word-wrap: break-word !important;
}
.editor code{
    background-color: #ccc !important;
    padding: 4px;
}
.editor P {
    margin-top:1.2em;
    margin-bottom:1.2em;
}
</style>
