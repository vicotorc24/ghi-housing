import fs from 'fs';
import path from 'path';
import { fileURLToPath } from 'url';
import sharp from 'sharp';

const __filename = fileURLToPath(import.meta.url);
const __dirname = path.dirname(__filename);
const projectRoot = path.join(__dirname, '..');

const imgDir = path.join(projectRoot, 'public', 'assets', 'img');
const srcDir = path.join(projectRoot, 'src');

async function getFiles(dir) {
  const dirents = await fs.promises.readdir(dir, { withFileTypes: true });
  const files = await Promise.all(dirents.map((dirent) => {
    const res = path.resolve(dir, dirent.name);
    return dirent.isDirectory() ? getFiles(res) : res;
  }));
  return Array.prototype.concat(...files);
}

async function optimizeImages() {
  console.log('Scanning for images...');
  const files = await getFiles(imgDir);
  
  const toOptimize = files.filter(f => f.match(/\.(png|jpe?g)$/i));
  
  console.log(`Found ${toOptimize.length} images to optimize.`);
  
  for (const file of toOptimize) {
    const ext = path.extname(file);
    const newFile = file.replace(new RegExp(`${ext}$`, 'i'), '.webp');
    
    // Convert to webp
    await sharp(file).webp({ quality: 80 }).toFile(newFile);
    console.log(`Converted: ${path.basename(file)} -> ${path.basename(newFile)}`);
    
    // Delete original to save space (commented out due to Windows lock)
    // await fs.promises.unlink(file);
  }
}

async function updateReferences() {
  console.log('Scanning for .astro and .json files to update references...');
  const files = await getFiles(srcDir);
  const toUpdate = files.filter(f => f.match(/\.(astro|json|html)$/i));
  
  let totalReplaced = 0;
  for (const file of toUpdate) {
    let content = await fs.promises.readFile(file, 'utf-8');
    const originalContent = content;
    
    // Replace .png, .jpg, .jpeg with .webp in strings like src="/assets/img/..."
    content = content.replace(/(\/assets\/img\/.*?\.)(png|jpe?g)/gi, '$1webp');
    
    if (content !== originalContent) {
      await fs.promises.writeFile(file, content, 'utf-8');
      console.log(`Updated references in: ${path.basename(file)}`);
      totalReplaced++;
    }
  }
  console.log(`Total files updated: ${totalReplaced}`);
}

async function run() {
  try {
    await optimizeImages();
    await updateReferences();
    console.log('Done!');
  } catch (err) {
    console.error('Error:', err);
  }
}

run();
