<template>
  <span class="smbg">
    <span v-for="(val, key) in smilies" :key="key">
      <img
        @click.stop.prevent="addSmiley(key)"
        :src="'/images/smilies/icon_' + val + '.gif'"
        :alt="key"
        :title="key"
        class="inline_alt cursor-pointer"
      />
      &nbsp;
    </span>
  </span>
</template>

<script>
export default {
  name: "SmileysAdd",
  props: {
    name: {
      type: String,
      required: true
    },
    editor: {
      type: String,
      required: true
    }
  },
  data() {
    return {
      smilies: {
        ";)": "wink",
        ":D": "biggrin",
        ":)": "smile",
        ":o": "surprised",
        ":shock:": "eek",
        ":?": "confused",
        " 8)": "cool",
        ":lol:": "lol",
        ":x": "mad",
        ":P": "razz",
        ":mrgreen:": "mrgreen",
        ":arrow:": "arrow",
        ":cry:": "cry",
        ":evil:": "evil",
        ":!:": "exclaim",
        ":{": "frown",
        ":idea:": "idea",
        ":|": "neutral",
        ":question:": "question",
        ":shy:": "redface",
        ":roll:": "rolleyes",
        ":(": "sad",
        "^^": "twisted",
        ":ying:": "ying",
        ':ugly:': 'ugly',
        ':catch:': "catch",
        ":holy:": 'holy',
      }
    };
  },
  emits: ['insert-smiley'],
  methods: {
    getEditorElement() {
      // Verwende die editor prop, die von der Hauptkomponente übergeben wird
      const editorId = "editor_" + this.editor;
      const el = document.getElementById(editorId);
      if (!el) {
        console.warn(`Editor with id '${editorId}' not found`);
        console.warn('Available editors:', document.querySelectorAll('[contenteditable="true"]'));
      }
      return el;
    },

    saveRange() {
      const editorEl = this.getEditorElement();
      if (!editorEl) return;

      const sel = window.getSelection();
      if (sel.rangeCount > 0) {
        const range = sel.getRangeAt(0);
        // Nur speichern, wenn die Selection im Editor ist
        if (editorEl.contains(range.commonAncestorContainer)) {
          editorEl.__savedRange = range.cloneRange();
        }
      }
    },

    addSmiley(smiley) {
      console.log('Adding smiley:', smiley, 'to editor:', this.editor);

      // Emit Event zur Hauptkomponente
      this.$emit('insert-smiley', smiley);

      // Zusätzlich direkt einfügen (Fallback)
    //   this.insertSmileyDirectly(smiley);
    },

    insertSmileyDirectly(smiley) {
      const editorEl = this.getEditorElement();
      if (!editorEl) {
        console.error('Editor element not found for direct insertion');
        return;
      }

      editorEl.focus();

      // Range wiederherstellen
      let range;
      const sel = window.getSelection();

      if (editorEl.__savedRange) {
        try {
          range = editorEl.__savedRange;
          sel.removeAllRanges();
          sel.addRange(range);
        } catch (e) {
          console.warn('Failed to restore saved range:', e);
          range = null;
        }
      }

      // Wenn keine gültige Range, am Ende einfügen
      if (!range || !sel.rangeCount) {
        range = document.createRange();
        range.selectNodeContents(editorEl);
        range.collapse(false);
        sel.removeAllRanges();
        sel.addRange(range);
      } else {
        range = sel.getRangeAt(0);
      }

      // Sicherstellen, dass die Range im Editor ist
      if (!editorEl.contains(range.commonAncestorContainer)) {
        range = document.createRange();
        range.selectNodeContents(editorEl);
        range.collapse(false);
        sel.removeAllRanges();
        sel.addRange(range);
      }

      // Smiley einfügen
      const textNode = document.createTextNode(smiley + ' ');
      range.deleteContents();
      range.insertNode(textNode);

      // Cursor nach dem Smiley positionieren
      range.setStartAfter(textNode);
      range.setEndAfter(textNode);
      range.collapse(false);
      sel.removeAllRanges();
      sel.addRange(range);

      // Range für nächsten Smiley speichern
      editorEl.__savedRange = range.cloneRange();

      // Input-Event auslösen
      const inputEvent = new Event('input', { bubbles: true });
      editorEl.dispatchEvent(inputEvent);

      // Change-Event auslösen (für Kompatibilität)
      const changeEvent = new Event('change', { bubbles: true });
      editorEl.dispatchEvent(changeEvent);

      console.log('Smiley inserted successfully');
    },

    // Alternative Methode für einfacheres Einfügen
    insertSmileySimple(smiley) {
      const editorEl = this.getEditorElement();
      if (!editorEl) return;

      editorEl.focus();

      // Aktuelle Selection verwenden oder Ende des Editors
      const sel = window.getSelection();
      let range;

      if (sel.rangeCount > 0) {
        range = sel.getRangeAt(0);
        // Prüfen ob Selection im Editor ist
        if (!editorEl.contains(range.commonAncestorContainer)) {
          range = null;
        }
      }

      if (!range) {
        range = document.createRange();
        range.selectNodeContents(editorEl);
        range.collapse(false);
      }

      // Text einfügen
      const textNode = document.createTextNode(smiley + ' ');
      range.deleteContents();
      range.insertNode(textNode);

      // Cursor positionieren
      range.setStartAfter(textNode);
      range.collapse(true);
      sel.removeAllRanges();
      sel.addRange(range);

      // Event auslösen
      editorEl.dispatchEvent(new Event('input', { bubbles: true }));
    }
  },
  mounted() {
    console.log('Smileys component mounted for editor:', this.editor);

    const editorEl = this.getEditorElement();
    if (!editorEl) {
      console.error('Editor not found during mount');
      return;
    }

    // Event-Listener für Selection-Speicherung
    const saveSelectionHandler = () => this.saveRange();

    editorEl.addEventListener("keyup", saveSelectionHandler);
    editorEl.addEventListener("mouseup", saveSelectionHandler);
    editorEl.addEventListener("focus", saveSelectionHandler);
    editorEl.addEventListener("click", saveSelectionHandler);

    // Cleanup-Funktion speichern
    this._cleanup = () => {
      editorEl.removeEventListener("keyup", saveSelectionHandler);
      editorEl.removeEventListener("mouseup", saveSelectionHandler);
      editorEl.removeEventListener("focus", saveSelectionHandler);
      editorEl.removeEventListener("click", saveSelectionHandler);
    };
  },
  beforeUnmount() {
    // Event-Listener entfernen
    if (this._cleanup) {
      this._cleanup();
    }
  }
};
</script>

<style scoped>
.smbg {
  display: block;
  padding: 8px;
  border-radius: 4px;
  margin-bottom: 8px;
}

.inline_alt {
  display: inline-block;
  cursor: pointer;
  margin: 2px;
  transition: transform 0.2s ease;
  vertical-align: middle;
}

.inline_alt:hover {
  transform: scale(1.1);
}

.cursor-pointer {
  cursor: pointer;
}

/* Dark mode support */
@media (prefers-color-scheme: dark) {
  .smbg {
    background-color: #2d3748;
  }
}
</style>
