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
        <th class="py-2 pr-4 text-left">Alle Gruppen</th>
      </tr>
    </thead>
    <tbody>
      <tr v-for="g in usergroups" :key="g.id" class="border-t hover:bg-gray-50 dark:hover:bg-gray-800">
        <td class="pl-4 text-left">
          <input
            type="checkbox"
            :id="'group_' + g.id"
            :checked="selectedGroupIds.includes(g.id)"
            @change="toggleItem(g.id, $event.target.checked)"
            class="w-4 h-4 rounded text-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-400"
          />
        </td>
        <td class="py-2 pr-4 text-left">{{ g.name }}</td>
      </tr>
    </tbody>
  </table>
</template>

<script>
import { loadRights } from "@/helpers";

export default {
  name: "UserGroupSelect",
  props: {
    usergroups: Array,
    modelValue: { type: Array, default: () => [] }, // IDs vom Parent
    show: Boolean, // für Modal
  },
  data() {
    return {
      selectedGroupIds: [...(this.modelValue || [])],
      newsletterSelected: false,
      modulRights: {},
    };
  },
  watch: {
    modelValue(newVal) {
      this.selectedGroupIds = [...newVal]; // synchronisieren beim Prop-Update
    },
  show(newVal) {
    if (newVal) {
      let storedGroups = [];
      try {
        storedGroups = JSON.parse(sessionStorage.getItem('selectedGroups') || '[]');
      } catch {
        storedGroups = [];
      }
      this.selectedGroupIds = storedGroups;
      this.emitSelection();
    }

  },
  },
  computed: {
    allSelected() {
      const groups = this.usergroups || [];
      return groups.length > 0 && this.selectedGroupIds.length === groups.length;
    },
  },
  methods: {
    async loadRights() {
      this.modulRights = await loadRights();
    },
toggleItem(id, checked) {
  if (checked && !this.selectedGroupIds.includes(id)) {
    this.selectedGroupIds.push(id);
  } else if (!checked) {
    this.selectedGroupIds = this.selectedGroupIds.filter(v => v !== id);
  }

  // Display-Array aktualisieren
  this.selectedGroupsDisplay = this.usergroups
    .filter(g => this.selectedGroupIds.includes(g.id))
    .map(g => g.name);

  // SessionStorage
  sessionStorage.setItem('selectedGroups', JSON.stringify(this.selectedGroupIds));

  this.emitSelection();
},

    toggleSelectAll(checked) {
      this.selectedGroupIds = checked ? this.usergroups.map(g => g.id) : [];
      sessionStorage.setItem('selectedGroups', JSON.stringify(this.selectedGroupIds));
      this.emitSelection();
    },

emitSelection() {
  const selectedItems = this.usergroups
    .filter(g => this.selectedGroupIds.includes(g.id))
    .map(g => ({
      id: g.id,
      name: `{${g.name}}` // <--- hier wird der Name in {} gesetzt
    }));

  this.$emit('selection', selectedItems);
}
  },
  async mounted() {
    await this.loadRights();

    // SessionStorage auslesen
    let storedGroups = [];
    try {
      storedGroups = JSON.parse(sessionStorage.getItem('selectedGroups') || '[]');
    } catch {
      storedGroups = [];
    }
    this.selectedGroupIds = storedGroups;
    this.emitSelection();
  }
};
</script>
