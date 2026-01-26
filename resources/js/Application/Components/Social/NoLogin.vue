<template>
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
      placeholder="Email"
      :required="true"
    >
      <template #label>Email</template>
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
  },

  emits: ["update:modelValue"],

  computed: {
    // 🔁 v-model Bridge
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
      return valid ? null : "Bitte eine gültige Email eingeben";
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
