function JumpMenu() {
    var l = document.getElementById("jmenu").value;
    if (document.getElementById("Loading_Layer")) {
        document.getElementById("Loading_Layer").style.display = "block";
    }
    location.href = l;
}
function wordwrapCutoff(str, width, maxLines = 4, brk = "\n") {
    if (!str) {
        return str;
    }
    var hell = "";
    // Regul�rer Ausdruck, um den String nach der angegebenen Breite zu umbrechen
    let regex = new RegExp(`.{1,${width}}(\\s|$)|.{${width}}|.+`, "g");

    // Text umbrechen
    let lines = str.match(regex);

    // Falls mehr als maxLines vorhanden sind, schneide den Rest ab
    if (lines.length > maxLines) {
        lines = lines.slice(0, maxLines);
        var hell = "...";
    }

    // Verbinde die umgebrochenen Zeilen mit dem Umbruchzeichen
    return lines.join(brk).trim() + hell;
}
function GetDom(){
    let url = window.location.search;
    url = url.replace("www.",'');
    const parts = url.split(".");
    const dom = parts[0];
    return dom;
}
function addslashes(str) {
    return (str + "")
        .replace(/\\/g, "\\\\") // Backslashes escapen
        .replace(/'/g, "\\'") // Einfache Anf�hrungszeichen escapen
        .replace(/"/g, '\\"') // Doppelte Anf�hrungszeichen escapen
        .replace(/\0/g, "\\0"); // Null-Bytes escapen
}
var $_GET = {};
if (document.location?.toString().indexOf("?") !== -1) {
    var query = document.location
        ?.toString()
        // get the query string
        .replace(/^.*?\?/, "")
        // and remove any existing hash string (thanks, @vrijdenker)
        .replace(/#.*$/, "")
        .split("&");

    for (var i = 0, l = query.length; i < l; i++) {
        var aux = decodeURIComponent(query[i]).split("=");
        $_GET[aux[0]] = aux[1];
    }
}
function WO(lname, adr, wname, format, hname, id) {
    if (document.getElementById(lname)) {
        adr = adr + document.getElementById(lname).value;
    }
    if (document.getElementById(hname)) {
        adr = adr + document.getElementById(hname).value;
    }
    var xe = $j("#" + id).val();
    if (!xe) {
        xc = "refr_img.png";
    } else {
        xc = xe;
    }
    // if($k('#' + id).val() !== '')
    // {
    adr = adr + "&cvalue=" + xc;
    // }
    window.open(adr, wname, format);
}
function togg2(div) {
    if (document.getElementById(div).style.display == "none") {
        document.getElementById(div).style.opacity = "100";
        document.getElementById(div).style.display = "block";
    } else {
        document.getElementById(div).style.opacity = "0";
        document.getElementById(div).style.display = "none";
    }
}
var $k = jQuery.noConflict();
$k(document).on("click", ".selectable", function () {
    let $cell = $k(this);
    if ($cell.find(".a_sel").length) {
        //console.log("Select found:", $cell.find(".a_sel"));
    } else {
        //console.error("Select field not found!");
    }
});
$k(document).on("click", function (e) {
    //console.log("Clicked element:", e.target);
});

function ucfirst(data) {
    data = String(data); // Konvertiere in einen String
    return data.charAt(0).toUpperCase() + data.slice(1);
}

// Event bei Änderung des Select-Wertes
function select_form() {
    var $ = jQuery.noConflict();
    $(document).ready(function () {
        // Klick auf ein .selectable-Element
        $(document).on("dblclick", ".selectable", function () {
            let $cell = $k(this); // Die Zelle
            let $span = $cell.find(".sele"); // Der verborgene Span
            let $select = $cell.find(".a_sel"); // Das Select-Feld

            // Verstecke den Span und zeige das Select
            $span.hide();
            $select.show().focus();
        });

        // Event bei Änderung des Select-Wertes
        $(document).on("change", ".a_sel", function () {
            let $select = $k(this); // Das aktuelle Select
            let newValue = $select.val(); // Der neue Wert
            let id = $select.closest(".selectable").find(".a_sel").data("id"); // Die ID des Eintrags
            let column = $select.data("column"); // Der Spaltenname
            let $span = $select.closest(".selectable").find(".sele"); // Der zugehörige Span
            let table = $k("#act_table").val();
            // AJAX-Anfrage zum Speichern des neuen Werts
            $.ajax({
                url: "/update-selectable/", // URL für das Update
                method: "POST",
                data: {
                    _token: $k('meta[name="csrf-token"]').attr("content"), // CSRF-Token
                    id: id,
                    table: table,
                    name: column,
                    column: column,
                    value: newValue,
                },
                success: function () {
                    // Erfolgreich gespeichert
                    $select.hide(); // Verstecke das Select
                    $span
                        .text(ucfirst($select.find("option:selected").text()))
                        .show() // Zeige den neuen Text
                        .data("value", ucfirst(newValue)) // Aktualisiere das data-value-Attribut
                        .show(); // Zeige den Span
                },
                error: function () {
                    // Fehlerbehandlung
                    alert("Fehler beim Speichern des Wertes");
                },
            });
        });

        // Wenn das Select-Feld den Fokus verliert, ohne dass eine Auswahl getroffen wurde
        $k(document).on("blur", ".a_sel", function () {
            let $select = $k(this); // Das aktuelle Select
            let $span = $select.closest(".selectable").find(".sele"); // Der zugehörige Span

            $select.hide(); // Verstecke das Select
            $span.show(); // Zeige den Span
        });
    });
}
function shorter(value) {
    const maxLineLength = 25; // Maximale Zeichen pro Zeile
    const maxLines = 4; // Maximale Anzahl an Zeilen
    const ellipsis = "…"; // Auslassungszeichen
    const lineBreak = "\n"; // HTML-Zeilenumbruch

    // Text in Wörter aufteilen
    const words = value.split(" ");
    const lines = [];
    let currentLine = "";

    // Erstelle Zeilen basierend auf Wortlängen
    for (const word of words) {
        if ((currentLine + word).length > maxLineLength) {
            lines.push(currentLine.trim());
            currentLine = "";
        }
        currentLine += word + " ";
    }

    // Letzte Zeile hinzufügen, falls sie existiert
    if (currentLine.trim()) {
        lines.push(currentLine.trim());
    }

    // Wenn mehr als `maxLines`, füge ein Auslassungszeichen hinzu
    if (lines.length > maxLines) {
        return lines.slice(0, maxLines).join(lineBreak) + ellipsis;
    }

    // Andernfalls, gib die gesamten Zeilen zurück
    return lines.join(lineBreak);
}

function text_form() {
    $k(document).ready(function () {
        // Doppelklick: Gekürzten Text ausblenden und Textarea einblenden
        $k(document).on("dblclick", ".editable", function () {
            let $element = $k(this);
            let $span = $element.find(".short-text");
            let $textarea = $element.find(".full-text");

            // Originaltext holen
            let originalText = $textarea.text();
            $textarea.val(originalText).show().focus();
            $span.hide();
        });

        // Keydown-Ereignis für das Textarea
        $k(document).on("keydown", ".full-text", function (e) {
            let $textarea = $k(this);
            let $element = $textarea.closest(".editable");
            let $span = $element.find(".short-text");
            let id = $element.data("id");
            let column = $element.data("column");

            // Shift + Enter: Zeilenumbruch
            if (e.key === "Enter" && e.shiftKey) {
                e.preventDefault(); // Verhindert das Absenden des Formulars
                let cursorPos = $textarea.prop("selectionStart");
                let text = $textarea.val();
                $textarea.val(
                    text.substring(0, cursorPos) +
                        "\n" +
                        text.substring(cursorPos)
                );
                $textarea[0].setSelectionRange(cursorPos + 1, cursorPos + 1); // Cursor an die richtige Stelle setzen
            }
            // Enter: Speichern in der DB
            else if (e.key === "Enter") {
                e.preventDefault();

                // Neuer Text
                let newText = $textarea.val();
                let table = $k("#act_table").val();
                // AJAX-Aufruf zum Speichern
                $k.ajax({
                    url: "/update-text", // Deine API-URL
                    method: "POST",
                    data: {
                        _token: $k('meta[name="csrf-token"]').attr("content"), // CSRF-Token
                        id: id,
                        column: column,
                        value: newText,
                        table: table,
                    },
                    success: function () {
                        // Erfolgreich gespeichert: Textarea ausblenden und aktualisierten Text anzeigen
                        $textarea.hide();
                        $textarea.text(newText);
                        $span.text(shorter(newText)).show();
                    },
                    error: function () {
                        // console.log(
                        //     "Fehler beim Speichern des Wertes." + newText
                        // );
                    },
                });
            }
        });

        // Fokusverlust: Textarea ausblenden, ohne zu speichern
        $k(document).on("blur", ".full-text", function () {
            let $textarea = $k(this);
            let $span = $textarea.closest(".editable").find(".short-text");

            $textarea.hide();
            $span.show();
        });
    });
}
text_form();

select_form();
init_date();
const take = {}; // Globales Objekt f�r Offset
const isLoading = {};
function scrollComments(postId2) {
    // let take = {};
    let postId = postId2;
    // take[postId] = 10; //Anzahl der Kommentare, die beim ersten Laden abgerufen werden
    let isLoading = false; // Flag, um mehrere gleichzeitige Ladevorg�nge zu verhindern
    let lastCommentId = null; // Zum Verhindern von Duplikaten

    // Funktion zum Laden von Kommentaren
    async function loadMoreComments(postId) {
        //console.log("Lade Kommentare f�r Post-ID:", postId); // Debugging

        if (isLoading) return; // Verhindert parallele Ladevorg�nge
        isLoading = true;
        document.getElementById("loading-" + postId).style.display = "block"; // Ladeanzeige sichtbar machen
        try {
            take[postId] = take[postId] ?? 0;
            // Anfrage an den Server, um Kommentare zu laden
            var response = await fetch(
                `/comments/load/${postId}?offset=${take[postId]}`
            );

            const data = await response.json();
            // console.log("postId:", postId);
            // console.log("Container gefunden:", container_new);
            // console.log("take[postId]:", take[postId]);
            // console.log(data);
            //�berpr�fen, ob es neue Kommentare gibt
            if (data.comments[postId] && data.comments[postId].length > 0) {
                var container = document.getElementById("com-btn-" + postId);

                //  console.log(data.comments[postId]);
                data.comments[postId].forEach((comment) => {
                    // Duplikate vermeiden: Nur neue Kommentare hinzuf�gen
                    // alert(comment.id);
                    if (
                        !document.getElementById(
                            `comment-${postId}-${comment.id}`
                        )
                    ) {
                        const commentBubble = document.createElement("div");
                        commentBubble.className = "comment-bubble-container";
                        commentBubble.id = `comment-${postId}-${comment.id}`; // Setze eine eindeutige ID f�r das Kommentar-Element
                        comment.image_path = comment.image_path ?? "008.jpg ";
                        commentBubble.innerHTML = `
                        <img src="/images/_ab/users/sm/${
                            comment.image_path
                        }" alt="${comment.name}" class="user-icon">
                        <div class="comment-bubble">
                            <span class='smtext'>${
                                comment.name || "Unbekannt"
                            }:</span><br />
                            ${comment.content}${DelLink(postId, comment.id)}
                        </div>
                    `;
                        container.before(commentBubble);
                    } else {
                    }
                });

                // Aktualisiere den Wert von `lastCommentId` mit der letzten geladenen Kommentar-ID
                lastCommentId =
                    data.comments[postId][data.comments[postId].length - 1].id;

                // Erh�he `take`, um beim n�chsten Abruf mehr Kommentare zu holen
                take[postId] += 10;
            }
        } catch (error) {
            console.error("Fehler beim Laden der Kommentare:", error);
        } finally {
            // Ladeanzeige ausblenden und den Ladezustand zur�cksetzen
            document.getElementById("loading-" + postId).style.display = "none";
            isLoading = false;
        }
    }
    var container_new = document.getElementById("comments-content-" + postId);

    container_new.addEventListener("scroll", function () {
        const scrollThreshold =
            container_new.scrollHeight -
            container_new.scrollTop -
            container_new.clientHeight;

        // �berpr�fen, ob der Benutzer fast am Ende des Kommentarbereichs angekommen ist
        if (scrollThreshold < 100) {
            // 100 Pixel vor dem Ende

            loadMoreComments(postId); // Kommentare nachladen
        }
    });

    // Initialer Kommentarabruf beim Laden der Seite
    document.addEventListener("DOMContentLoaded", () => {
        loadMoreComments(postId); // Lade die ersten Kommentare
    });
}
async function upd_xis(name, value, table, id) {
    newval = document.getElementById(name + "_" + id).checked;
    try {
        var response = await fetch(
            "/tables/xiupdate/" +
                table +
                "/" +
                name +
                "/" +
                id +
                "/" +
                Number(newval)
        );
    } catch (error) {
        console.error("Fehler beim speichern des Schalters:", error);
    }
}
function DelLink(postId, commentId) {
    const configElement = document.getElementById("is_delable");
    if (!configElement) {
        return " ";
    }
    const isConditionTrue = configElement.value == "true";
    const csrfToken = document.getElementById("csrf-token").value;
    if (!isConditionTrue) {
        return "";
    } else {
        const path = window.location.pathname; // Holt den Pfad der URL
        const segments = path.split("/").filter((segment) => segment); // Teilt den Pfad und entfernt leere Segmente
        if (segments.length > 0) {
            table = segments[0] || null;
            pic = segments[1] || "";
        }
        if (pic) {
            added = "" + table + "/" + pic;
        } else if (table) {
            added = "/" + table + "/";
        }

        return `
        <form action="/comments/delete/${postId}/${commentId}/${added}" method="POST" style="display:inline;">
            <input type="hidden" name="_token" value="${csrfToken}">
            <input type="hidden" name="_method" value="DELETE">
            <button type="submit" class="trans-btn" onclick="return confirm('M&ouml;chten Sie diesen Eintrag wirklich l&ouml;schen?');">
                <i class="fa fa-del-icon"></i>
            </button>
        </form>
    `;
    }
}
function initializeDatePicker() {
    var $k = jQuery.noConflict();
    $k(".datetimepicker").datetimepicker({
        dateFormat: "dd.mm.yy", // Deutsches Datumsformat: Tag.Monat.Jahr
        timeFormat: "HH:mm:ss", // Deutsches Zeitformat: Stunden:Minuten
        currentText: "Jetzt", // Button für aktuelle Uhrzeit
        closeText: "Schließen", // Schließen-Button
        timeOnlyTitle: "Zeit auswählen",
        timeText: "Zeit",
        hourText: "Stunde",
        minuteText: "Minute",
        secondText: "Sekunde",
        showButtonPanel: true,
        separator: " ",
    });
}
function initializeDatePicker() {
    // var $k = jQuery.noConflict();
    // $k(".datetimepicker").datetimepicker({
    //     dateFormat: "dd.mm.yy", // Deutsches Datumsformat: Tag.Monat.Jahr
    //     timeFormat: "HH:mm:ss", // Deutsches Zeitformat: Stunden:Minuten
    //     currentText: "Jetzt", // Button für aktuelle Uhrzeit
    //     closeText: "Schließen", // Schließen-Button
    //     timeOnlyTitle: "Zeit auswählen",
    //     timeText: "Zeit",
    //     hourText: "Stunde",
    //     inuteText: "Minute",
    //     secondText: "Sekunde",
    //     showButtonPanel: true,
    //     separator: " ",
    // });
}
function date_form() {}
function initDatePicker() {
    var $k = jQuery.noConflict();

    $k(document).ready(function () {
        // Initialize Datepicker
        // $k(".datetimepicker").datetimepicker({
        //     dayOfWeekStart: 1,
        //     format: "d.m.Y", // Format für Datum und Zeit
        //     timepicker: false, // Zeigt Zeit-Auswahl an
        //     onClose: function (selectedDateTime, $input) {
        //         saveDateTime($input); // Speichern beim Schließen
        //     },
        // });

        // Save DateTime on blur for safety
        $k(document).on("change", ".datetimepicker2", function () {
            saveDateTime($k(this));
        });
    });
}
function saveDateTime($input) {
    // Retrieve data attributes and value
    let value = $input.val();
    let id = $input.data("id");
    let table = $input.data("table");
    let column = $input.data("column");

    // Validate if the value is not empty
    if (!value) {
        alert("Das Datum darf nicht leer sein!");
        return;
    }
    // alert("asd23");

    // Send AJAX request
    $k.ajax({
        url: "/update-datetime", // Dein Endpunkt für das Speichern
        method: "POST",
        data: {
            _token: $k('meta[name="csrf-token"]').attr("content"), // CSRF-Token
            value: value,
            id: id,
            table: table,
            column: column,
        },
        success: function (response) {
            if (response.success) {
                // alert("Datum erfolgreich gespeichert!");
            } else {
                // alert("Speichern fehlgeschlagen: " + response.message);
            }
        },
        error: function (xhr) {
            console.error("Fehler beim Speichern:", xhr.responseJSON || xhr);
            alert("Ein Fehler ist aufgetreten!");
        },
    });
}
// Initialize Datepicker
initDatePicker();

function init_date(id = "") {
    var $ko = jQuery.noConflict();
    // $ko(document).ready(function () {
    // $.datepicker.setDefaults($.datepicker.regional["de"]);
    // $.datetimepicker.setLocale("pt-BR");
    if (!id) {
        $ko(document).on("click", ".datepicker2", function (event) {
            event.preventDefault();
            // $(this)
            //     .datetimepicker({
            //         timepicker: false,
            //         format: "d.m.Y",
            //         // "setDate": new Date(),
            //     })
            //     .focus();
        });
    } else {
        $ko(document).on("click", ".datepicker2", function (event) {
            event.preventDefault();
            // $(this)
            //     .datetimepicker({
            //         timepicker: false,
            //         format: "d.m.Y",
            //         // "setDate": new Date(),
            //     })
            //     .focus();
        });
    }
    // });
}

// // Erster Initialisierungsschritt
jQuery(document).ready(function () {
    init_date();
    date_form();
});

// // Starte das Skript
// date_form();

// Funktion aufrufen
function initi_date() {
    // $.datetimepicker.setLocale("de-DE");
    // $(".datetimepicker").datetimepicker({
    //     dayOfWeekStart: 1,
    //     format: "d.m.Y H:i:s",
    //     defaultTime: "14:27:13",
    // });
    $(function () {
        $.datepicker.setDefaults($.datepicker.regional["de"]);
        $(".datetimepicker").datetimepicker({
            dateFormat: "d.m.Y", // Deutsches Datumsformat
            dayOfWeekStart: 1,
            timepicker: false,
        });
    });
}
function createDeleteForm(postId, commentId) {
    // CSRF-Token aus dem HTML-Dokument holen
    const csrfToken = document.getElementById("csrf-token").dataset.token;
    const path = window.location.pathname; // Holt den Pfad der URL
    const segments = path.split("/").filter((segment) => segment); // Teilt den Pfad und entfernt leere Segmente
    table = segments[0] || "blog_posts";
    pic = segment[1] || "";
    // HTML f�r das Formular generieren
    return `
        <form action="/comments/delete/${postId}/${commentId}7${table}/${pic}" method="POST" style="display:inline;">
            <input type="hidden" name="_token" value="${csrfToken}">
            <input type="hidden" name="_method" value="DELETE">
            <button type="submit" class="trans-btn" onclick="return confirm('M�chten Sie diesen Eintrag wirklich l�schen?');">
                <i class="fa fa-del-icon"></i>
            </button>
        </form>
    `;
}
let isDialogOpen = {}; // Globale Variable
if (typeof window.isDialogOpen === "undefined") {
    window.isDialogOpen = {};
}
function upload_files(id) {
    var $k = jQuery.noConflict();
    let isDialogOpen = window.isDialogOpen || {};
    // Initialisieren, wenn noch nicht gesetzt
    if (typeof isDialogOpen[id] === "undefined") {
        isDialogOpen[id] = false;
    }
    // console.log("isDialogOpen:", isDialogOpen[id]);

    $k(document).ready(function () {
        let uploadArea = $k("#ul_label_" + id);
        let fileInput = $k("#fileInput_" + id);
        let previewArea = $k("#preview_" + id); // Vorschau-Bereich
        let progressBar = $k("#progress-bar_" + id); // Fortschrittsbalken
        let progressContainer = $k("#progress-container_" + id); // Fortschrittsbalken-Container

        // Entferne vorherige Handler und füge einen neuen hinzu
        uploadArea.off("click").on("click", function () {
            if (!isDialogOpen[id]) {
                isDialogOpen[id] = true; // Setze das Flag auf "offen"
                fileInput.click();
            }
        });

        // Entferne vorherige Datei-Input-Handler und füge neuen hinzu
        fileInput.off("change").on("change", function (e) {
            if (e.target.files.length > 0) {
                $k("#fileInput_2_" + id).val(e.target.files[0].name);
                // Zeige Vorschau der ausgewählten Datei
                showImagePreview(e.target.files[0]);

                // Starte den Upload
                uploadFile(e.target.files[0]);

                // Setze das Flag zurück, damit der Dialog erneut geöffnet werden kann
                isDialogOpen[id] = false;
            }
        });

        function showImagePreview(file) {
            let reader = new FileReader();
            reader.onload = function (e) {
                previewArea.empty(); // Leere den Vorschau-Bereich
                let img = $k("<img>")
                    .attr("src", e.target.result)
                    .css({ width: "100px", height: "auto" });
                previewArea.append(img); // Füge das Bild zur Vorschau hinzu
            };
            reader.readAsDataURL(file);
        }

        function uploadFile(file) {
            let formData = new FormData();
            formData.append("file", file);
            formData.append("id", id);
            formData.append(
                "_token",
                $k('meta[name="csrf-token"]').attr("content")
            );

            // Fortschrittsbalken sichtbar machen
            progressContainer.show();
            progressBar.css("width", "0%").text("0%");

            jQuery.ajax({
                url: "/imul/upload", // Passe den Upload-URL an
                type: "POST",
                data: formData,
                contentType: false,
                processData: false,
                xhr: function () {
                    let xhr = new XMLHttpRequest();
                    xhr.upload.addEventListener(
                        "progress",
                        function (e) {
                            if (e.lengthComputable) {
                                let percentComplete =
                                    (e.loaded / e.total) * 100;
                                progressBar.css("width", percentComplete + "%");
                                progressBar.text(
                                    Math.round(percentComplete) + "%"
                                );
                            }
                        },
                        false
                    );
                    return xhr;
                },
                success: function (response) {
                    progressBar.css("width", "100%");
                    progressBar.text("100%");
                },
                error: function () {
                    alert("Fehler beim Hochladen der Datei.");
                    progressContainer.hide();
                },
                complete: function () {
                    // Fortschrittsbalken nach dem Upload verstecken
                    setTimeout(function () {
                        progressContainer.hide();
                        progressBar.css("width", "0%");
                        progressBar.text("0%");
                    }, 2000);
                },
            });
        }
    });
}
function age_form(id, n, v, t) {
    // Wähle das Alter-Feld aus
    var alterField = document.getElementById("age_" + id + "");
    var pfield = document.getElementById("pfield_" + id + "");
    var wrapper = document.getElementById("w_" + id);
    // Doppelklick-Eventlistener hinzufügen
    pfield.addEventListener("dblclick", function () {
        // Der aktuelle Wert des Felds
        var currentValue_alt = alterField.value;
        // Erstelle ein neues Input-Element vom Typ 'date'
        var dateInput = alterField;
        // dateInput.type = "date";
        wrapper.style.display = "inline-block";
        pfield.style.display = "none";
        dateInput.value = currentValue_alt || ""; // Falls ein Wert vorhanden ist
        //dateInput.classList.add("date-input"); // Optional: CSS-Klasse für Styling

        // Ersetze den Textinhalt durch das Input-Element
        alterField.textContent = "";
        alterField.style.display = "inline-block";
        //  alterField.appendChild(dateInput);

        // Fokus auf das neue Input-Element setzen
        dateInput.focus();

        // Eventlistener für Änderungen im Input hinzufügen
        dateInput.addEventListener("change", function () {
            // AJAX-Anfrage senden
        });
        // Verlasse das Feld (Blur) nach dem Speichern
        dateInput.addEventListener("blur", function () {
            const newValue = dateInput.value;

            // AJAX-Anfrage senden
            fetch("/update-age", {
                method: "POST",
                headers: {
                    "Content-Type": "application/json",
                    "X-CSRF-TOKEN": document
                        .querySelector('meta[name="csrf-token"]')
                        .getAttribute("content"),
                },
                body: JSON.stringify({
                    value: newValue,
                    id: id, // ID des Datensatzes
                    table: t, // Tabellenname
                    column: n, // Spaltenname
                }),
            })
                .then((response) => {
                    if (!response.ok) {
                        throw new Error("Fehler beim Aktualisieren des Alters");
                    }
                    return response.json();
                })
                .then((data) => {
                    // Erfolgreich aktualisiert
                    //console.log("Alter erfolgreich aktualisiert:", data);

                    // Vorschau-Box mit dem berechneten Alter aktualisieren
                    const previewBox = pfield;
                    const currentValue = data.age || "N/AA"; // Sicherstellen, dass ein Wert vorhanden ist

                    if (previewBox) {
                        previewBox.textContent = currentValue; // Berechnetes Alter verwenden
                    }

                    // Eingabefeld ausblenden und Vorschau anzeigen
                    dateInput.style.display = "none";
                    wrapper.style.display = "none";
                    alterField.style.display = "inline-block";
                    pfield.style.display = "inline-block";
                })
                .catch((error) => {
                    console.error("Fehler:", error);
                    alert("Das Alter konnte nicht aktualisiert werden.");
                });
        });
    });
}
if (!("showPicker" in document.createElement("input"))) {
    document.getElementById("time-fallback").style.display = "inline-block";
}

function dpicker_hide(id, val = "") {
    const $k = jQuery.noConflict();
    const table = $k("#act_table").val();
    const $graphicOverlay = $k("#graphicOverlay" + id);
    const $datetimepicker2 = $k("#datetimepicker2_" + id);
    const $datetimepicker1 = $k("#datetimepicker1_" + id);
    const $unlimitedBtn = $k("#unlimitedBtn" + id);
    if (!document.getElementById("datetimepicker2_" + id)) {
        return;
    }
    // Sicherstellen, dass Events nur einmal gebunden werden
    // $graphicOverlay.off("click").on("click", function (e) {
    //     e.preventDefault();
    //     const datetimeInput = document.getElementById("datetimepicker2_" + id);
    //     if (datetimeInput && datetimeInput.showPicker) {
    //         datetimeInput.showPicker();
    //     } else {
    //         datetimeInput.focus(); // Fallback
    //     }
    // });
    $datetimepicker2.on("input", function () {
        // Hier wird der eingegebene Wert des DateTimePickers erfasst
        let selectedDate = $(this).val();
        // console.log("te" + selectedDate);
    });
    const datetimeField = document.getElementById("datetimepicker2_" + id);

    datetimeField.addEventListener("click", function () {
        $graphicOverlay.hide();
    });
    $datetimepicker2.off("input change").on("input change", function () {
        const selectedValue = $k(this).val();
        if (selectedValue) {
            $graphicOverlay.hide();
            $unlimitedBtn.show();
        } else {
            $graphicOverlay.show();
            $unlimitedBtn.hide();
        }
    });

    $unlimitedBtn.off("click").on("click", function () {
        $datetimepicker2.val("");
        senddata(table, "date_end", id, "");
        $graphicOverlay.show();
        $unlimitedBtn.hide();
    });
    $datetimepicker1.off("blur").on("blur", function () {
        senddata(table, "date_begin", id, $k(this).val());
    });
    $datetimepicker2.off("blur").on("blur", function () {
        if (!$k(this).val()) {
            $graphicOverlay.hide();
            $unlimitedBtn.show();
        } else {
            senddata(table, "date_end", id, $k(this).val());
            $graphicOverlay.hide();
            $unlimitedBtn.show();
        }
    });

    // Initialer Zustand
    if (!$datetimepicker2.val()) {
        $graphicOverlay.show();
        $unlimitedBtn.hide();
    } else {
        $graphicOverlay.hide();
        $unlimitedBtn.show();
    }
}
function senddata(table, column, id, value) {
    // URL für die API oder den Endpunkt, an den die Daten gesendet werden sollen
    const url = "/update-datetime";
    // Erstelle ein Objekt mit den zu sendenden Daten
    const data = {
        table: table,
        column: column,
        id: id,
        value: value,
    };

    // Sende die Daten per Fetch-API
    fetch(url, {
        method: "POST", // HTTP-Methode
        headers: {
            "Content-Type": "application/json", // Datenformat
            "X-CSRF-TOKEN": document
                .querySelector('meta[name="csrf-token"]')
                .getAttribute("content"), // CSRF-Token für Laravel
        },
        body: JSON.stringify(data), // Konvertiere das Objekt in JSON
    })
        .then((response) => {
            if (response.ok) {
                return response.json(); // Verarbeite die Antwort als JSON
            } else {
                throw new Error(`Server error: ${response.status}`);
            }
        })
        .then((responseData) => {
            // Verarbeite die Antwortdaten
            // console.log("Erfolgreich gesendet:", responseData);
            // alert("Daten erfolgreich aktualisiert!");
        })
        .catch((error) => {
            // Behandle Fehler
            console.error("Fehler beim Senden der Daten:", error);
            alert("Fehler beim Senden der Daten.");
        });
}

function updateViews(id) {
    const $datetimeInput = $("#datetimes_" + id + "");
    const $infinityText = $("#infinityTexts_" + id + "");
    const $toggleButton = $("#toggleDatetimes_" + id + "");
    const $ini_btn = $("#ini_btn_" + id + "_");
    const value = $datetimeInput.val();
    if (!value) {
        $infinityText.show();
        $ini_btn.show();
    } else {
        $infinityText.hide();
    }
    //alert(value);
}
function inf(id) {
    // Initial überprüfen
    const $datetimeInput = $("#datetimes_" + id + "");
    const $infinityText = $("#infinityTexts_" + id + "");
    const $toggleButton = $("#toggleDatetimes_" + id + "");
    const $ini_btn = $("#ini_btn_" + id + "_inf2");
    updateViews(id);

    // Event: Beim Klick auf das Icon den Datepicker öffnen
    $toggleButton.on("click", function (e) {
        e.preventDefault();
        $datetimeInput.show().focus(); // Zeige den Datetime-Picker und öffne ihn
        $infinityText.hide();
    });

    // Event: Wenn sich der Wert im Datetime-Picker ändert
    $datetimeInput.on("change", function () {
        const value = $datetimeInput.val();
        $infinityText.hide();
        if (value) {
            //$datetimeInput.hide();
            $infinityText.hide();
            $ini_btn.show();
        } else {
            $infinityText.hide();
            $ini_btn.show();
        }
        if (value) {
            $infinityText.hide();
        }

        updateViews(id);
    });

    // Wenn der Wert direkt gesetzt wird
    $datetimeInput.on("blur", function () {
        updateViews(id);
        $infinityText.hide();
        $ini_btn.show();
    });
}
function BTNRESET(id) {
    // Definiere den HTML-Code des Buttons
    const inf2 = "inf2"; // Beispielwert
    const disp = "block"; // Sichtbarkeit (z. B. "block" oder "none")
    const $datetimeInput = $("#datetimepicker_" + id + inf2 + "");
    const $infinityText = $("#infinityTexts_" + id + "");
    const $toggleButton = $("#toggleDatetimes_" + id + "");
    const $ini_btn = $("#ini_btn_" + id + "_inf2");
    const added = `<button style="display:block;" class="btn btn-default infinit" id="ini_btn_${id}_${inf2}">Unendlich ${id}</button>`;
    $licon = $("#licon_" + id + "_" + inf2);
    // Füge den Button an ein Ziel-Element an
    // $("#datetimepicker_" + id).append(added);

    // Optional: Event an den neuen Button anhängen
    $(document).on("click", "#ini_btn_" + id + "_" + inf2 + "", function () {
        $infinityText.show();
        // $datetimeInput.val("tt.mm.jjjj --:--");
        $datetimeInput.val("");
        $infinityText.css("top", "5px");
        $licon.css("top", "18px");
    });
}
function upload_filez(id, action, ad) {
    var $k = jQuery.noConflict();
    if (!ad) {
        ad = "_" + id;
    }
    // Öffnet das Modal bei Klick auf die Vorschau
    // $k("#preview-thumb_" + id).on("click", function () {
    //     $k("#uploadModal_" + id).show(); // Bootstrap Modal anzeigen
    //     alert("opened_" + id);
    // });
    // $k(document).on("click", "#filePreview_" + id, function () {
    //     $k("#uploadModal_" + id).show(); // Bootstrap Modal anzeigen
    //     alert("opened_" + id);
    // });
    $k(document).ready(function () {
        // Doppelklick: Gekürzten Text ausblenden und Textarea einblenden
        $k(document).on("click", "#preview-thumb_" + id, function () {
            $k("#uploadModal_" + id).show();
        });
    });

    // Dropzone Drag-and-Drop Verhalten
    const dropzone = document.getElementById("dropzone_" + id);

    dropzone.addEventListener("dragover", function (e) {
        e.preventDefault();
        dropzone.classList.add("border-primary");
    });

    dropzone.addEventListener("dragleave", function () {
        dropzone.classList.remove("border-primary");
    });

    dropzone.addEventListener("drop", function (e) {
        e.preventDefault();
        dropzone.classList.remove("border-primary");

        const files = e.dataTransfer.files;
        const fileInput = document.getElementById("fileInput_" + id);
        fileInput.files = files;
        displayFileThumbnail(files[0]);
    });

    // Klick auf "browse", um den Datei-Explorer zu öffnen
    $k("#fileLink_" + id).on("click", function (e) {
        e.preventDefault();
        $k("#fileInput_" + id).click();
    });

    // Dateien im Explorer auswählen
    $k("#fileInput_" + id).on("input", function () {
        const files = this.files; // Direkt auf das DOM-Element zugreifen
        if (files.length > 0) {
            displayFileThumbnail(files[0]);
        }
    });
    $k("#fileInput_" + id).on("change", function () {
        const files = this.files; // Direkt auf das DOM-Element zugreifen
        if (files.length > 0) {
            displayFileThumbnail(files[0]);
        }
    });
    $k(document).on("input", "[id^='fileInput_']", function () {
        var id = $(this).attr("id").replace("fileInput_", ""); // Extrahiere die ID aus dem Attribut
        const files = this.files; // Greife auf die ausgewählten Dateien zu
        action = action + "/" + id;

        if (files.length > 0) {
            displayFileThumbnail(files[0], id); // Übergib die Datei und die ID
        }
    });
    // Funktion zum Anzeigen des Thumbnails
    function displayFileThumbnail(file, id) {
        const reader = new FileReader();

        if (file && file.type.startsWith("image/")) {
            reader.onload = function (e) {
                const imageUrl = e.target.result;
                const thumbnail = $("#uploadedThumb_" + id); // Nutze die ID, um das richtige Thumbnail zu finden
                thumbnail.attr("src", imageUrl); // Setze die Bildquelle
                thumbnail.show(); // Zeige das Thumbnail an
            };
            reader.readAsDataURL(file);
        } else {
            const thumbnail = $("#uploadedThumb_" + id);
            thumbnail.hide(); // Verstecke das Thumbnail bei ungültiger Datei
        }
    }
    // Submit-Funktion
    $k(`#submitButton_${id}`).on("click", function (e) {
        e.preventDefault();
        // console.log("uploadForm" + ad);
        const form = document.getElementById("uploadForm" + ad);
        const formData = new FormData(form);

        // Prüfen, ob eine Datei ausgewählt wurde
        const fileInput = document.getElementById("fileInput_" + id);
        if (fileInput.files.length > 0) {
            formData.append("image_path", fileInput.files[0]); // Datei hinzufügen
            formData.append("id", id); // Datei hinzufügen
        } else {
            alert("Bitte wählen Sie eine Datei aus.");
            return;
        }

        //console.log(fileInput.files[0]); // Prüfen, ob die Datei korrekt ausgewählt wurde

        // Senden der Daten per Fetch API
    });
    window.submitForm = function (id) {
        // console.log(action);
        var form = document.getElementById("uploadForm" + ad);
        var formData = new FormData(form);
        var fileInput = document.getElementById("fileInput_" + id);
        formData.append("image_path", fileInput.files[0]); // Datei hinzufügen
        formData.append("id", id); // Datei hinzufügen
        for (let pair of formData.entries()) {
            // console.log(pair[0], pair[1]);
        }
        // Create a new instance of XMLHttpRequest
        var xhr = new XMLHttpRequest();
        document.getElementById("progressContainer_" + id).style.display =
            "block";

        // Set up the request
        xhr.open("POST", "localhost:8000/" + action, true);
        xhr.upload.onprogress = function (event) {
            if (event.lengthComputable) {
                var percentComplete = (event.loaded / event.total) * 100;
                document.getElementById("progressBar_" + id).value =
                    percentComplete;
                document.getElementById("progressText_" + id).textContent =
                    Math.round(percentComplete) + "%";
            }
        };
        var csrfToken = document
            .querySelector('meta[name="csrf-token"]')
            .getAttribute("content");
        xhr.setRequestHeader("X-CSRF-TOKEN", csrfToken);
        // Add event listeners for various states of the request
        xhr.onload = function () {
            if (xhr.status >= 200 && xhr.status < 500) {
                //alert("Datei erfolgreich hochgeladen! 200");
                $k("#uploadModal_" + id).hide(); // Schließt das Modal
            } else {
                alert("Fehler beim Hochladen der Datei.");
                console.error(
                    "Server returned an error: " +
                        xhr.status +
                        " " +
                        xhr.statusText
                );
            }
        };

        // Handle network errors or other issues
        xhr.onerror = function () {
            console.error("Upload-Fehler:", xhr.statusText);
            alert("Fehler beim Hochladen.");
        };

        // Set up additional error handling for timeout
        xhr.ontimeout = function () {
            console.error("Request timed out.");
            //alert("Die Anfrage ist abgelaufen.");
        };

        // Set timeout (optional)
        xhr.timeout = 5000; // Set the timeout to 5 seconds (for example)

        // Create a FormData object and append the file
        var formData = new FormData(form);
        formData.append("image_path", fileInput.files[0]); // assuming `fileInput` is your file input element
        // Send the request with the form data
        xhr.send(formData);
        setTimeout(function () {
            $("#uploadForm" + ad).action +=
                "?success=" +
                encodeURIComponent(
                    "Profileinstellungen wurden erfolgreich aktualisiert. 200"
                );
            $("#uploadForm" + ad).submit();

            //
        }, 5001);
    };
}
async function startRegistration() {
    try {
        // Anfrage an den Server, um WebAuthn-Registrierungsoptionen zu erhalten
        const response = await fetch("/webauthn/start-registration");
        const options = await response.json();

        // Konvertiere challenge in einen ArrayBuffer
        options.challenge = Uint8Array.from(atob(options.challenge), (c) =>
            c.charCodeAt(0)
        ).buffer;

        // Konvertiere user.id in einen ArrayBuffer
        options.user.id = new TextEncoder().encode(options.user.id);

        // WebAuthn-Anmeldeinformationen erstellen
        const credential = await navigator.credentials.create({
            publicKey: options,
        });

        // Registrierung abschließen
        const registrationResponse = await fetch(
            "/webauthn/complete-registration",
            {
                method: "POST",
                body: JSON.stringify({
                    credential: credential,
                }),
                headers: {
                    "Content-Type": "application/json",
                },
            }
        );

        const result = await registrationResponse.json();
        //alert(result.message);
    } catch (error) {
        console.error("Fehler bei der Registrierung:", error);
        alert("Fehler bei der Registrierung: " + error.message);
    }
}

async function startAuthentication() {
    const response = await fetch("/webauthn/start-authentication");
    const options = await response.json();

    const credential = await navigator.credentials.get({
        publicKey: options,
    });

    // Complete Authentication
    const authResponse = await fetch("/webauthn/complete-authentication", {
        method: "POST",
        body: JSON.stringify({
            credential: credential,
        }),
        headers: {
            "Content-Type": "application/json",
        },
    });

    const result = await authResponse.json();
    //alert(result.message);
}
