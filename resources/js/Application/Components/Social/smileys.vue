<template>
  <span class="smbg">
    <span v-for="(val, key) in smilies" :key="key">
      <img
       @click="addSmiley(key)"
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
  methods: {
    getEditorElement() {
      const editorId = "editor_" + this.name;
      const el = document.getElementById(editorId);
      if (!el) console.warn(`Editor with id '${editorId}' not found`);
      return el;
    },

    saveRange() {
      const editorEl = this.getEditorElement();
      if (!editorEl) return;
      const sel = window.getSelection();
      if (sel.rangeCount > 0) {
        editorEl.__savedRange = sel.getRangeAt(0).cloneRange();
      }
    },

    addSmiley(smiley) {
      const editorEl = this.getEditorElement();
      if (!editorEl) return;

      editorEl.focus();

      // Range wiederherstellen
      if (editorEl.__savedRange) {
        const sel = window.getSelection();
        sel.removeAllRanges();
        sel.addRange(editorEl.__savedRange);
      }

      const sel = window.getSelection();
      let range = sel.rangeCount ? sel.getRangeAt(0) : null;

      if (!range || !editorEl.contains(range.commonAncestorContainer)) {
        range = document.createRange();
        range.selectNodeContents(editorEl);
        range.collapse(false);
        sel.removeAllRanges();
        sel.addRange(range);
      }

      const textNode = document.createTextNode(smiley);
      range.deleteContents();
      range.insertNode(textNode);

      range.setStartAfter(textNode);
      range.setEndAfter(textNode);
      sel.removeAllRanges();
      sel.addRange(range);

      // Range für nächsten Smiley speichern
      editorEl.__savedRange = range.cloneRange();

      // Input-Event auslösen
      editorEl.dispatchEvent(new Event("input", { bubbles: true }));
    }
  },
  mounted() {
    const editorEl = this.getEditorElement();
    if (!editorEl) return;

    // Range speichern bei User-Aktionen
    editorEl.addEventListener("keyup", this.saveRange);
    editorEl.addEventListener("mouseup", this.saveRange);
    editorEl.addEventListener("focus", this.saveRange);
  }
};
</script>

<style>
.inline_alt {
  display: inline;
}
.cursor-pointer {
  cursor: pointer;
}
</style>

