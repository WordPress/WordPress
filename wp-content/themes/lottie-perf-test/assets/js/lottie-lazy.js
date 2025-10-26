/**
 * Lottie Lazy Loading Integration - Intersection Observer
 * Performance Mode: Best result (≥95 score)
 * Only loads animations when they come into viewport
 */

// Intersection Observer for lazy loading
let lottiePlayerLoaded = false;
let observerInitialized = false;

// Load the dotLottie player script only when needed
function loadDotLottiePlayer() {
  if (lottiePlayerLoaded) return Promise.resolve();
  
  return new Promise((resolve, reject) => {
    if (window.customElements && window.customElements.get('dotlottie-player')) {
      lottiePlayerLoaded = true;
      resolve();
      return;
    }
    
    const script = document.createElement('script');
    script.src = 'https://unpkg.com/@dotlottie/player-component@latest/dist/dotlottie-player.mjs';
    script.type = 'module';
    script.onload = () => {
      lottiePlayerLoaded = true;
      resolve();
    };
    script.onerror = reject;
    document.head.appendChild(script);
  });
}

// Initialize lazy loading when DOM is ready
document.addEventListener('DOMContentLoaded', function() {
  console.log('Lottie Lazy Mode: Setting up intersection observer');
  
  // Only initialize if Intersection Observer is supported
  if (!('IntersectionObserver' in window)) {
    console.warn('IntersectionObserver not supported, falling back to immediate load');
    loadAllAnimations();
    return;
  }
  
  setupLazyLoading();
});

function setupLazyLoading() {
  if (observerInitialized) return;
  
  const options = {
    root: null,
    rootMargin: '50px', // Start loading 50px before element enters viewport
    threshold: 0.1 // Trigger when 10% of element is visible
  };
  
  const observer = new IntersectionObserver((entries) => {
    entries.forEach(entry => {
      if (entry.isIntersecting) {
        const player = entry.target;
        loadAnimation(player);
        observer.unobserve(player); // Stop observing once loaded
      }
    });
  }, options);
  
  // Observe all dotlottie-player elements
  const players = document.querySelectorAll('dotlottie-player');
  players.forEach(player => {
    // Add placeholder styling
    player.classList.add('loading');
    player.style.minHeight = '300px'; // Prevent layout shift
    observer.observe(player);
  });
  
  observerInitialized = true;
  
  // Performance tracking
  if (window.performance && window.performance.mark) {
    window.performance.mark('lottie-lazy-observer-setup');
  }
}

async function loadAnimation(player) {
  try {
    // Load the player script if not already loaded
    await loadDotLottiePlayer();
    
    console.log('Loading animation lazily:', player.getAttribute('src'));
    
    // Set up the ready event listener
    player.addEventListener('ready', function() {
      player.classList.remove('loading');
      player.classList.add('fade-in');
      console.log('Lazy animation loaded successfully');
    });
    
    // Trigger the animation load by ensuring src is set
    const src = player.getAttribute('src') || player.dataset.src;
    if (src && !player.hasAttribute('src')) {
      player.setAttribute('src', src);
    }
    
    // Fallback: remove loading class after timeout
    setTimeout(() => {
      player.classList.remove('loading');
    }, 3000);
    
  } catch (error) {
    console.error('Failed to load animation:', error);
    player.classList.remove('loading');
  }
}

// Fallback function for browsers without Intersection Observer
function loadAllAnimations() {
  loadDotLottiePlayer().then(() => {
    const players = document.querySelectorAll('dotlottie-player');
    players.forEach((player, index) => {
      setTimeout(() => {
        loadAnimation(player);
      }, index * 200); // Stagger loading
    });
  });
}

// Video facade functionality with lazy loading
document.addEventListener('click', function(e) {
  const videoFacade = e.target.closest('.video-facade');
  if (!videoFacade) return;
  
  // Add loading state
  videoFacade.style.opacity = '0.7';
  
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
  
  // Smooth transition
  iframe.onload = () => {
    iframe.style.opacity = '0';
    iframe.style.transition = 'opacity 0.3s ease';
    videoFacade.replaceWith(iframe);
    setTimeout(() => {
      iframe.style.opacity = '1';
    }, 10);
  };
  
  // Fallback
  setTimeout(() => {
    if (videoFacade.parentNode) {
      videoFacade.replaceWith(iframe);
    }
  }, 2000);
});
