<template>
  <span v-if="(hasRight('edit', table) && !noedit) || (users_id && users_id == page.props.user.id)">
    <a :href="'/admin/tables/edit/' + id + '/' + table" @click.stop>
      <IconPencil class="sm-pencil cursor-pointer text-layout-sun-600 dark:text-layout-night-900" />
    </a>
    &nbsp;&nbsp;
  </span>

  <span v-if="hasRight('delete', table)">
    <form @submit.prevent="deletePost" style="display:inline">
      <button @click.stop.prevent="confirmDelete" type="button">
        <IconTrash class="sm-pencil cursor-pointer" />
      </button>
    </form>
  </span>
</template>

<script>
import axios from "axios";
import IconPencil from "@/Application/Components/Icons/Pencil.vue";
import IconTrash from "@/Application/Components/Icons/Trash.vue";
import { toastBus } from '@/utils/toastBus';

// 🔥 Optimiertes Rechtssystem
import { hasRightSync, loadAllRights } from '@/utils/rights';

export default {
    name: "editbtns",

    components: { IconTrash, IconPencil, toastBus },

    props: {
        id: Number,
        table: String,
        noedit: { default: false },
        pm: Boolean,
        users_id: [String, Number]
    },

    async mounted() {

    },

    methods: {
        hasRight(right, table) {
            return hasRightSync(right, table);
        },

        async confirmDelete() {
            if (!confirm("Sind Sie sicher, dass Sie diesen Eintrag löschen möchten?")) return;
            await this.deletePost();
        },

        async deletePost() {
            try {
                if (!this.hasRight("delete", this.table)) {
                    alert("Sie haben nicht die benötigten Rechte zum Löschen.");
                    return;
                }

                let t = this.pm ? "pm" : "tables";

                console.log(`DELETE: /admin/${t}/delete/${this.table}/${this.id}`);

                const response = await axios.delete(`/admin/${t}/delete/${this.table}/${this.id}`, {
                    params: { edit: "blogposts.index" }
                });

                toastBus.emit("toast", response.data);
                this.$inertia.reload();

            } catch (err) {
                console.error("Fehler beim Löschen:", err);
            }
        }
    }
};
</script>

<style scoped>
.sm-pencil {
    width:20px;
    display:inline;
}
</style>
