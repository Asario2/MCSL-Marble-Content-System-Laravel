<template>
  <span v-if="hasRight('add', table)">
    <button-group>
      <input-icon-hyperlink
        v-if="hasRight('add', table)"
        :href="'/admin/tables/create/' + table"
        display_type="table"
        @click.stop
        class="text-black"
      >
        <template #icon>
          <!-- Flex-Container für Icon + Text -->
          <span class="flex items-center gap-2 text-black">
            <plus-circle class="w-5 h-5" />
            Erstellen
          </span>
        </template>
      </input-icon-hyperlink>

      <slot name="button"></slot>
    </button-group>
  </span>
</template>

    <script>
    import InputIconHyperlink from "@/Application/Components/Form/InputIconHyperlink.vue";
    import PlusCircle from "@/Application/Components/Icons/PlusCircle.vue";
    import { toastBus } from '@/utils/toastBus';
    import IconTrash from "@/Application/Components/Icons/Trash.vue";
    import { hasRight,loadAllRights,isRightsReady,hasRightSync } from '@/utils/rights';
    import { CleanTable, CleanId } from '@/helpers';
    export default {
        name: "newbtn",
        components: {
            IconTrash,
            PlusCircle,
            InputIconHyperlink,
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
