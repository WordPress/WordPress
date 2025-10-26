/**
 * Lottie Deferred Loading Integration - Local Player with Defer
 * Performance Mode: Good improvement (~91 score)
 * Uses requestIdleCallback to load animations during browser idle time
 */

class LottieDeferredLoader {
    constructor() {
        this.players = [];
        this.loadedCount = 0;
        this.init();
    }

    init() {
        console.log('Lottie Defer Mode: Initializing deferred loading');
        
        document.addEventListener('DOMContentLoaded', () => {
            this.setupDeferredLoading();
        });
    }

    setupDeferredLoading() {
        this.players = document.querySelectorAll('dotlottie-player');
        
        if (this.players.length === 0) {
            console.warn('No Lottie animations found');
            return;
        }

        // Add loading placeholders
        this.players.forEach((player, index) => {
            this.addLoadingPlaceholder(player, index);
        });

        // Use requestIdleCallback for optimal loading timing
        if ('requestIdleCallback' in window) {
            console.log('Using requestIdleCallback for deferred loading');
            
            requestIdleCallback(() => {
                this.loadAnimationsDeferred();
            }, { 
                timeout: 2000 // Fallback after 2 seconds
            });
        } else {
            console.log('requestIdleCallback not supported, using setTimeout fallback');
            setTimeout(() => {
                this.loadAnimationsDeferred();
            }, 100);
        }
    }

    addLoadingPlaceholder(player, index) {
        // Create loading placeholder
        const placeholder = document.createElement('div');
        placeholder.className = 'lottie-defer-placeholder';
        placeholder.innerHTML = `
            <div class="defer-spinner"></div>
            <div class="defer-text">Preparing Animation ${index + 1}...</div>
        `;
        
        // Insert placeholder before player
        player.parentNode.insertBefore(placeholder, player);
        
        // Hide player initially
        player.style.display = 'none';
        player.setAttribute('data-deferred', 'true');
    }

    loadAnimationsDeferred() {
        console.log(`Lottie Defer Mode: Starting deferred load of ${this.players.length} animations`);
        
        // Load animations with staggered timing to prevent blocking
        this.players.forEach((player, index) => {
            setTimeout(() => {
                this.loadSingleAnimation(player, index);
            }, index * 100); // Stagger by 100ms each
        });
    }

    async loadSingleAnimation(player, index) {
        const placeholder = player.previousElementSibling;
        
        try {
            // Update placeholder text
            if (placeholder) {
                placeholder.querySelector('.defer-text').textContent = `Loading Animation ${index + 1}...`;
            }

            // Performance tracking
            const startTime = performance.now();
            
            // Load the animation
            await player.load();
            
            // Wait for ready event
            player.addEventListener('ready', () => {
                const loadTime = performance.now() - startTime;
                console.log(`Deferred animation ${index + 1} loaded in ${loadTime.toFixed(2)}ms`);
                
                this.loadedCount++;
                
                // Hide placeholder
                if (placeholder) {
                    placeholder.style.opacity = '0';
                    placeholder.style.transition = 'opacity 0.3s ease';
                    setTimeout(() => placeholder.remove(), 300);
                }
                
                // Show player with fade-in effect
                player.style.display = 'block';
                player.style.opacity = '0';
                player.style.transition = 'opacity 0.5s ease-in-out';
                
                setTimeout(() => {
                    player.style.opacity = '1';
                    player.setAttribute('data-loaded', 'true');
                }, 100);
                
                // Check if all animations are loaded
                if (this.loadedCount === this.players.length) {
                    console.log('Lottie Defer Mode: All animations loaded successfully');
                    this.onAllAnimationsLoaded();
                }
            });

            // Fallback timeout
            setTimeout(() => {
                if (player.getAttribute('data-loaded') !== 'true') {
                    console.warn(`Deferred animation ${index + 1} load timeout`);
                    if (placeholder) placeholder.remove();
                    player.style.display = 'block';
                    player.style.opacity = '1';
                }
            }, 5000);

        } catch (error) {
            console.error(`Error loading deferred animation ${index + 1}:`, error);
            if (placeholder) {
                placeholder.innerHTML = '<div class="error-text">Failed to load animation</div>';
            }
            player.style.display = 'block';
        }
    }

    onAllAnimationsLoaded() {
        // Performance summary
        if (window.performance && window.performance.mark) {
            window.performance.mark('lottie-defer-all-loaded');
            
            // Measure total load time
            const navigationStart = window.performance.timing.navigationStart;
            const loadTime = performance.now();
            
            console.log(`Lottie Defer Mode: All animations loaded in ${loadTime.toFixed(2)}ms`);
        }
        
        // Dispatch custom event
        document.dispatchEvent(new CustomEvent('lottieDeferredLoaded', {
            detail: {
                animationCount: this.players.length,
                loadTime: performance.now()
            }
        }));
    }

    // Method to load specific animation immediately
    loadImmediately(player) {
        if (player && player.getAttribute('data-deferred') === 'true') {
            player.removeAttribute('data-deferred');
            this.loadSingleAnimation(player, 0);
        }
    }
}

// Initialize deferred loader
window.lottieDeferredLoader = new LottieDeferredLoader();

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