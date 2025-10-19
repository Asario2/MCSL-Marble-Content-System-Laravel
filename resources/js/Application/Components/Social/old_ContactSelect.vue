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
            class="w-4 h-4 rounded text-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-400"
            :checked="selectedContactIds.includes(c.id)"
            @change="toggleContact(c.id, $event.target.checked)"
          />
        </td>
        <td class="py-2 pr-4 text-left">{{ c.name }}</td>
      </tr>
    </tbody>
  </table>
</template>

<script>
import { loadRights } from "@/helpers";

export default {
  name: "UserContactSelect",
  props: {
    contacts: Array,
    modelValue: { type: Array, default: () => [] }, // IDs vom Parent
  },
  data() {
    return {
      selectedContactIds: [...this.modelValue],
      modulRights: {},
    };
  },
  watch: {
    modelValue(newVal) {
      // Nur synchronisieren, wenn sich parentseitig was geändert hat
      if (JSON.stringify(newVal) !== JSON.stringify(this.selectedContactIds)) {
        this.selectedContactIds = [...newVal];
      }
    },
  },
  computed: {
    allSelected() {
      return this.contacts.length > 0 && this.selectedContactIds.length === this.contacts.length;
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
        this.selectedContactIds = this.selectedContactIds.filter(v => v !== id);
      }

      // IDs an Parent
      this.$emit("update:modelValue", [...this.selectedContactIds]);

      // Vollständige Objekte an Parent, inkl. vorheriger Auswahl
      this.emitSelection();
    },

    toggleSelectAll(checked) {
      this.selectedContactIds = checked ? this.contacts.map(c => c.id) : [];
      this.$emit("update:modelValue", [...this.selectedContactIds]);
      this.emitSelection();
    },

    emitSelection() {
      // Alle aktuell ausgewählten Objekte
      const selectedItems = this.contacts
        .filter(c => this.selectedContactIds.includes(c.id))
        .map(c => ({ id: c.id, name: c.name, type: "contact" }));

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
    // Direkt nach dem Mount einmal emitten
    this.$nextTick(() => this.emitSelection());
  },
};
</script>
