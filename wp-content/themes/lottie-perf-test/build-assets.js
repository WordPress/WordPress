import fs from 'fs';
import path from 'path';
import { minify } from 'terser';
import zlib from 'zlib';
import { fileURLToPath } from 'url';

const __filename = fileURLToPath(import.meta.url);
const __dirname = path.dirname(__filename);

const assetsDir = path.join(__dirname, 'assets');
const jsDir = path.join(assetsDir, 'js');
const cssDir = path.join(assetsDir, 'css');

// Ensure dist directory exists
const distDir = path.join(assetsDir, 'dist');
if (!fs.existsSync(distDir)) {
  fs.mkdirSync(distDir, { recursive: true });
}

async function minifyJS() {
  console.log('Minifying JavaScript...');
  
  const jsFiles = ['lottie-light.js'];
  
  for (const file of jsFiles) {
    const inputPath = path.join(jsDir, file);
    const outputPath = path.join(jsDir, file.replace('.js', '.min.js'));
    
    if (fs.existsSync(inputPath)) {
      const code = fs.readFileSync(inputPath, 'utf8');
      
      try {
        const result = await minify(code, {
          compress: {
            drop_console: true,
            drop_debugger: true,
            pure_funcs: ['console.log', 'console.info', 'console.debug']
          },
          mangle: {
            toplevel: true
          },
          format: {
            comments: false
          }
        });
        
        fs.writeFileSync(outputPath, result.code);
        console.log(`✓ Minified ${file} (${(result.code.length / 1024).toFixed(2)} KB)`);
        
        // Create compressed versions
        const gzip = zlib.gzipSync(result.code);
        const brotli = zlib.brotliCompressSync(result.code);
        
        fs.writeFileSync(outputPath + '.gz', gzip);
        fs.writeFileSync(outputPath + '.br', brotli);
        
        console.log(`✓ Created compressed versions: ${file}.gz (${(gzip.length / 1024).toFixed(2)} KB), ${file}.br (${(brotli.length / 1024).toFixed(2)} KB)`);
      } catch (error) {
        console.error(`Error minifying ${file}:`, error);
      }
    }
  }
}

function minifyCSS() {
  console.log('Minifying CSS...');
  
  const cssFiles = ['critical.css', 'non-critical.css'];
  
  for (const file of cssFiles) {
    const inputPath = path.join(cssDir, file);
    const outputPath = path.join(cssDir, file.replace('.css', '.min.css'));
    
    if (fs.existsSync(inputPath)) {
      let css = fs.readFileSync(inputPath, 'utf8');
      
      // Basic CSS minification
      css = css
        .replace(/\/\*[\s\S]*?\*\//g, '') // Remove comments
        .replace(/\s+/g, ' ') // Collapse whitespace
        .replace(/;\s*}/g, '}') // Remove semicolons before closing braces
        .replace(/\s*{\s*/g, '{') // Remove spaces around opening braces
        .replace(/;\s*/g, ';') // Remove spaces after semicolons
        .replace(/,\s*/g, ',') // Remove spaces after commas
        .replace(/:\s*/g, ':') // Remove spaces after colons
        .trim();
      
      fs.writeFileSync(outputPath, css);
      console.log(`✓ Minified ${file} (${(css.length / 1024).toFixed(2)} KB)`);
      
      // Create compressed versions
      const gzip = zlib.gzipSync(css);
      const brotli = zlib.brotliCompressSync(css);
      
      fs.writeFileSync(outputPath + '.gz', gzip);
      fs.writeFileSync(outputPath + '.br', brotli);
      
      console.log(`✓ Created compressed versions: ${file}.gz (${(gzip.length / 1024).toFixed(2)} KB), ${file}.br (${(brotli.length / 1024).toFixed(2)} KB)`);
    }
  }
}

function compressLottieFiles() {
  console.log('Compressing .lottie files...');
  const lottieDir = path.join(assetsDir, 'lottie');
  if (!fs.existsSync(lottieDir)) {
    console.log('No lottie directory found. Skipping.');
    return;
  }
  const files = fs.readdirSync(lottieDir).filter(f => f.endsWith('.lottie'));
  for (const file of files) {
    const inputPath = path.join(lottieDir, file);
    const data = fs.readFileSync(inputPath);
    try {
      const gzip = zlib.gzipSync(data);
      const brotli = zlib.brotliCompressSync(data);
      fs.writeFileSync(inputPath + '.gz', gzip);
      fs.writeFileSync(inputPath + '.br', brotli);
      console.log(`✓ Compressed ${file} → ${file}.gz (${(gzip.length / 1024).toFixed(2)} KB), ${file}.br (${(brotli.length / 1024).toFixed(2)} KB)`);
    } catch (e) {
      console.error('Failed to compress', file, e);
    }
  }
}

async function build() {
  console.log('Building optimized assets...');
  
  await minifyJS();
  minifyCSS();
  compressLottieFiles();
  
  console.log('✓ Build complete!');
}

build().catch(console.error);
