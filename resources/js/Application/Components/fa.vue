<script>
import { FontAwesomeIcon } from "@fortawesome/vue-fontawesome"; // Importiere FontAwesome-Icon-Komponenten
import { library } from "@fortawesome/fontawesome-svg-core"; // Importiere die Bibliothek
import { faTrashCan } from "@fortawesome/free-solid-svg-icons"; // Importiere das spezifische Icon

library.add(faTrashCan); // Füge das Icon der Bibliothek hinzu

export default {
    components: {
        FontAwesomeIcon, // Registriere die Vue-Komponente
    },
};
</script>

