    <template>
    <div class="flex items-center h-6">
        <div class="flex items-center h-6">
            <input type="hidden" :name="name" :id="name + '_alt'" :value="modelValue">
            <input
                :id="name"
                :name="name"
                type="checkbox"
                class="w-5 h-5 rounded border focus:ring-3 bg-primary-sun-50 text-primary-sun-500 border-primary-sun-300 focus:ring-primary-sun-300 ring-offset-primary-sun-800 checked:bg-primary-sun-500 dark:bg-primary-night-50 dark:text-primary-night-500 dark:border-primary-night-300 dark:focus:ring-primary-night-300 dark:ring-offset-primary-night-800 dark:checked:bg-primary-night-500"
                :exValue="exValue"
                :checked="modelValue == 1"
                @change="toggle"
            />
        </div>
        <div class="ml-3 text-sm">
            <label :for="name" class="...">
                <span v-if="label">{{ label }}</span>
                <span v-else><slot></slot></span>
                <icon-exclamation-circle v-if="helptext" class="inline-block ml-1 w-5 h-5" />
            </label>
            <tippy v-if="helptext" v-html="helptext" placement="left"></tippy>
        </div>
    </div>
</template>

<script>
import IconExclamationCircle from "@/Application/Components/Icons/ExclamationCircle.vue";

export default {
    name: "Contents_Form_InputCheckbox",
    components: { IconExclamationCircle },
    emits: ["update:modelValue"],
    props: {
        name: { type: String },
        modelValue: { type: [Number, String, Boolean], default: 0 },
        label: { type: String, default: "" },
        helptext: { type: String, default: "" },
        exValue: [Number,String],
    },
    emits: ['update:modelValue'],

    methods: {
        toggle(e) {

      this.$emit("update:modelValue", e.target.checked ? 1 : 0);

        },

    },
    mounted() {
        //this.toggle();
    },
    computed: {
        local: {
      get()      { return Number(this.modelValue); },
      set(value) { this.$emit('update:modelValue', value ? 1 : 0); }
    }
    },
};
</script>

