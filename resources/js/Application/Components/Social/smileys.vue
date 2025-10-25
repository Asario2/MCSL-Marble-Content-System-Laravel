<template>
    <span class="smbg">
      <span v-for="(val, xkey) in smilies" :key="xkey">
    <img
          @click="add_smilie(xkey, editor)"
          :src="'/images/smilies/icon_' + val + '.gif'"
          :alt="smilie"
          :title="smilie"
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
      editor: String,
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
          ":mrgreen": "mrgreen",
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
        }
      };
    },
    methods: {
      add_smilie(sm, edit) {
        const el = document.getElementById(edit);
        if (el && el.isContentEditable) {
            el.focus();
            const sel = window.getSelection();
            const range = sel.getRangeAt(0);
            range.deleteContents();
            range.insertNode(document.createTextNode(sm));
            // Cursor nach dem eingefügten Smiley setzen
            range.collapse(false);
            sel.removeAllRanges();
            sel.addRange(range);
        }
    }
    }
  };
  </script>

  <style>
  .inline_alt {
    display: inline;
  }
  /* .smbg {
    background-color: #fff;
  } */
  </style>
