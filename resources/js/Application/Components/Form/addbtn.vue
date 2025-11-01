<template>
    <span v-if="hasRight('add', table) || safe">
        <Link :href="`/admin/tables/create/${table}`" class=" inline-flex items-center px-1 py-1.5 md:px-2 md:py-2 h-6 md:h-8 rounded-md font-medium text-xs tracking-widest disabled:opacity-25 transition cursor-pointer focus:ring focus:outline-none button_bg button_text_case_bg">
            <PlusCircle class="cursor-pointer " />&nbsp;<span class="tb">{{ text }}</span>
        </Link>
        &nbsp;&nbsp;
    </span>
</template>

<script>
import PlusCircle from "@/Application/Components/Icons/PlusCircle.vue";
import { Link } from "@inertiajs/vue3";
import { toastBus } from '@/utils/toastBus';
import { hasRight, loadAllRights, isRightsReady, hasRightSync } from '@/utils/rights';
import { CleanTable, CleanId } from '@/helpers';
import axios from 'axios'; // WICHTIG: axios importieren

export default {
    name: "EditButtons",
    components: {
        PlusCircle,
        Link,
    },
    props: {
        Redit: {
            type: [String, Number],
            default: 0
        },
        Rdelete: {
            type: [String, Number], // Korrektur: type sollte Array sein
            default: 0
        },
        table: {
            type: String,
            required: true
        },
        text: {
            type: String,
            default: "Erstellen",
        },
        safe:{
            type:Boolean,
            default:false,
        },
        

    },
    data() {
        return {
            rightsData: {},
            rightsReady: false,
        }
    },
    async mounted() {
        this.loadAllUsers();
        await loadAllRights();
        this.rightsReady = true;
//         console.log('EditButtons mounted - ID:', this.id, 'Table:', this.table);
    },
    methods: {
        async loadAllUsers()
        {
            const res = await axios.get("/api/user-id");
            this.uid_orig = res.data.id;
        },
        hasRight(right, table) {
            if(this.safe){
                return true;
            }
            const result = hasRightSync(right, table);
//             console.log(`Right check - ${right} for ${table}:`, result);
            return result;
        },

        async confirmDelete() {
            if (!this.hasRight('delete', this.table)) {
                alert("Sie haben nicht die benötigten Rechte zum Löschen des Datensatzes");
                return;
            }

            if (confirm('Sind Sie sicher, dass Sie diesen Datensatz löschen möchten?')) {
                await this.deletePost();
            }
        },

        async deletePost() {
            try {
//                 console.log(`Deleting: /admin/tables/delete/${this.table}/${this.id}`);

                const response = await axios.delete(`/admin/tables/delete/${this.table}/${this.id}`, {
                    params: {
                        edit: "blogposts.index",
                    }
                });

//                 console.log('Delete response:', response.data);
                toastBus.emit('toast', response.data);

                // Seite neu laden
                this.$inertia.reload();

            } catch (error) {
                console.error("Fehler beim Löschen:", error);
                toastBus.emit('toast', {
                    status: 'error',
                    message: 'Fehler beim Löschen des Datensatzes'
                });
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
</style>
