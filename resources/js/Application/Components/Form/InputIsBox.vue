<template>
    <div class="flex items-center h-6">
  <label class="inline-flex items-center cursor-pointer relative">
    <input type="hidden" :name="name" :id="name + '_alt'" :value="modelValue">
    <input
      :id="name"
      :name="name"
      type="checkbox"
      class="sr-only peer"
      :exValue="exValue"
      :checked="internalValue === 1"
      @change="toggle"
    />
    <div class="w-11 h-6 bg-gray-300 rounded-full peer-checked:bg-blue-600 transition-all duration-300"></div>
    <div class="w-5 h-5 bg-white rounded-full shadow absolute left-1 top-0.5 peer-checked:left-6 transition-all duration-300"></div>
  </label>

  <div class="ml-3 text-sm">
        <label :for="name">
          <span v-if="label">{{ label }}</span>
          <span v-else><slot /></span>
          <icon-exclamation-circle v-if="helptext" class="inline-block ml-1 w-5 h-5" />
        </label>
      </div>
    </div>

    <!-- ✅ Hidden-Feld, damit auch 0 gesendet wird -->
    <input type="hidden" :name="name" :id="name + '_alt'" value="1">
  </template>

  <script>
  import IconExclamationCircle from "@/Application/Components/Icons/ExclamationCircle.vue";

  export default {
    name: "Contents_Form_InputIsbox",
    components: { IconExclamationCircle },
    emits: ["update:modelValue"],
    props: {
      name: String,
      modelValue: {
        type: [Number, Boolean,String],
        default: 0,
      },
      label: String,
      helptext: String,
      exValue: [Number,String],
    },
    computed: {
        internalValue: {
            get() {
            return Number(this.exValue) == 1 ? 1 : 0;
            },
            set(val) {
            this.$emit("update:modelValue", val ? 1 : 0);
            }
        }
    },
    methods: {
        toggle(e) {
  const val = e.target.checked ? 1 : 0;
//   console.log("Checkbox toggled →", val);
  this.internalValue = val;
}

    },
  };
  </script>
