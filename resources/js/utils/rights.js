// utils/rights.js
let userRights = {};
let adminTablePositions = {};
let rightsReady = false;
let isAuthenticated = false;

import { CleanTable, GetAuth } from '@/helpers';

/**
 * Einmaliger Ladevorgang aller Rechte und Tabellenpositionen
 */
let rightsPromise = null;

export async function loadAllRights() {
  if (rightsPromise) return rightsPromise;

  rightsPromise = (async () => {
    const [rightsRes, tablesRes] = await Promise.all([
      axios.get('/api/user/rights'),
      axios.get('/api/admin_table_positions'),
    ]);

    userRights = rightsRes.data[1];
    adminTablePositions = tablesRes.data[1].reduce((acc, item) => {
      acc[item.name] = item.position;
      return acc;
    }, {});

    isAuthenticated = await GetAuth();
    rightsReady = true;
    return true;
  })();

  return rightsPromise;
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
export function hasRightSync(right, table) {
  if (!isAuthenticated) {
    // console.warn("⚠️ User ist nicht authentifiziert");
    return false;
  }

  if (!rightsReady) {
    // console.warn("⚠️ Rechte sind noch nicht bereit");
    return false;
  }

  table = table ?? CleanTable();
  const rightKey = `${right}_table`;
  const rightsString = userRights?.[rightKey];
  const position = adminTablePositions[table];

  if (typeof rightsString !== 'string') {
    // console.error(`❌ Rechte-String für '${rightKey}' nicht gefunden`);
    return false;
  }

  if (typeof position !== 'number' && typeof position !== "string") {
    // console.error(`❌ Position für Tabelle '${table}' nicht gefunden`);
    return false;
  }

  const result = rightsString.charAt(position) === '1';
//   console.log(`🔍 ${rightKey} @${table} = ${result}`);
  return result;
}


export function isRightsReady() {
  return rightsReady;
}

