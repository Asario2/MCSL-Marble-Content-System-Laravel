const { exec } = require('child_process');
const fs = require('fs');
const path = require('path');

// Zielverzeichnis sicherstellen: public/css/tailw/
const outputDir = path.resolve(__dirname, 'public/css/tailw');
if (!fs.existsSync(outputDir)) {
  fs.mkdirSync(outputDir, { recursive: true });
}

// Konfigurationen für Subdomains
const configs = [
  { name: 'ab', config: 'tailwind.ab.config.js' },
  { name: 'mfx', config: 'tailwind.mfx.config.js' },
  { name: 'default', config: 'tailwind.default.config.js' },
];

// Für jede Subdomain Tailwind-Build starten
configs.forEach(({ name, config }) => {
  const outFile = `public/css/tailw/${name}.css`;
  const cmd = `npx tailwindcss -c ${config} -o ${outFile} --minify`;

//   console.log(`?? Baue CSS für "${name}" ? ${outFile}`);
  exec(cmd, (error, stdout, stderr) => {
    if (error) {
      console.error(`? Fehler bei "${name}":`, stderr);
    } else {
//       console.log(`? Fertig: ${outFile}`);
    }
  });
});
