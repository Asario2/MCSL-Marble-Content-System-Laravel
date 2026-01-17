import fetch from 'node-fetch';
import fs from 'fs/promises';
import path from 'path';

const subdomains = ['ab', 'mfx', 'default'];

async function fetchColors(subdomain) {
  const url = `http://localhost/api/tailwind-colors/${subdomain}`;
  const res = await fetch(url);
  if (!res.ok) {
    console.error(`Fehler beim Laden der Farben für ${subdomain}`);
    return {};
  }
  return await res.json();
}

async function main() {
  for (const sub of subdomains) {
    const colors = await fetchColors(sub);
    const filePath = path.resolve(`./tailwind_colors_${sub}.js`);

    // Datei mit export schreiben
    const content = `export default ${JSON.stringify(colors, null, 2)};`;
    await fs.writeFile(filePath, content);
//     console.log(`? Farben für ${sub} gespeichert in ${filePath}`);
  }
}

main();
