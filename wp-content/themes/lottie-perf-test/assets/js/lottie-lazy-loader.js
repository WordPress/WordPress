/**
 * Lottie Lazy Loader - IntersectionObserver-based lazy loading for Lottie animations
 * Prevents CLS and improves performance by loading animations only when needed
 */
class LottieLazyLoader {
  constructor() {
    this.observer = null;
    this.loadedAnimations = new Set();
    this.init();
  }
  
  init() {
    if ('IntersectionObserver' in window) {
      this.observer = new IntersectionObserver((entries) => {
        entries.forEach(entry => {
          if (entry.isIntersecting) {
            this.loadAnimation(entry.target);
            this.observer.unobserve(entry.target);
          }
        });
      }, {
        rootMargin: '100px 0px', // Start loading 100px before element comes into view
        threshold: 0.1
      });
      
      this.observeAnimations();
    } else {
      // Fallback for older browsers - load all animations immediately
      this.loadAllAnimations();
    }
  }
  
  observeAnimations() {
    const lottiePlayers = document.querySelectorAll('dotlottie-player[data-lazy]');
    lottiePlayers.forEach(player => {
      this.observer.observe(player);
    });
  }
  
  loadAnimation(player) {
    const src = player.getAttribute('src');
    if (!src || this.loadedAnimations.has(src)) return;
    
    this.loadedAnimations.add(src);
    
    // Add loading state
    player.classList.add('lottie-loading');
    
    // Create placeholder with proper dimensions
    const placeholder = this.createPlaceholder(player);
    player.parentNode.insertBefore(placeholder, player);
    
    // Load the animation
    this.loadLottieFile(src).then(() => {
      // Remove placeholder and show animation
      if (placeholder.parentNode) {
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
        }, 100);
      }
    }).catch(error => {
      console.error('Failed to load Lottie animation:', error);
      // Remove placeholder on error
      if (placeholder.parentNode) {
        placeholder.parentNode.removeChild(placeholder);
      }
      player.classList.add('lottie-error');
    });
  }
  
  createPlaceholder(player) {
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
    
    // Add CSS animation
    if (!document.querySelector('#lottie-lazy-styles')) {
      const style = document.createElement('style');
      style.id = 'lottie-lazy-styles';
      style.textContent = `
        @keyframes lottie-spin {
          0% { transform: rotate(0deg); }
          100% { transform: rotate(360deg); }
        }
        .lottie-loading {
          opacity: 0;
        }
        .lottie-loaded {
          opacity: 1;
          transition: opacity 0.3s ease;
        }
        .lottie-error {
          background: #ffebee;
          color: #c62828;
          display: flex;
          align-items: center;
          justify-content: center;
          font-size: 14px;
        }
      `;
      document.head.appendChild(style);
    }
    
    return placeholder;
  }
  
  async loadLottieFile(src) {
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
  
  loadAllAnimations() {
    const lottiePlayers = document.querySelectorAll('dotlottie-player[data-lazy]');
    lottiePlayers.forEach(player => {
      this.loadAnimation(player);
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
    new LottieLazyLoader();
  });
} else {
  new LottieLazyLoader();
}
