/**
 * Lottie Global Integration - CDN Load in Head
 * Performance Mode: Baseline (~85 score)
 * Loads Lottie player from CDN immediately in document head
 */

// This file is loaded after the CDN script in the head
// All animations will be initialized immediately when DOM is ready

document.addEventListener('DOMContentLoaded', function() {
  console.log('Lottie Global Mode: Initializing all animations immediately');
  
  // Find all dotlottie-player elements and ensure they're ready
  const players = document.querySelectorAll('dotlottie-player');
  
  players.forEach((player, index) => {
    // Add loading class
    player.classList.add('loading');
    
    // Listen for load event
    player.addEventListener('ready', function() {
      player.classList.remove('loading');
      player.classList.add('fade-in');
      console.log(`Animation ${index + 1} loaded`);
    });
    
    // Fallback: remove loading class after 3 seconds
    setTimeout(() => {
      player.classList.remove('loading');
    }, 3000);
  });
  
  // Performance tracking
  if (window.performance && window.performance.mark) {
    window.performance.mark('lottie-global-init-complete');
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
