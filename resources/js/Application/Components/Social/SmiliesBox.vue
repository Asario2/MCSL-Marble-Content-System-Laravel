<template>
  <span class="smbg" style="display: block;">
    <span v-for="(val, xkey) in smilies" :key="xkey">
      <img
        @click="AddSmiley(xkey)"
        :src="'/images/smilies/icon_' + val + '.gif'"
        :alt="xkey"
        :title="xkey"
        class="inline_alt cursor-pointer"
      />
      &nbsp;
    </span>
  </span>
</template>

<script>
export default {
  name: "SmileysBox",
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
        "8)": "cool",
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

        ":ugly:": "ugly",
        ":catch:": "catch",
        ":holy:": "holy",

      }
    };
  },
  methods: {
    AddSmiley(code) {
      const editorId = "editor_" + this.name;
      const el = document.getElementById(editorId);

      if (!el) {
        alert("Kein Editor mit ID '" + editorId + "' gefunden!");
        return;
      }

      // Hier das Alert — nur zum Testen
      alert("Füge Smiley ein: " + code + " in " + editorId);

      // Smiley als <img> einfügen
      const img = document.createElement("img");
      const smileyName = this.smilies[code];
      img.src = "/images/smilies/icon_" + smileyName + ".gif";
      img.alt = code;
      img.title = code;
      img.className = "inline_alt";

      // Smiley an Cursorposition einfügen
      const sel = window.getSelection();
      if (sel && sel.rangeCount > 0) {
        const range = sel.getRangeAt(0);
        range.deleteContents();
        range.insertNode(img);
        range.collapse(false);
      } else {
        el.appendChild(img);
      }

      // Cursor nach dem Bild setzen
      el.focus();
    }
  }
};
</script>

<style>
.inline_alt {
  display: inline;
  vertical-align: middle;
}
.cursor-pointer {
  cursor: pointer;
}
</style>

