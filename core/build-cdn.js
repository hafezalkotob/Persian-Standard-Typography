const fs = require('fs');
const path = require('path');

// ======================== تنظیمات ========================
const CDN_BASE_URL = 'https://cdn.cdoc.ir/pst';      // CDN root address (without / at the end)
const ROOT_DIR = __dirname;                          // PST project root folder
const DIST_DIR = path.join(ROOT_DIR, 'dist', 'cdn'); // Output in dist/cdn
// ==========================================================

function copyFile(src, dest, transform = null) {
    const destDir = path.dirname(dest);
    fs.mkdirSync(destDir, { recursive: true });
    if (transform) {
        const content = fs.readFileSync(src, 'utf-8');
        fs.writeFileSync(dest, transform(content), 'utf-8');
    } else {
        fs.copyFileSync(src, dest);
    }
    console.log(`✅ ${path.relative(ROOT_DIR, src)} → ${path.relative(ROOT_DIR, dest)}`);
}

function copyDirectoryRecursive(src, dest, fileCallback) {
    if (!fs.existsSync(dest)) fs.mkdirSync(dest, { recursive: true });
    const entries = fs.readdirSync(src, { withFileTypes: true });
    entries.forEach(entry => {
        const srcPath = path.join(src, entry.name);
        const destPath = path.join(dest, entry.name);
        if (entry.isDirectory()) {
            copyDirectoryRecursive(srcPath, destPath, fileCallback);
        } else {
            if (fileCallback) {
                fileCallback(srcPath, destPath);
            } else {
                fs.mkdirSync(path.dirname(destPath), { recursive: true });
                fs.copyFileSync(srcPath, destPath);
            }
            console.log(`✅ ${path.relative(ROOT_DIR, srcPath)} → ${path.relative(ROOT_DIR, destPath)}`);
        }
    });
}

function processCSS() {
    const cssSrcDir = path.join(ROOT_DIR, 'css');
    const cssDestDir = path.join(DIST_DIR, 'css');
    const cssFiles = [
        'base/reset.css', 'base/ui-base.css', 'base/tokens.css',
        'base/typography.css', 'components/table.css', 'components/forms.css'
    ];
    cssFiles.forEach(file => {
        const src = path.join(cssSrcDir, file);
        const dest = path.join(cssDestDir, file);
        if (fs.existsSync(src)) copyFile(src, dest);
        else console.warn(`⚠️ ${file} Not found`);
    });

    const mainSrc = path.join(cssSrcDir, 'main.css');
    const mainDest = path.join(cssDestDir, 'main.css');
    if (fs.existsSync(mainSrc)) {
        copyFile(mainSrc, mainDest, content =>
            content.split('\n')
                .filter(line => !(line.includes('@import url') && line.includes('../fonts/')))
                .join('\n')
        );
    }
}

function processFonts() {
    const fontsSrcDir = path.join(ROOT_DIR, 'fonts');
    const fontsDestDir = path.join(DIST_DIR, 'fonts');
    if (!fs.existsSync(fontsSrcDir)) return;
    const fontFamilies = fs.readdirSync(fontsSrcDir).filter(item =>
        fs.statSync(path.join(fontsSrcDir, item)).isDirectory()
    );
    fontFamilies.forEach(family => {
        const familySrc = path.join(fontsSrcDir, family);
        const familyDest = path.join(fontsDestDir, family);
        copyDirectoryRecursive(familySrc, familyDest, (srcPath, destPath) => {
            if (path.basename(srcPath) === 'font-face.css') {
                const content = fs.readFileSync(srcPath, 'utf-8');
                const transformed = content.replace(
                    /url\(['"]?(woff2?|variable)\/([^'"')]+)['"]?\)/g,
                    (match, folder, fileName) => `url('${CDN_BASE_URL}/fonts/${family}/${folder}/${fileName}')`
                );
                fs.mkdirSync(path.dirname(destPath), { recursive: true });
                fs.writeFileSync(destPath, transformed, 'utf-8');
            } else {
                fs.mkdirSync(path.dirname(destPath), { recursive: true });
                fs.copyFileSync(srcPath, destPath);
            }
        });
    });
}

function processJS() {
    const jsSrcDir = path.join(ROOT_DIR, 'js');
    const jsDestDir = path.join(DIST_DIR, 'js');
    if (fs.existsSync(jsSrcDir)) {
        copyDirectoryRecursive(jsSrcDir, jsDestDir, null);
    } else {
        console.warn('⚠️ js folder not found');
    }
}

function processImages() {
    const imgSrcDir = path.join(ROOT_DIR, 'img');
    const imgDestDir = path.join(DIST_DIR, 'img');
    if (fs.existsSync(imgSrcDir)) {
        copyDirectoryRecursive(imgSrcDir, imgDestDir, null);
    } else {
        console.warn('⚠️ img folder not found');
    }
}

function createSampleCustomFile() {
    const sampleContent = `/*
 * Sample file project-custom.css
 * ---------------------------------
 * 1. Load project-required fonts via @import from CDN.
 * 2. Override brand variables (color, primary font, etc.) in :root.
 *
 * Save this file as project-custom.css in your project.
 */

/* ── Required fonts───────────────────── */
@import url('${CDN_BASE_URL}/fonts/IranSansX/font-face.css');
@import url('${CDN_BASE_URL}/fonts/Vazirmatn/font-face.css');

/* ── Project brand variables──────────────────── */
:root {
  --brand-primary: #e63946;
  --font-primary: 'Vazirmatn', sans-serif;
}
`;
    const dest = path.join(DIST_DIR, 'project-custom-sample.css');
    fs.writeFileSync(dest, sampleContent, 'utf-8');
    console.log(`✅ Sample file project-custom-sample.css created.`);
}

function cleanDist() {
    if (fs.existsSync(DIST_DIR)) {
        fs.rmSync(DIST_DIR, { recursive: true, force: true });
        console.log('🧹 Previous dist/cdn folder cleaned up.');
    }
}

// ========== Run ==========
console.log('🚀 Preparing PST project for CDN (output in dist/cdn)...\n');
cleanDist();
console.log('\n📁 Processing CSS...');
processCSS();
console.log('\n🔤 Processing fonts...');
processFonts();
console.log('\n📜 Processing JavaScript...');
processJS();
console.log('\n🖼️  Processing images...');
processImages();
console.log('\n📝 Creating sample file project-custom.css...');
createSampleCustomFile();
console.log('\n🎉 Done! The dist/cdn folder is ready for upload to CDN.');