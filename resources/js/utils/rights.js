
// utils/rights.js
let userRights = {};
let adminTablePositions = {};
let rightsReady = false;
let isAuthenticated = false;

import { CleanTable,GetBatchRights } from '@/helpers';

/**
 * Einmaliger Ladevorgang aller Rechte und Tabellenpositionen
 */


import axios from "axios";

let rightsStore = {};     // Cache für alle Rechte
let rightsLoaded = false; // Status ob schon geladen

export async function loadAllRights() {
    if (rightsLoaded) return;

    try {
        //const response = await axios.get("/admin/rights/all");
        // rightsStore = response.data;        // z. B. { "view_users":1, "edit_users":1 }
        rightsLoaded = true;
        console.log("ALLE Rechte geladen:", rightsStore);
    } catch (e) {
        console.error("Fehler beim Laden der Rechte:", e);
    }
}

export function hasRightSync(right, table) {
    const key = `${right}_${table}`;

    // if (!rightsLoaded) {
    //     console.warn("hasRightSync aufgerufen BEVOR Rechte geladen!");
    //     return false;
    // }
// console.log(GetBatchRights(table, right));
    return GetBatchRights(table, right);
}

export function isRightsReady() {
    return rightsLoaded;
}

/**
 * Rechteprüfung für Templates (synchron, z. B. für v-if)
 */
export function hasRight(right, table) {
  if (!rightsReady || !isAuthenticated) {
    // console.warn(`⚠️ Rechteprüfung fehlgeschlagen – ready=${rightsReady}, auth=${isAuthenticated}`);
    return false;
  }

  table = table ?? CleanTable();
  const rightKey = `${right}_table`;
  const rightsString = userRights?.[rightKey];
  const position = adminTablePositions[table];

  if (typeof rightsString !== 'string') {
   // console.error(`❌ Rechte-String für '${rightKey}' fehlt`);
    return false;
  }

  if (typeof position !== 'number' && typeof position !== 'string') {
    // console.error(`❌ Position für Tabelle '${table}' fehlt`);
    return false;
  }

  const result = rightsString.charAt(position) === '1';
//   console.log(`🔍 hasRight(${right}, ${table}) = ${result}`);
  return result;
}
