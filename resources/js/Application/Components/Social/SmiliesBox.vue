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
  const textarea = document.getElementById("editor_" + this.name);
  if (!textarea) return;

  const start = textarea.selectionStart;
  const end = textarea.selectionEnd;

  // Smiley-Code einfügen
  textarea.value =
    textarea.value.substring(0, start) + code + textarea.value.substring(end);

  // Cursor setzen
  textarea.selectionStart = textarea.selectionEnd = start + code.length;

  // Vue reactive value lokal aktualisieren
  this.newComment = textarea.value;

  // **Parent benachrichtigen**
  this.$emit("update:comment", this.newComment);

  textarea.focus();
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

