    <template>
    <table class="w-full table-auto border-collapse">
        <thead>
        <tr class="text-left">
            <!-- Newsletter-Empfänger auswählen -->
            <th class="w-12 pl-4">
            <input
                type="checkbox"
                :checked="newsletterSelected"
                @change="toggleNewsletterSelect($event.target.checked)"
                aria-label="Newsletter-Empfänger auswählen"
                class="w-4 h-4 rounded text-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-400"
            />
            </th>
            <th class="py-2 pr-4">Newsletter-Empfänger</th>

            <!-- Alle Benutzer (nur mit Recht) -->
            <th class="w-12 pl-4" v-if="modulRights?.SendMailToAll">
            <input
                type="checkbox"
                :checked="allSelected"
                @change="toggleSelectAll($event.target.checked)"
                aria-label="Alle Benutzer auswählen"
                class="w-4 h-4 rounded text-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-400"
            />
            </th>
            <th class="py-2 pr-4" v-if="modulRights?.SendMailToAll">Alle Benutzer</th>
        </tr>
        </thead>

        <tbody>
        <tr
            v-for="user in users"
            :key="user.id"
            class="border-t last:border-b hover:bg-gray-50 dark:hover:bg-gray-800"
        >
            <td class="pl-4 align-center">
            <input
                type="checkbox"
                :value="user.id"
                :id="'u' + user.id"
                :checked="selectedIds.includes(user.id)"
                @change="toggleItem(user.id, $event.target.checked)"
                :aria-label="`Auswählen ${user.name}`"
                class="w-4 h-4 rounded text-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-400"
            />
            </td>

            <td class="py-3 pr-4 align-middle">
            <div class="flex items-center gap-3">
                <div class="flex-shrink-0">
                <img
                    v-if="user.avatar"
                    :src="user.avatar"
                    :alt="user.name + ' avatar'"
                    class="w-8 h-8 rounded-full object-cover"
                />
                <div
                    v-else
                    class="w-8 h-8 rounded-full bg-gray-200 dark:bg-gray-700 flex items-center justify-center text-sm text-gray-600 dark:text-gray-300"
                >
                <label class="cursor-pointer" :for="'u' + user.id">{{ initials(user.name) }}</label>

                </div>
                </div>
                <div>
                <div class="font-medium text-sm text-gray-900 dark:text-gray-100">
                    <label class="cursor-pointer" :for="'u' + user.id">{{ user.name }}</label>
                </div>
                </div>
            </div>
            </td>
        </tr>

        <tr v-if="users.length === 0">
            <td colspan="3" class="py-6 text-center text-gray-500 dark:text-gray-400">
            Keine Benutzer vorhanden.
            </td>
        </tr>
        </tbody>
    </table>
    </template>


    <script>
    import { loadRights } from "@/helpers";
    // import { nextTick } from "vue";

    export default {
    name: "UserSelect",

    props: {
        users: {
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
        selectedIds: [...this.modelValue],
        newsletterSelected: false,
        modulRights: {},
        }
    },

    computed: {
        allSelected() {
        return this.users.length > 0 && this.selectedIds.length === this.users.length;
        },
    },

    watch: {
    modelValue(newVal) {
        this.selectedIds = [...newVal]; // synchronisieren beim Prop-Update
    },
    selectedIds: {
        handler(newVal) {
        this.$emit("update:modelValue", newVal); // gibt IDs an Parent zurück
        this.emitSelection();
        },
        deep: true,
    }
    },

    methods: {
        async loadRights() {
        this.modulRights = await loadRights();
        },

    toggleNewsletterSelect(checked) {
    this.newsletterSelected = checked;

    const newsletterIds = this.users
        .filter(u => typeof u.xch_newsletter === "string" && u.xch_newsletter == "1")
        .map(u => u.id);

    if (checked) {
        // IDs hinzufügen, keine Duplikate
        this.selectedIds = Array.from(new Set([...this.selectedIds, ...newsletterIds]));
    } else {
        // Newsletter-IDs entfernen
        this.selectedIds = this.selectedIds.filter(id => !newsletterIds.includes(id));
    }

    sessionStorage.setItem('selectedUsers', JSON.stringify(this.selectedIds));
    this.emitSelection();
    },

    toggleUser(id, checked) {
        const current = [...this.selectedIds];
        if (checked && !current.includes(id)) current.push(id);
        else if (!checked) current.splice(current.indexOf(id), 1);
        this.selectedIds = current;

        // in SessionStorage speichern
        sessionStorage.setItem('selectedUsers', JSON.stringify(this.selectedIds));

        // an Parent senden
        this.emitSelection();
    },

    toggleSelectAll(checked) {
    this.selectedIds = checked ? this.users.map(u => u.id) : [];
    sessionStorage.setItem('selectedUsers', JSON.stringify(this.selectedIds));
    this.emitSelection();
    },
emitSelection() {
  if (!this.users) return; // <-- verhindert Fehler

  const selectedItems = this.users
    .filter(u => this.selectedIds.includes(u.id))
    .map(u => ({ id: u.id, name: u.name, type: 'user' }));

  this.$emit('selection', selectedItems);
},

        initials(name) {
        if (!name) return "";
        return name
            .split(" ")
            .map((n) => n.charAt(0))
            .slice(0, 2)
            .join("")
            .toUpperCase();
        },
    toggleItem(id, checked) {
        this.toggleUser(id, checked);
    },
    },

    async mounted() {
    this.$nextTick(() => {
        let storedUsers = [];
        try { storedUsers = JSON.parse(sessionStorage.getItem('selectedUsers') || '[]'); }
        catch { storedUsers = []; }
    this.selectedIds = Array.from(new Set([
    ...storedUsers,
    ...(this.modelValue || [])
    ]));

        this.emitSelection();
    });
    await this.loadRights();
    },
    };
    </script>
