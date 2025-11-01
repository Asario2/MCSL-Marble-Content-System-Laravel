<template>
    <span class="smbg" style="overflow-x:auto; white-space:nowrap; height:40px; display:block;">
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
    name: "SmileysBox",
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
    computed:{

    },
    methods: {
       add_smilie(sm) {
      const el = document.getElementById(this.editor);
      if (!el) return;

      // Textarea einfügen
      const start = el.selectionStart;
      const end = el.selectionEnd;
      const value = el.value;
      el.value = value.substring(0, start) + sm + value.substring(end);
      el.selectionStart = el.selectionEnd = start + sm.length;
      el.focus();

      // Update v-model
      el.dispatchEvent(new Event('input'));
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
