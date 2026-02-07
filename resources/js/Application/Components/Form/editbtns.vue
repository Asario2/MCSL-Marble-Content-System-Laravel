<template>
  <div class="whitespace-nowrap editbtn-container">
    <span
      v-if="(rights.edit == 1 && !noedit) || (users_id && users_id == page.props.user.id && CleanTable() == 'contacts')"
    >
      <a :href="'/admin/tables/edit/' + id + '/' + table" @click.stop>
        <IconPencil
          class="sm-pencil cursor-pointer text-layout-sun-600 dark:text-layout-night-900"
        />
      </a>
      &nbsp;&nbsp;
    </span>

    <span v-if="rights.delete == 1">
      <form @submit.prevent="deletePost" style="display:inline">
        <button @click.stop.prevent="confirmDelete" type="button">
          <IconTrash class="sm-pencil cursor-pointer" />
        </button>
      </form>
    </span>
  </div>
</template>

<script>
import axios from "axios";
import IconPencil from "@/Application/Components/Icons/Pencil.vue";
import IconTrash from "@/Application/Components/Icons/Trash.vue";
import { toastBus } from "@/utils/toastBus";
import { CheckTRights, CleanTable } from "@/helpers";

export default {
  name: "EditBtns",

  components: { IconTrash, IconPencil, toastBus },

  props: {
    id: Number,
    table: String,
    noedit: { default: false },
    pm: Boolean,
    users_id: [String, Number],
  },

  data() {
    return {
      rights: {
        edit: false,
        delete: false,
      },
    };
  },

  async mounted() {
     if (!this.table) {
    console.error("editbtns: table prop ist leer oder falsch!");
  }

    this.rights.edit = await CheckTRights("edit", this.table);
    this.rights.delete = await CheckTRights("delete", this.table);

  },

  methods: {
    CleanTable,

    hasRight(right, table) {
      return CheckTRights(right, table);
    },

    async confirmDelete() {
      if (!confirm("Sind Sie sicher, dass Sie diesen Eintrag löschen möchten?"))
        return;
      await this.deletePost();
    },

    async deletePost() {
      try {
        if (!this.hasRight("delete", this.table)) {
          alert("Sie haben nicht die benötigten Rechte zum Löschen.");
          return;
        }

        let t = this.pm ? "pm" : "tables";

//         console.log(`DELETE: /admin/${t}/delete/${this.table}/${this.id}`);

        const response = await axios.delete(
          `/admin/${t}/delete/${this.table}/${this.id}`,
          { params: { edit: "blogposts.index" } }
        );
        console.log(response.data);
        window.toastBus.emit(response.data);
        this.$inertia.reload();
      } catch (err) {
        console.error("Fehler beim Löschen:", err);
      }
    },
  },
};
</script>

<style scoped>
.sm-pencil {
  width: 20px;
  display: inline;
}

.editbtn-container {
  display: inline-flex;
  align-items: center;
}
</style>
