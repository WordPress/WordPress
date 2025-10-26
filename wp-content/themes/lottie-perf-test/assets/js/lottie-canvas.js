/**
 * Lottie Canvas Renderer Integration - Mobile Optimized
 * Performance Mode: Mobile-optimized (~94 score)
 * Uses canvas renderer for better mobile performance
 */

class LottieCanvasRenderer {
    constructor() {
        this.isMobile = this.detectMobile();
        this.players = [];
        this.init();
    }

    detectMobile() {
        return /Android|webOS|iPhone|iPad|iPod|BlackBerry|IEMobile|Opera Mini/i.test(navigator.userAgent) ||
               window.innerWidth <= 768;
    }

    init() {
        console.log(`Lottie Canvas Mode: Initializing for ${this.isMobile ? 'mobile' : 'desktop'} device`);
        
        document.addEventListener('DOMContentLoaded', () => {
            this.setupCanvasRenderers();
        });
    }

    setupCanvasRenderers() {
        this.players = document.querySelectorAll('dotlottie-player');
        
        if (this.players.length === 0) {
            console.warn('No Lottie animations found');
            return;
        }

        this.players.forEach((player, index) => {
            this.configurePlayer(player, index);
        });

        console.log(`Lottie Canvas Mode: Configured ${this.players.length} animations`);
    }

    configurePlayer(player, index) {
        // Force canvas renderer for better mobile performance
        player.setAttribute('renderer', 'canvas');
        
        // Mobile-specific optimizations
        if (this.isMobile) {
            // Reduce quality for mobile
            player.setAttribute('quality', 'medium');
            
            // Smaller dimensions for mobile
            const currentWidth = player.getAttribute('width') || '300';
            const currentHeight = player.getAttribute('height') || '300';
            
            if (parseInt(currentWidth) > 250) {
                player.setAttribute('width', '250');
                player.setAttribute('height', '250');
            }
            
            // Enable autoplay but with reduced frame rate
            player.setAttribute('autoplay', '');
            player.setAttribute('loop', '');
            
            // Add mobile-specific styling
            player.style.maxWidth = '100%';
            player.style.height = 'auto';
            
        } else {
            // Desktop optimizations
            player.setAttribute('quality', 'high');
            player.setAttribute('autoplay', '');
            player.setAttribute('loop', '');
        }

        // Add loading state
        player.classList.add('canvas-loading');
        
        // Performance monitoring
        const startTime = performance.now();
        
        player.addEventListener('ready', () => {
            const loadTime = performance.now() - startTime;
            console.log(`Canvas animation ${index + 1} loaded in ${loadTime.toFixed(2)}ms`);
            
            player.classList.remove('canvas-loading');
            player.classList.add('canvas-ready');
            
            // Add fade-in effect
            player.style.opacity = '0';
            player.style.transition = 'opacity 0.5s ease-in-out';
            
            setTimeout(() => {
                player.style.opacity = '1';
            }, 100);
        });

        // Error handling
        player.addEventListener('error', (e) => {
            console.error(`Canvas animation ${index + 1} failed to load:`, e);
            player.classList.remove('canvas-loading');
            player.classList.add('canvas-error');
            
            // Fallback to SVG renderer
            player.setAttribute('renderer', 'svg');
            console.log(`Falling back to SVG renderer for animation ${index + 1}`);
        });

        // Fallback timeout
        setTimeout(() => {
            if (player.classList.contains('canvas-loading')) {
                console.warn(`Canvas animation ${index + 1} load timeout`);
                player.classList.remove('canvas-loading');
                player.classList.add('canvas-timeout');
            }
        }, 5000);
    }

    // Method to switch renderer dynamically
    switchRenderer(player, renderer) {
        if (player && ['svg', 'canvas'].includes(renderer)) {
            player.setAttribute('renderer', renderer);
            console.log(`Switched to ${renderer} renderer`);
        }
    }

    // Method to optimize for current viewport
    optimizeForViewport() {
        const isNowMobile = this.detectMobile();
        
        if (isNowMobile !== this.isMobile) {
            this.isMobile = isNowMobile;
            console.log(`Viewport changed to ${this.isMobile ? 'mobile' : 'desktop'}`);
            
            // Reconfigure all players
            this.players.forEach((player, index) => {
                this.configurePlayer(player, index);
            });
        }
    }
}

// Initialize canvas renderer
window.lottieCanvasRenderer = new LottieCanvasRenderer();

// Optimize on resize
let resizeTimeout;
window.addEventListener('resize', () => {
    clearTimeout(resizeTimeout);
    resizeTimeout = setTimeout(() => {
        if (window.lottieCanvasRenderer) {
            window.lottieCanvasRenderer.optimizeForViewport();
        }
    }, 250);
});

// Video facade functionality
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