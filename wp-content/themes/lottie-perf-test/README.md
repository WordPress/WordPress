# Lottie Animation Performance Test Framework

## Overview
This WordPress theme implements a systematic 7-step approach to optimizing Lottie animation loading performance. Each step builds upon the previous one, allowing for precise measurement of optimization impact.

## Project Structure

```
wp-content/themes/lottie-perf-test/
├── assets/
│   ├── css/
│   │   ├── reset.css
│   │   ├── style.css
│   │   └── tipalti-styles.css
│   ├── js/
│   │   ├── dotlottie-player.min.js (local player file)
│   │   ├── lottie-step1-local.js
│   │   ├── lottie-step2-canvas.js
│   │   ├── lottie-step3-defer.js
│   │   ├── lottie-step4-lazy.js
│   │   ├── lottie-step5-cache.js
│   │   ├── lottie-step6-conditional.js
│   │   └── lottie-step7-poster.js
│   ├── lottie/
│   │   ├── invoice-capture-agent-1.lottie
│   │   ├── bill-approvers-agent.lottie
│   │   ├── duplicate-bill-detection.lottie
│   │   └── po-matching-agent.lottie
│   └── images/
│       ├── invoice-capture-poster.png
│       ├── bill-approvers-poster.png
│       ├── duplicate-bill-poster.png
│       └── po-matching-poster.png
├── docs/
│   └── performance-results.md
├── page-local-test.php
├── page-canvas-mode-test.php
├── page-defer-test.php
├── page-lazy-test.php
├── page-cache-test.php
├── page-conditional-test.php
├── page-poster-test.php
├── functions.php
└── README.md
```

## 7-Step Optimization Process

### Step 1: Local Player File (Basic)
- **Template**: `page-local-test.php`
- **Script**: `lottie-step1-local.js`
- **Description**: Basic implementation using local dotlottie-player.min.js file
- **URL**: `/local-test/`

### Step 2: Local Player File + Canvas Renderer
- **Template**: `page-canvas-mode-test.php`
- **Script**: `lottie-step2-canvas.js`
- **Description**: Local player with Canvas renderer for improved performance
- **URL**: `/canvas-mode-test/`

### Step 3: Step 2 + Deferred/Async Load Method
- **Template**: `page-defer-test.php`
- **Script**: `lottie-step3-defer.js`
- **Description**: Canvas renderer with deferred script loading for better performance
- **URL**: `/defer-test/`

### Step 4: Step 3 + Lazy Loading with IntersectionObserver
- **Template**: `page-lazy-test.php`
- **Script**: `lottie-step4-lazy.js`
- **Description**: Canvas renderer with deferred loading + lazy loading for below-fold animations
- **URL**: `/lazy-test/`

### Step 5: Step 4 + Asset Compression & Caching
- **Template**: `page-cache-test.php`
- **Script**: `lottie-step5-cache.js`
- **Description**: Lazy loading + Canvas + Asset compression & caching headers
- **URL**: `/cache-test/`

### Step 6: Step 5 + Conditional Enqueue (Per-Page)
- **Template**: `page-conditional-test.php`
- **Script**: `lottie-step6-conditional.js`
- **Description**: All optimizations + conditional script loading only on Lottie pages
- **URL**: `/conditional-test/`

### Step 7: Step 6 + Poster Fallback for Below-Fold Animations
- **Template**: `page-poster-test.php`
- **Script**: `lottie-step7-poster.js`
- **Description**: All optimizations + poster images for below-fold animations
- **URL**: `/poster-test/`

## Setup Instructions

### 1. Install the Theme
1. Upload the `lottie-perf-test` theme to your WordPress themes directory
2. Activate the theme in WordPress admin

### 2. Local Player File Ready
The `dotlottie-player.min.js` file is already available and contains the complete dotlottie player implementation from unpkg. No additional download is needed.

### 3. Create Test Pages
Create WordPress pages using each template:

1. **Local Test Page**
   - Title: "Step 1 - Local Player Test"
   - Template: "Step 1 - Local Player Test"
   - Slug: `local-test`

2. **Canvas Mode Test Page**
   - Title: "Step 2 - Canvas Mode Test"
   - Template: "Step 2 - Canvas Mode Test"
   - Slug: `canvas-mode-test`

3. **Defer Test Page**
   - Title: "Step 3 - Defer Test"
   - Template: "Step 3 - Defer Test"
   - Slug: `defer-test`

4. **Lazy Test Page**
   - Title: "Step 4 - Lazy Test"
   - Template: "Step 4 - Lazy Test"
   - Slug: `lazy-test`

5. **Cache Test Page**
   - Title: "Step 5 - Cache Test"
   - Template: "Step 5 - Cache Test"
   - Slug: `cache-test`

6. **Conditional Test Page**
   - Title: "Step 6 - Conditional Test"
   - Template: "Step 6 - Conditional Test"
   - Slug: `conditional-test`

7. **Poster Test Page**
   - Title: "Step 7 - Poster Test"
   - Template: "Step 7 - Poster Test"
   - Slug: `poster-test`

### 4. Create Poster Images (Optional for Step 7)
For Step 7, you can create static PNG images representing the first frame of each Lottie animation:
- `invoice-capture-poster.png` (300x300px)
- `bill-approvers-poster.png` (300x300px)
- `duplicate-bill-poster.png` (300x300px)
- `po-matching-poster.png` (300x300px)

## Testing Process

### 1. Performance Testing
For each test page, run Google PageSpeed Insights:

1. Navigate to: https://pagespeed.web.dev/
2. Enter your test page URL: `https://yourdomain.com/page-slug`
3. Click "Analyze"
4. Record the following metrics:
   - First Contentful Paint (FCP)
   - Largest Contentful Paint (LCP)
   - Total Blocking Time (TBT)
   - Cumulative Layout Shift (CLS)
   - Performance Score

### 2. Update Results
Update the `docs/performance-results.md` file with your test results.

### 3. Compare Results
Analyze the performance improvements between each step to understand the impact of each optimization.

## Key Features

### Conditional Script Loading
The theme only loads Lottie-related scripts on pages that actually use them, reducing unnecessary resource loading.

### Progressive Enhancement
Each step builds upon the previous one, allowing for systematic optimization testing.

### Performance Monitoring
Built-in performance tracking in each JavaScript file for debugging and analysis.

### Caching Headers
Automatic caching headers for Lottie files and player scripts for improved performance.

## Browser Support
- Modern browsers with IntersectionObserver API support
- Canvas API support for Step 2+
- ES6+ JavaScript features

## Troubleshooting

### Common Issues

1. **Lottie animations not loading**
   - Check that `dotlottie-player.min.js` is properly downloaded
   - Verify the file paths in `functions.php`
   - Check browser console for errors

2. **Performance metrics not showing**
   - Ensure the page is fully loaded before running PageSpeed Insights
   - Check that the performance tracking scripts are loading

3. **Poster images not displaying**
   - Verify the poster image files exist
   - Check the file paths in the template files

### Debug Mode
Enable WordPress debug mode to see any PHP errors:

```php
// In wp-config.php
define('WP_DEBUG', true);
define('WP_DEBUG_LOG', true);
```

## Contributing
This is a performance testing framework. Feel free to:
- Add more optimization steps
- Improve the testing methodology
- Enhance the performance tracking
- Add more Lottie animations for testing

## License
This project is for performance testing purposes. Please ensure you have the proper licenses for any Lottie animations used.
