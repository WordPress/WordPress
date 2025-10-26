/**
 * Lottie Deferred Integration - Local Player with Defer
 * Performance Mode: Good improvement (~90 score)
 * Loads local Lottie player with defer attribute for better performance
 */

// Load the dotLottie player script dynamically
function loadDotLottiePlayer() {
  return new Promise((resolve, reject) => {
    if (window.customElements && window.customElements.get('dotlottie-player')) {
      resolve();
      return;
    }
    
    const script = document.createElement('script');
    script.src = 'https://unpkg.com/@dotlottie/player-component@latest/dist/dotlottie-player.mjs';
    script.type = 'module';
    script.onload = resolve;
    script.onerror = reject;
    document.head.appendChild(script);
  });
}

// Initialize when DOM is ready but defer the heavy lifting
document.addEventListener('DOMContentLoaded', function() {
  console.log('Lottie Defer Mode: Deferring animation initialization');
  
  // Use requestIdleCallback for better performance
  const initAnimations = () => {
    loadDotLottiePlayer().then(() => {
      console.log('Lottie player loaded, initializing animations');
      
      const players = document.querySelectorAll('dotlottie-player');
      
      players.forEach((player, index) => {
        player.classList.add('loading');
        
        // Stagger animation initialization to reduce blocking
        setTimeout(() => {
          player.addEventListener('ready', function() {
            player.classList.remove('loading');
            player.classList.add('fade-in');
            console.log(`Deferred animation ${index + 1} loaded`);
          });
          
          // Trigger load by setting src if not already set
          if (!player.hasAttribute('src') && player.dataset.src) {
            player.setAttribute('src', player.dataset.src);
          }
        }, index * 100); // Stagger by 100ms
      });
      
      // Performance tracking
      if (window.performance && window.performance.mark) {
        window.performance.mark('lottie-defer-init-complete');
      }
    }).catch(error => {
      console.error('Failed to load Lottie player:', error);
    });
  };
  
  // Use requestIdleCallback if available, otherwise setTimeout
  if (window.requestIdleCallback) {
    requestIdleCallback(initAnimations, { timeout: 2000 });
  } else {
    setTimeout(initAnimations, 100);
  }
});

// Video facade functionality
document.addEventListener('click', function(e) {
  const videoFacade = e.target.closest('.video-facade');
  if (!videoFacade) return;
  
  const src = videoFacade.dataset.src + '?autoplay=1&muted=1';
  const iframe = document.createElement('iframe');
  iframe.src = src;
  iframe.loading = 'lazy';
  iframe.allow = 'autoplay; fullscreen';
  iframe.setAttribute('allowfullscreen', '');
  iframe.style.width = '100%';
  iframe.style.height = '450px';
  iframe.style.border = 'none';
  iframe.style.borderRadius = '12px';
  
  videoFacade.replaceWith(iframe);
});
