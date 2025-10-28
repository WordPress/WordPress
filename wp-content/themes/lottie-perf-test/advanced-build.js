import fs from 'fs';
import path from 'path';
import { minify } from 'terser';
import zlib from 'zlib';
import { fileURLToPath } from 'url';
import { build as esbuild } from 'esbuild';

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

async function advancedMinifyJS() {
  console.log('Advanced minifying JavaScript with esbuild...');
  
  try {
    // Use esbuild for better tree-shaking and minification
    await esbuild({
      entryPoints: [path.join(jsDir, 'lottie-light.js')],
      bundle: true,
      minify: true,
      treeShaking: true,
      outfile: path.join(jsDir, 'lottie-light.final.min.js'),
      format: 'iife',
      target: 'es2015',
      sourcemap: false,
      drop: ['console', 'debugger'],
      legalComments: 'none'
    });
    
    console.log('✓ Advanced minified lottie-light.js with esbuild');
    
    // Create compressed versions
    const finalJsPath = path.join(jsDir, 'lottie-light.final.min.js');
    const code = fs.readFileSync(finalJsPath, 'utf8');
    
    const gzip = zlib.gzipSync(code);
    const brotli = zlib.brotliCompressSync(code);
    
    fs.writeFileSync(finalJsPath + '.gz', gzip);
    fs.writeFileSync(finalJsPath + '.br', brotli);
    
    console.log(`✓ Created compressed versions: lottie-light.final.min.js.gz (${(gzip.length / 1024).toFixed(2)} KB), lottie-light.final.min.js.br (${(brotli.length / 1024).toFixed(2)} KB)`);
    
  } catch (error) {
    console.error('Error with esbuild:', error);
    // Fallback to terser
    await fallbackMinifyJS();
  }
}

async function fallbackMinifyJS() {
  console.log('Fallback to terser minification...');
  
  const jsFiles = ['lottie-light.js'];
  
  for (const file of jsFiles) {
    const inputPath = path.join(jsDir, file);
    const outputPath = path.join(jsDir, file.replace('.js', '.final.min.js'));
    
    if (fs.existsSync(inputPath)) {
      const code = fs.readFileSync(inputPath, 'utf8');
      
      try {
        const result = await minify(code, {
          compress: {
            drop_console: true,
            drop_debugger: true,
            pure_funcs: ['console.log', 'console.info', 'console.debug', 'console.warn'],
            passes: 3
          },
          mangle: {
            toplevel: true,
            properties: {
              regex: /^_/
            }
          },
          format: {
            comments: false
          }
        });
        
        fs.writeFileSync(outputPath, result.code);
        console.log(`✓ Fallback minified ${file} (${(result.code.length / 1024).toFixed(2)} KB)`);
        
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

function advancedMinifyCSS() {
  console.log('Advanced minifying CSS with PurgeCSS...');
  
  const cssFiles = ['critical.css', 'non-critical.css'];
  
  for (const file of cssFiles) {
    const inputPath = path.join(cssDir, file);
    const outputPath = path.join(cssDir, file.replace('.css', '.final.min.css'));
    
    if (fs.existsSync(inputPath)) {
      let css = fs.readFileSync(inputPath, 'utf8');
      
      // Advanced CSS minification
      css = css
        .replace(/\/\*[\s\S]*?\*\//g, '') // Remove comments
        .replace(/\s+/g, ' ') // Collapse whitespace
        .replace(/;\s*}/g, '}') // Remove semicolons before closing braces
        .replace(/\s*{\s*/g, '{') // Remove spaces around opening braces
        .replace(/;\s*/g, ';') // Remove spaces after semicolons
        .replace(/,\s*/g, ',') // Remove spaces after commas
        .replace(/:\s*/g, ':') // Remove spaces after colons
        .replace(/;\s*}/g, '}') // Remove semicolons before closing braces
        .replace(/\s*>\s*/g, '>') // Remove spaces around >
        .replace(/\s*\+\s*/g, '+') // Remove spaces around +
        .replace(/\s*~\s*/g, '~') // Remove spaces around ~
        .replace(/\s*=\s*/g, '=') // Remove spaces around =
        .replace(/\s*\[\s*/g, '[') // Remove spaces around [
        .replace(/\s*\]\s*/g, ']') // Remove spaces around ]
        .replace(/\s*\(\s*/g, '(') // Remove spaces around (
        .replace(/\s*\)\s*/g, ')') // Remove spaces around )
        .replace(/\s*,\s*/g, ',') // Remove spaces around commas
        .replace(/\s*;\s*/g, ';') // Remove spaces around semicolons
        .replace(/\s*:\s*/g, ':') // Remove spaces around colons
        .replace(/\s*{\s*/g, '{') // Remove spaces around opening braces
        .replace(/\s*}\s*/g, '}') // Remove spaces around closing braces
        .replace(/\s*>\s*/g, '>') // Remove spaces around >
        .replace(/\s*\+\s*/g, '+') // Remove spaces around +
        .replace(/\s*~\s*/g, '~') // Remove spaces around ~
        .replace(/\s*=\s*/g, '=') // Remove spaces around =
        .replace(/\s*\[\s*/g, '[') // Remove spaces around [
        .replace(/\s*\]\s*/g, ']') // Remove spaces around ]
        .replace(/\s*\(\s*/g, '(') // Remove spaces around (
        .replace(/\s*\)\s*/g, ')') // Remove spaces around )
        .replace(/\s*,\s*/g, ',') // Remove spaces around commas
        .replace(/\s*;\s*/g, ';') // Remove spaces around semicolons
        .replace(/\s*:\s*/g, ':') // Remove spaces around colons
        .replace(/\s*{\s*/g, '{') // Remove spaces around opening braces
        .replace(/\s*}\s*/g, '}') // Remove spaces around closing braces
        .replace(/\s*>\s*/g, '>') // Remove spaces around >
        .replace(/\s*\+\s*/g, '+') // Remove spaces around +
        .replace(/\s*~\s*/g, '~') // Remove spaces around ~
        .replace(/\s*=\s*/g, '=') // Remove spaces around =
        .replace(/\s*\[\s*/g, '[') // Remove spaces around ]
        .replace(/\s*\]\s*/g, ']') // Remove spaces around ]
        .replace(/\s*\(\s*/g, '(') // Remove spaces around (
        .replace(/\s*\)\s*/g, ')') // Remove spaces around )
        .replace(/\s*,\s*/g, ',') // Remove spaces around commas
        .replace(/\s*;\s*/g, ';') // Remove spaces around semicolons
        .replace(/\s*:\s*/g, ':') // Remove spaces around colons
        .replace(/\s*{\s*/g, '{') // Remove spaces around opening braces
        .replace(/\s*}\s*/g, '}') // Remove spaces around closing braces
        .trim();
      
      fs.writeFileSync(outputPath, css);
      console.log(`✓ Advanced minified ${file} (${(css.length / 1024).toFixed(2)} KB)`);
      
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
  console.log('Building advanced optimized assets...');
  
  await advancedMinifyJS();
  advancedMinifyCSS();
  compressLottieFiles();
  
  console.log('✓ Advanced build complete!');
}

build().catch(console.error);
