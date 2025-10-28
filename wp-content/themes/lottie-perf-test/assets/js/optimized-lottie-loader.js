/**
 * Optimized Lottie Loader for Speed Index improvement
 * Loads critical Lottie animations immediately, others on demand
 */
class OptimizedLottieLoader {
  constructor() {
    this.criticalAnimations = new Set();
    this.loadedAnimations = new Set();
    this.observer = null;
    this.init();
  }
  
  init() {
    // Mark critical animations (above the fold)
    this.markCriticalAnimations();
    
    // Load critical animations immediately
    this.loadCriticalAnimations();
    
    // Set up lazy loading for non-critical animations
    this.setupLazyLoading();
  }
  
  markCriticalAnimations() {
    // First 3 Lottie animations are considered critical (above the fold)
    const lottiePlayers = document.querySelectorAll('dotlottie-player[data-lazy]');
    lottiePlayers.forEach((player, index) => {
      if (index < 3) {
        this.criticalAnimations.add(player);
        player.removeAttribute('data-lazy'); // Remove lazy attribute for critical animations
      }
    });
  }
  
  loadCriticalAnimations() {
    this.criticalAnimations.forEach(player => {
      this.loadAnimation(player, true);
    });
  }
  
  setupLazyLoading() {
    if ('IntersectionObserver' in window) {
      this.observer = new IntersectionObserver((entries) => {
        entries.forEach(entry => {
          if (entry.isIntersecting) {
            this.loadAnimation(entry.target);
            this.observer.unobserve(entry.target);
          }
        });
      }, {
        rootMargin: '200px 0px', // Start loading 200px before element comes into view
        threshold: 0.1
      });
      
      // Observe non-critical animations
      const nonCriticalPlayers = document.querySelectorAll('dotlottie-player[data-lazy]');
      nonCriticalPlayers.forEach(player => {
        this.observer.observe(player);
      });
    } else {
      // Fallback: load all animations immediately
      const allPlayers = document.querySelectorAll('dotlottie-player');
      allPlayers.forEach(player => this.loadAnimation(player));
    }
  }
  
  loadAnimation(player, isCritical = false) {
    const src = player.getAttribute('src');
    if (!src || this.loadedAnimations.has(src)) return;
    
    this.loadedAnimations.add(src);
    
    // Add loading state
    player.classList.add('lottie-loading');
    
    // For critical animations, show immediately
    if (isCritical) {
      player.style.opacity = '1';
      player.style.visibility = 'visible';
    }
    
    // Create placeholder with proper dimensions
    const placeholder = this.createPlaceholder(player, isCritical);
    if (!isCritical) {
      player.parentNode.insertBefore(placeholder, player);
    }
    
    // Preload the animation file
    this.preloadLottieFile(src).then(() => {
      // Remove placeholder and show animation
      if (placeholder && placeholder.parentNode) {
        placeholder.parentNode.removeChild(placeholder);
      }
      player.classList.remove('lottie-loading');
      player.classList.add('lottie-loaded');
      
      // Trigger play if autoplay is set
      if (player.hasAttribute('autoplay')) {
        setTimeout(() => {
          try {
            player.play();
          } catch (e) {
            console.warn('Could not autoplay Lottie animation:', e);
          }
        }, 50);
      }
    }).catch(error => {
      console.error('Failed to load Lottie animation:', error);
      if (placeholder && placeholder.parentNode) {
        placeholder.parentNode.removeChild(placeholder);
      }
      player.classList.add('lottie-error');
    });
  }
  
  createPlaceholder(player, isCritical = false) {
    const placeholder = document.createElement('div');
    placeholder.className = 'lottie-placeholder';
    
    // Get dimensions from player
    const width = player.style.width || '200px';
    const height = player.style.height || '200px';
    
    placeholder.style.cssText = `
      width: ${width};
      height: ${height};
      background: linear-gradient(45deg, #f0f0f0 25%, transparent 25%), 
                  linear-gradient(-45deg, #f0f0f0 25%, transparent 25%), 
                  linear-gradient(45deg, transparent 75%, #f0f0f0 75%), 
                  linear-gradient(-45deg, transparent 75%, #f0f0f0 75%);
      background-size: 20px 20px;
      background-position: 0 0, 0 10px, 10px -10px, -10px 0px;
      display: flex;
      align-items: center;
      justify-content: center;
      border-radius: 8px;
      position: relative;
      overflow: hidden;
      ${isCritical ? 'opacity: 0.3;' : ''}
    `;
    
    // Add loading spinner
    const spinner = document.createElement('div');
    spinner.className = 'lottie-spinner';
    spinner.style.cssText = `
      width: 40px;
      height: 40px;
      border: 3px solid #f3f3f3;
      border-top: 3px solid #4d62d3;
      border-radius: 50%;
      animation: lottie-spin 1s linear infinite;
    `;
    
    placeholder.appendChild(spinner);
    
    return placeholder;
  }
  
  async preloadLottieFile(src) {
    return new Promise((resolve, reject) => {
      // Check if file is already loaded
      if (this.loadedAnimations.has(src)) {
        resolve();
        return;
      }
      
      // Create a fetch request to preload the file
      fetch(src, {
        method: 'HEAD',
        cache: 'force-cache'
      }).then(response => {
        if (response.ok) {
          resolve();
        } else {
          reject(new Error(`Failed to load ${src}: ${response.status}`));
        }
      }).catch(error => {
        reject(error);
      });
    });
  }
  
  destroy() {
    if (this.observer) {
      this.observer.disconnect();
    }
  }
}

// Initialize when DOM is ready
if (document.readyState === 'loading') {
  document.addEventListener('DOMContentLoaded', () => {
    new OptimizedLottieLoader();
  });
} else {
  new OptimizedLottieLoader();
}
