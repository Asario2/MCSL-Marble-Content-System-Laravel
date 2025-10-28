<template>
<span v-if="hasRight('edit',table)">
<a :href="'/admin/tables/edit/'+ id+ '/' + table + ''" @click.stop><IconPencil class="sm-pencil cursor-pointer tw"></IconPencil></a>
&nbsp;&nbsp;</span>
<span v-if="hasRight('delete',table)">
<form @submit.prevent="deletePost" style="display:inline">
    <button @click.stop type="submit" onclick="return confirm('Sind Sie sicher, dass Sie diesen Blogbeitrag löschen möchten?');"><IconTrash class="sm-pencil cursor-pointer"></IconTrash></button>
</form>
</span>

</template>
<script>
import axios from axios;
import IconPencil from "@/Application/Components/Icons/Pencil.vue";
import { toastBus } from '@/utils/toastBus';
import IconTrash from "@/Application/Components/Icons/Trash.vue";
import { hasRight,loadAllRights,isRightsReady,hasRightSync } from '@/utils/rights';
import { CleanTable, CleanId } from '@/helpers';
export default {
    name: "editbtns",
    components: {
        IconTrash,
        IconPencil,
        CleanTable,
        CleanId,
        toastBus,
    },
    props: {
        Redit: {
            type: [String,Number],
            default:0
        },
        Rdelete: {String, default: 0,},
                id: { type: Number },
                table:{type:String},

    },
    data()
    {
        return {
        rightsData: {}, // Hier speichern wir die Rechte für den User
      rightsReady: false,
        }
    },
    computed:{
        darkMode(){
            this.darkMode = localStorage.getItem("theme");
        },
        isRightsReady() {
      return this.$isRightsReady; // Zugriff auf globale Methode
    },
    hasRight() {
      return this.$hasRight; // Zugriff auf globale Methode
    },

    },
    async mounted() {
        await loadAllRights();
    this.rightsReady = true;
    },
    methods: {
        hasRight(right, table) {
      return hasRightSync(right, table); // jetzt synchron & ohne await
    },
    //     hasRight(right, table) {
    //   return hasRight(right, table);
    //     },
//         async hasRight(right, table) {
//     // Überprüfe, ob die Rechte bereits geladen wurden
//     if (!this.rightsData[`${right}_${table}`] && table) {
//       // Wenn die Rechte noch nicht geladen wurden, lade sie
//       await this.checkRight(right, table);
//     }
//     // Wenn die Rechte nach dem Laden vorhanden sind, gib den Wert zurück
//     return this.rightsData[`${right}_${table}`] === 1; // Beispiel: Wenn das Recht '1' ist, erlauben wir den Zugriff
//   },

//   async checkRight(right, table) {
//     // Lade die Rechte für den User
//     const value = await GetRights(right, table);
//     // Speichere die geladenen Rechte im rightsData-Objekt
//     this.$set(this.rightsData, `${right}_${table}`, value);
//   },

    async deletePost() {
        try {
            if(!hasRight("delete",this.table))
            {
                 alert("Sie haben nicht die benötigten Rechet zum löschen des Datensatzes");
                 return "";
            }
            // console.log(`aad: admin/tables/delete/${this.table}/${this.id}`);
            // DELETE-Anfrage mit Parametern in der URL
            const response = await axios.delete(`/admin/tables/delete/${this.table}/${this.id}`, {
                params: {
                    edit: "blogposts.index",
                }
            });
            // console.log(response.data);
            toastBus.emit('toast', response.data); // ← erwartet { status: "...", message: "..." }
            this.$inertia.reload();
            // Optional: Seite neu laden oder Liste aktualisieren
        } catch (error) {
            console.error("Fehler beim Löschen:", error);
        }
    },

},
};

</script>
<style scoped>
.sm-pencil{
    width:20px;
    display:inline;
}
</style>
