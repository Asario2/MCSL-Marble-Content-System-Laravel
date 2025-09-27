// // Erkennen, ob Dark Mode aktiviert ist

// const isDarkMode = window.matchMedia && window.matchMedia('(prefers-color-scheme: dark)').matches;

// // Senden Sie die Information an den Server
// fetch('/set-dark-mode', {
// method: 'POST',
// headers: {
// 'Content-Type': 'application/json',
// 'X-CSRF-TOKEN': document.getElementById('token').value,
// },
// body: JSON.stringify({ darkMode: isDarkMode })
// });
// alert(isDarkMode);
var $_GET = {};
if(document.location?.toString().indexOf('?') !== -1) {
    var query = document.location
                   ?.toString()
                   // get the query string
                   .replace(/^.*?\?/, '')
                   // and remove any existing hash string (thanks, @vrijdenker)
                   .replace(/#.*$/, '')
                   .split('&');

    for(var i=0, l=query.length; i<l; i++) {
       var aux = decodeURIComponent(query[i]).split('=');
       $_GET[aux[0]] = aux[1];
    }
}
async function loadDarkMode() {
    // Lade den gespeicherten Wert aus localStorage (Standard: 'light', falls nicht vorhanden)
    let mode = localStorage.getItem("theme") || "light";


    // Überprüfe, ob ein globales 'darkMode' gesetzt wurde
    if (window.darkMode) {
        mode = window.darkMode;
    } else {
        try {
            const response = await fetch("/api/dark-mode");
            const data = await response.json();
            mode = data.darkMode || "light"; // Verwende die vom Server gelieferten Daten, wenn vorhanden
        } catch (error) {
            console.error("Fehler beim Laden des Dark Modes:", error);
        }
    }

    // Setze den Wert im localStorage nur, wenn er sich geändert hat
    if (localStorage.getItem("theme") !== mode) {

        //console.log("Ändere Theme von", localStorage.getItem("theme"), "auf", mode);
        localStorage.setItem("theme", mode);
    }

    // Setze das `data-theme` Attribut auf <html>, um das Theme zu wechseln
    document.documentElement.setAttribute("data-theme", mode);

    // Optional: Alle AI-Buttons oder spezifische Elemente aktualisieren
    document.querySelectorAll(".ai-button").forEach((aibut) => {
        aibut.src = `/images/icons/ai-${mode}.png`;
    });


    // Alle AI-Buttons aktualisieren
    document.querySelectorAll(".ai-button").forEach((aibut) => {
        aibut.src = `/images/icons/ai-${mode}.png`;
    });
}

// 🎯 Dark Mode direkt beim Laden der Seite setzen
document.addEventListener("DOMContentLoaded", loadDarkMode);

function checkImageExists(url, callback) {
    const img = new Image();
    img.onload = () => callback(true);   // Bild erfolgreich geladen
    img.onerror = () => callback(false); // Bild konnte nicht geladen werden
    img.src = url;
  }
  function decodeHtml(html) {
    if(!html){
        return "";
    }
    return html.replace(/%5B/g, '[').replace(/%5D/g, ']');

}
function ucf(str) {
    // Teilt den String an den Unterstrichen
    const arr = str.split("_");

    // Wandelt jedes Element des Arrays um, falls es mehr als ein Wort gibt
    const na = arr.map(
        (val) => val.charAt(0).toUpperCase() + val.slice(1).toLowerCase(),
    );

    // Setzt die W�rter mit einem Leerzeichen zusammen
    return na.join(" ");
}




