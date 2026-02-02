<template>
    <div v-if="!hidden">
  <div class="mt-4 space-y-3">
    <InputFormText
      v-if="!nick"
      id="name"
      name="name"
      v-model="localForm.name"
      placeholder="Name"
      :required="true"
    >
      <template #label>Name</template>
    </InputFormText>

    <InputFormText
      id="email"
      name="email"
      type="email"
      v-model="localForm.email"
      :placeholder="em"
      :required="true"
    >
      <template #label>{{em}}</template>
    </InputFormText>

    <div v-if="emailError" class="text-red-500 text-sm">
      {{ emailError }}
    </div>

    <InputFormText
      id="password"
      name="password"
      type="password"
      v-model="localForm.password"
      placeholder="Für registrierte Benutzer"
    >
      <template #label>Passwort (Optional)</template>
    </InputFormText>
  </div>
  </div>
</template>

<script>
import InputFormText from "@/Application/Components/Form/InputFormText.vue";
import { toastBus } from "@/utils/toastBus";

export default {
  name: "NoLogin",

  components: {
    InputFormText,
  },

  props: {
    modelValue: {
      type: Object,
      default: () => ({
        name: null,
        email: null,
        password: null,
      }),
    },

    nick: {
      type: [Boolean, Number],
      default: true,
    },
    bothnicks:
    {
        type:[Boolean,Number],
        default:false,
    },
     hidden:
    {
        type:[Boolean,Number],
        default:false,
    },
  },

  emits: ["update:modelValue"],

  computed: {
    // 🔁 v-model Bridge
      em()
      {
        if(this?.bothnicks)
        {
            return "Email/Nickname";
        }
        else{
            return "Email";
        }
      },
    localForm: {
      get() {
        return this.modelValue;
      },
      set(value) {
        this.$emit("update:modelValue", value);
      },

    },

    emailError() {
      const email = this.localForm.email;
      if (!email) return null;

      const valid = /^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email);
      // valid ? null : "Bitte eine gültige Email eingeben";
      return null;
    },
  },

  watch: {
    // "localForm.email"(val) {
    //   console.log("📧 Email geändert (Child):", val);

    //   const valid = /^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(val);

    //   if (!valid || !val)  {
    //     toastBus.emit("toast", {
    //       status: "error",
    //       message: "Bitte eine gültige Email-Adresse eingeben",
    //     });
    //   }
    // },
  },
};
</script>
