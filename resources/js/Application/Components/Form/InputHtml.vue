<template>
    <div>
        <!-- Menü -->
        <div class="mb-4 h-14 p-4 flex items-center bg-layout-sun-300 dark:bg-layout-night-300 rounded-lg edit0R">
            <p class='border rounded label p-3'><nobr>{{name ? getLabel(name): ''}}</nobr></p>

            <!-- Formatierungsbuttons -->
            <button type="button" @click="toggleBold()" class="icon-btn" v-tippy aria-label="Fett">
                <b>B</b>
            </button>

            <button type="button" @click="toggleItalic()" class="icon-btn" v-tippy aria-label="Kursiv">
                <b><i>I</i></b>
            </button>

            <template v-for="i in 6" :key="i">
                <button type="button" @click="toggleHeading(`${i}`)" class="icon-btn" v-tippy :aria-label="'Überschrift ' + i">
                    <b>H{{ i }}</b>
                </button>
            </template>

            <button type="button" @click="openModal_alt2" class="icon-btn" v-tippy aria-label="Bildupload">
                <b><IconPictures stroke="currentColor"/></b>
            </button>

            <button type="button" @click="toggleCode()" class="icon-btn" v-tippy aria-label="Code">
                <b><IconCode /></b>
            </button>

            <button type="button" @click="AddHr" class="icon-btn" v-tippy  aria-label="Trennlinie">
                <b><nobr>---</nobr></b>
            </button>

            <button type="button" @click="toggleFormat('email')" class="icon-btn" v-tippy  aria-label="Email">
                <b>@</b>
            </button>

            <button type="button" @click="toggleFormat('link')" class="icon-btn" v-tippy aria-label="Hyperlink">
                <b><IconHyperLink/></b>
            </button>

            <button type="button" @click="toggleList('ul')" class="icon-btn" v-tippy  aria-label="Ungeordnete Liste">
                <b><IconList/></b>
            </button>

            <button type="button" @click="toggleList('ol')" class="icon-btn" v-tippy aria-label="Geordnete Liste">
                <b><IconOrdList/></b>
            </button>
        </div>

        <div>
            <smileys
                v-if="!nosmilies"
                :editor="name"
                :name="name"
                @insert-smiley="handleSmileyInsert"
            ></smileys>
        </div>

        <div v-if="hasError && required" class="text-red-500 text-sm mt-1">
            Dieses Feld darf nicht leer sein.
        </div>

        <!-- Textfeld -->
        <div class="mb-4 p-4 bg-layout-sun-0 dark:bg-layout-night-0 rounded-lg edit0R editor">
            <div
                ref="editor"
                :id="'editor_' + name"
                :name="'editor_' + name"
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

            <input type="hidden" :id="name + '_alt'" :value="internalValue">
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
import { GetSettings, rumLaut, nl2br } from "@/helpers";
import IconPictures from "@/Application/Components/Icons/IconPictures.vue";
import IconList from "@/Application/Components/Icons/IconList.vue";
import IconOrdList from "@/Application/Components/Icons/IconOrdList.vue";
import smileys from "@/Application/Components/Social/smileys.vue";
import IconCode from "@/Application/Components/Icons/IconCode.vue";
import IconHyperLink from "@/Application/Components/Icons/IconHyperLink.vue";
import { Tippy } from 'tippy.vue';

export default {
    name: "InputHtml",
    components: {
        IconPictures,
        IconCode,
        IconHyperLink,
        ImageUploadModal,
        IconList,
        IconOrdList,
        smileys,
        Tippy
    },
    props: {
        imageId: [String, Number],
        modelValue: [String, Number],
        name: String,
        value: [String, Number],
        tablex: String,
        table_x: String,
        placeholder: String,
        required: { type: [Number, Boolean, String], default: false },
        nosmilies: { type: String, default: '' }
    },
    data() {
        return {
            isFocused: false,
            isModalOpen: false,
            modals: [],
            settings: {},
            savedSelection: null,
            isComposing: false,
            hasError: false,
            internalValue: ''
        };
    },
    emits: ['update:modelValue', 'validationFailed', 'validationPassed'],
    async mounted() {
        this.settings = await GetSettings();

        // Initialwert setzen
        this.internalValue = this.modelValue || this.value || '';

        if (this.$refs.editor && this.internalValue) {
            this.$refs.editor.innerHTML = this.internalValue;
        }

        this.$nextTick(() => {
            tippy('[data-tippy-content]', { placement: 'right', animation: 'scale' });
        });
    },
    watch: {
        modelValue(newVal) {
            console.log('ModelValue changed:', newVal);
            this.internalValue = newVal || '';
            this.updateEditorContent();
        },
        value(newVal) {
            console.log('Value changed:', newVal);
            this.internalValue = newVal || '';
            this.updateEditorContent();
        }
    },
    methods: {
        onFocus() {
            this.isFocused = true;
        },

        onBlur() {
            this.isFocused = false;
            this.validate();
        },

        validate() {
            const el = this.$refs.editor;
            const text = (el?.innerText || el?.textContent || '').replace(/\s+/g, '').trim();
            if (this.required && text.length === 0) {
                this.hasError = true;
                this.$emit('validationFailed');
                return false;
            }
            this.hasError = false;
            this.$emit('validationPassed');
            return true;
        },

        handleKeyDown(e) {
            if (e.key === 'Enter' && !e.shiftKey) {
                document.execCommand('insertParagraph', false, null);
                e.preventDefault();
                this.updateValue();
                return;
            }
            if (['Tab', 'Backspace', 'Delete'].includes(e.key)) this.saveSelection();
        },

        handleKeyUp(e) {
            this.isComposing = !(e.key === 'Process');
        },

        onInput() {
            if (this.isComposing) return;
            requestAnimationFrame(() => {
                this.updateValue();
                this.saveSelection();
                this.validate();
            });
        },

        saveSelection() {
            const sel = window.getSelection();
            if (sel.rangeCount > 0) {
                this.savedSelection = sel.getRangeAt(0).cloneRange();
            }
        },

        restoreSelection() {
            if (!this.savedSelection) {
                this.setCursorToEnd();
                return;
            }
            try {
                const sel = window.getSelection();
                sel.removeAllRanges();
                sel.addRange(this.savedSelection);
                this.$refs.editor.focus();
            } catch (e) {
                console.error('Error restoring selection:', e);
                this.setCursorToEnd();
            }
        },

        setCursorToEnd() {
            const editor = this.$refs.editor;
            if (!editor) return;
            editor.focus();
            const range = document.createRange();
            range.selectNodeContents(editor);
            range.collapse(false);
            const sel = window.getSelection();
            sel.removeAllRanges();
            sel.addRange(range);
        },

        updateValue() {
            const editor = this.$refs.editor;
            if (!editor) return;

            let html = editor?.innerHTML?.trim().replace(/[\u202A-\u202E\u200E\u200F\u061C]/g, '');
            html = nl2br(rumLaut(this.decodeBrackets(html)));

            this.internalValue = html;
            console.log('Emitting update:', html);
            this.$emit('update:modelValue', html);

            const altInput = document.getElementById(this.name + "_alt");
            if (altInput) altInput.value = html;
        },

        updateEditorContent() {
            const editor = this.$refs.editor;
            if (!editor) return;
            if (document.activeElement === editor) return;

            const currentHtml = editor.innerHTML;
            const decodedNewVal = nl2br(this.decodeBrackets(rumLaut(this.internalValue)));

            if (currentHtml !== decodedNewVal) {
                editor.innerHTML = decodedNewVal;
            }
        },

        decodeBrackets(str) {
            return str ? str.replace(/%5B/g, "[").replace(/%5D/g, "]") : "";
        },

        getLabel(name) {
            return this.settings?.exl?.[name] ?? name;
        },

        handleSmileyInsert(smiley) {
            this.saveSelection();
            this.restoreSelection();

            const editor = this.$refs.editor;
            if (!editor) return;

            editor.focus();

            const sel = window.getSelection();
            if (!sel || sel.rangeCount === 0) {
                this.insertAtEnd(smiley);
                return;
            }

            const range = sel.getRangeAt(0);

            if (!editor.contains(range.commonAncestorContainer)) {
                this.insertAtEnd(smiley);
                return;
            }

            range.deleteContents();
            const textNode = document.createTextNode(smiley);
            range.insertNode(textNode);

            range.setStartAfter(textNode);
            range.setEndAfter(textNode);
            sel.removeAllRanges();
            sel.addRange(range);

            this.updateValue();
        },

        insertAtEnd(text) {
            const editor = this.$refs.editor;
            if (!editor) return;

            const textNode = document.createTextNode(text);
            editor.appendChild(textNode);

            const range = document.createRange();
            range.selectNodeContents(editor);
            range.collapse(false);
            const sel = window.getSelection();
            sel.removeAllRanges();
            sel.addRange(range);

            this.updateValue();
        },

        toggleFormat(format) {
            this.saveSelection();
            requestAnimationFrame(() => {
                this.restoreSelection();
                const sel = window.getSelection();
                if (!sel || sel.rangeCount === 0) return;
                const range = sel.getRangeAt(0);

                const selectedText = range.toString();
                const isTextSelected = !range.collapsed && selectedText.trim().length > 0;

                const parent = range.commonAncestorContainer.nodeType === 3 ? range.commonAncestorContainer.parentNode : range.commonAncestorContainer;

                if (/^h[1-6]$/.test(format)) {
                    if (parent.nodeName.toLowerCase() === format) {
                        const p = document.createElement('p');
                        p.innerHTML = parent.innerHTML;
                        parent.parentNode.replaceChild(p, parent);

                        const newRange = document.createRange();
                        newRange.selectNodeContents(p);
                        newRange.collapse(false);
                        sel.removeAllRanges();
                        sel.addRange(newRange);
                    } else {
                        const h = document.createElement(format);

                        if (isTextSelected) {
                            h.textContent = selectedText;
                            range.deleteContents();
                            range.insertNode(h);

                            const newRange = document.createRange();
                            newRange.selectNodeContents(h);
                            newRange.collapse(false);
                            sel.removeAllRanges();
                            sel.addRange(newRange);
                        } else if (parent.nodeName.toLowerCase().match(/^h[1-6]$/)) {
                            const newHeading = document.createElement(format);
                            newHeading.innerHTML = parent.innerHTML;
                            parent.parentNode.replaceChild(newHeading, parent);

                            const newRange = document.createRange();
                            newRange.selectNodeContents(newHeading);
                            newRange.collapse(false);
                            sel.removeAllRanges();
                            sel.addRange(newRange);
                        } else {
                            let contextElement = parent;
                            while (contextElement &&
                                !['P', 'DIV', 'H1', 'H2', 'H3', 'H4', 'H5', 'H6'].includes(contextElement.nodeName) &&
                                contextElement !== this.$refs.editor) {
                                contextElement = contextElement.parentNode;
                            }

                            if (contextElement && contextElement !== this.$refs.editor && contextElement.textContent.trim()) {
                                const elementText = contextElement.textContent.trim();
                                h.textContent = elementText;
                                contextElement.parentNode.replaceChild(h, contextElement);

                                const newRange = document.createRange();
                                newRange.selectNodeContents(h);
                                newRange.collapse(false);
                                sel.removeAllRanges();
                                sel.addRange(newRange);
                            } else {
                                h.textContent = 'Überschrift';
                                range.insertNode(h);

                                const newRange = document.createRange();
                                newRange.setStart(h.firstChild || h, 0);
                                newRange.collapse(true);
                                sel.removeAllRanges();
                                sel.addRange(newRange);
                            }
                        }
                    }
                    this.updateValue();
                    return;
                }

                if (format === 'email') {
                    const email = prompt("E-Mail-Adresse eingeben:");
                    if (!email) return;
                    const a = document.createElement("a");
                    a.href = `mailto:${email}`;
                    a.textContent = selectedText || email;
                    range.deleteContents();
                    range.insertNode(a);
                    const newRange = document.createRange(); newRange.setStartAfter(a); newRange.collapse(true);
                    sel.removeAllRanges(); sel.addRange(newRange);
                    this.updateValue();
                    return;
                }

                if (format === 'link') {
                    const url = prompt("URL eingeben:", "https://");
                    if (!url) return;
                    const a = document.createElement("a");
                    a.href = url; a.target = "_blank"; a.rel = "noopener noreferrer";
                    a.textContent = selectedText || url;
                    range.deleteContents();
                    range.insertNode(a);
                    const newRange = document.createRange(); newRange.setStartAfter(a); newRange.collapse(true);
                    sel.removeAllRanges(); sel.addRange(newRange);
                    this.updateValue();
                }
            });
        },

        toggleBold() {
            this.saveSelection();
            document.execCommand("bold");
            this.$nextTick(() => {
                this.restoreSelection();
                this.updateValue();
            });
        },

        toggleItalic() {
            this.saveSelection();
            document.execCommand("italic");
            this.$nextTick(() => {
                this.restoreSelection();
                this.updateValue();
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
                let parent = range.commonAncestorContainer;
                while (parent && parent.nodeType === 3) parent = parent.parentNode;
                if (parent && parent.tagName && parent.tagName.toLowerCase() === "code") {
                    const selectedText = range.toString();
                    const before = parent.textContent.substring(0, range.startOffset);
                    const after = parent.textContent.substring(range.endOffset);
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
                    this.updateValue();
                    return;
                }
                if (range.collapsed) return;
                const code = document.createElement("code");
                code.textContent = range.toString();
                range.deleteContents();
                range.insertNode(code);
                const newRange = document.createRange();
                newRange.setStartAfter(code);
                newRange.collapse(true);
                sel.removeAllRanges();
                sel.addRange(newRange);
                this.updateValue();
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
            this.restoreSelection();
        },

        toggleList(type) {
            this.saveSelection();
            const cmd = type === "ul" ? "insertUnorderedList" : "insertOrderedList";
            document.execCommand(cmd);
            this.$nextTick(() => {
                this.restoreSelection();
                this.updateValue();
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
            const br = document.createElement("br");
            this.$nextTick(() => {
                let range;
                const selection = window.getSelection();
                if (this.savedSelection) {
                    try {
                        range = document.createRange();
                        range.setStart(this.savedSelection.startContainer, this.savedSelection.startOffset);
                        range.setEnd(this.savedSelection.endContainer, this.savedSelection.endOffset);
                    }
                    catch {
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
                this.savedSelection = null;
            });
        },

        handleImageUpload(imageUrl) {
            this.insertImageIntoEditor(imageUrl);
        },

        insertImage(imageUrl) {
            this.insertImageIntoEditor(imageUrl);
        },

        getSelection() {
            const sel = window.getSelection();
            return sel.rangeCount > 0 ? sel.getRangeAt(0) : null;
        }
    }
};
</script>

<style>
.edit0R {
    max-width: 1177px;
    overflow: auto;
    height: auto;
}

.editor h1{
    font-weight:800;
    font-size:2.25em;
    margin-top:0;
    margin-bottom:.8888889em;
    line-height:1.1111111;
}

.editor h2{
    font-size:1.875rem;
    font-weight:600;
    margin-top:1rem;
    margin-bottom:.5rem;
}

.editor h3{
    font-size:1.5rem;
    font-weight:600;
    margin-top:.75rem;
    margin-bottom:.25rem;
}

.editor h4{
    font-weight:600;
    font-size:1.25rem;
}

.editor h5{
    font-weight:600;
    font-size:1.1rem;
}

.editor h6{
    font-weight:500;
    font-size:1.05rem;
}

.icon-btn{
    padding:.4rem .6rem;
    border-radius:9999px;
    transition:background .2s;
    cursor:pointer;
    margin-right:.25rem;
    background:transparent;
}

.icon-btn:hover{
    background-color: var(--tw-bg-opacity);
}

.editor a:link,
.editor a:visited,
.editor a:active{
    text-decoration:underline;
    color:#a7dff5 !important;
}

.editor OL LI{
    list-style-type: decimal;
}

.editor-error-message{
    color:#a00;
}

.editor{
    white-space:pre-wrap !important;
    word-wrap:break-word !important;
}

.editor code{
    background-color:#ccc !important;
    padding:4px;
    font-family:'Courier New', Courier, monospace;
}

.editor P{
    margin-top:1.2em;
    margin-bottom:1.2em;
}
</style>
