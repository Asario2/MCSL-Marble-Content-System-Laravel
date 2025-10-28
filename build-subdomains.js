// console.log('?? build-subdomains.js gestartet');

// build-subdomains.js (ESM-kompatibel)
import { exec } from 'child_process';
import { mkdirSync, existsSync } from 'fs';
import { fileURLToPath } from 'url';
import { dirname, resolve } from 'path';

const __filename = fileURLToPath(import.meta.url);
const __dirname = dirname(__filename);

const builds = [
  { name: 'ab', config: 'tailwind.ab.config.js', out: 'public/css/tailw/ab.css' },
  { name: 'mfx', config: 'tailwind.mfx.config.js', out: 'public/css/tailw/mfx.css' },
  { name: 'default', config: 'tailwind.default.config.js', out: 'public/css/tailw/default.css' },
];

if (!existsSync('public/css/tailw')) {
  mkdirSync('public/css/tailw', { recursive: true });
}

builds.forEach(({ config, out }) => {
  const cmd = `npx tailwindcss -c ${config} -o ${out}`;
//   console.log(`???  Building: ${out}`);
  exec(cmd, (err, stdout, stderr) => {
    if (err) {
      console.error(`? Fehler bei ${out}:\n`, stderr);
    } else {
//       console.log(`? Erfolgreich erstellt: ${out}`);
    }
  });
});
