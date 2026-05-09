// use: node generate-tree.js

const fs = require('fs');
const path = require('path');

// Folders that are ignored (you can add or remove)
const IGNORE = ['dist', '.gitignore', 'generate-tree.js', 'PROJECT_TREE.txt', 'pst.code-workspace']; // 'node_modules', '.git', 'dist', '.gitignore', 'generate-tree.js', 'PROJECT_TREE.txt', 'pst.code-workspace'

function tree(dir, prefix = '') {
    let result = '';
    const files = fs.readdirSync(dir).filter(f => !IGNORE.includes(f) && !f.startsWith('.'));
    files.forEach((file, index) => {
        const fullPath = path.join(dir, file);
        const stat = fs.statSync(fullPath);
        const isLast = index === files.length - 1;
        const connector = isLast ? '└── ' : '├── ';
        result += prefix + connector + file + '\n';
        if (stat.isDirectory()) {
            const newPrefix = prefix + (isLast ? '    ' : '│   ');
            result += tree(fullPath, newPrefix);
        }
    });
    return result;
}

const root = process.argv[2] || '.';
const output = 'PROJECT_TREE.txt';
fs.writeFileSync(output, tree(root));
console.log(`✅ Project structure saved in file "${output}".`);