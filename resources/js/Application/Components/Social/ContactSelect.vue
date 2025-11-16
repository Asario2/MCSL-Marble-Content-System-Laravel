<template>
  <table class="w-full table-auto border-collapse">
    <thead>
      <tr class="text-left">
        <th class="w-12 pl-4 text-left">
          <input
            type="checkbox"
            class="w-4 h-4 rounded text-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-400"
            :checked="allSelected"
            @change="toggleSelectAll($event.target.checked)"
          />
        </th>
        <th class="py-2 pr-4 text-left">Alle Kontakte</th>
      </tr>
    </thead>
    <tbody>
      <tr
        v-for="c in contacts"
        :key="c.id"
        class="border-t hover:bg-gray-50 dark:hover:bg-gray-800"
      >
        <td class="pl-4 text-left">
          <input
            type="checkbox"
            :id="'c' + c.id"
            class="w-4 h-4 rounded text-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-400"
            :checked="selectedContactIds.includes(c.id)"
            @change="toggleContact(c.id, $event.target.checked)"
          />
        </td>
        <td class="py-2 pr-4 text-left"><label class="cursor-pointer" :for="'c' + c.id">{{ c.name }}</label></td>
      </tr>
    </tbody>
  </table>
</template>

<script>
import { loadRights } from "@/helpers";

export default {
  name: "ContactSelect",
  props: {
    contacts: {
      type: Array,
      default: () => [],
    },
    modelValue: {
      type: Array,
      default: () => [],
    },
  },
  data() {
    return {
      selectedContactIds: [...this.modelValue],
      modulRights: {},
    };
  },
  computed: {
    allSelected() {
      return (
        this.contacts.length > 0 &&
        this.selectedContactIds.length === this.contacts.length
      );
    },
  },
  watch: {
    modelValue(newVal) {
      if (JSON.stringify(newVal) !== JSON.stringify(this.selectedContactIds)) {
        this.selectedContactIds = [...newVal];
      }
    },
  },
  methods: {
    async loadRights() {
      this.modulRights = await loadRights();
    },

    toggleContact(id, checked) {
      if (checked && !this.selectedContactIds.includes(id)) {
        this.selectedContactIds.push(id);
      } else if (!checked) {
        this.selectedContactIds = this.selectedContactIds.filter((v) => v !== id);
      }
      this.persistSelection();
    },

    toggleSelectAll(checked) {
      this.selectedContactIds = checked ? this.contacts.map((c) => c.id) : [];
      this.persistSelection();
    },

    persistSelection() {
      // Erzwungene neue Array-Referenz für Reaktivität
      this.selectedContactIds = [...this.selectedContactIds];

      // SessionStorage speichern
      sessionStorage.setItem(
        "selectedContacts",
        JSON.stringify(this.selectedContactIds)
      );

      // IDs an Parent
      this.$emit("update:modelValue", [...this.selectedContactIds]);

      // Vollständige Objekte an Parent
      this.emitSelection();
    },

    emitSelection() {
      const selectedItems = this.contacts
        .filter((c) => this.selectedContactIds.includes(c.id))
        .map((c) => ({ id: c.id, name: c.name, type: "contact" }));

      this.$emit("selection", selectedItems);
    },
  },
  async mounted() {
    await this.loadRights();

    const stored = sessionStorage.getItem("selectedContacts");
    if (stored) {
      try {
        this.selectedContactIds = JSON.parse(stored);
      } catch {
        this.selectedContactIds = [];
      }
    }

    this.$nextTick(() => this.persistSelection());
  },
};
</script>

