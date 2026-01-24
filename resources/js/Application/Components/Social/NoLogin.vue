<template>
  <div>
    <InputFormText v-if="!nick"
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

    <div v-if="emailError" class="text-red-500 text-sm mt-1">
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
import InputFormText from '@/Application/Components/Form/InputFormText.vue'

export default {
  name: 'NoLogin',
  components: { InputFormText },

  props: {
    modelValue: {
      type: Object,
      default: () => ({ name: null, email: null, password: null }),
    },
    nick: {
      type: [Number, Boolean],
      default: true,
    },
  },

  emits: ['update:modelValue'],

  computed: {
    localForm: {
      get() {
        return this.modelValue
      },
      set(value) {
        this.$emit('update:modelValue', value)
      }
    },

    emailError() {
      const email = this.localForm.email
    //   if (!email) return "Email ist Pflicht."
      // simple email regex
      const valid = /^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email)
      return valid ? null : ""
    }
  }
}
</script>
