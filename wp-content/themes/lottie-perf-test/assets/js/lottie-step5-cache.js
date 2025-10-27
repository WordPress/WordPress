/**
 * Step 5: Compression & Caching Optimization
 * Lazy loading + Canvas + Asset compression & caching headers
 */

document.addEventListener('DOMContentLoaded', function() {
    console.log('Step 5: Compression & Caching Optimization loaded');
    
    // Wait for dotlottie-player to be available
    const initOptimizedPlayers = () => {
        const lottiePlayers = document.querySelectorAll('lottie-player[data-lazy="true"]');
        
        if (lottiePlayers.length === 0) {
            console.log('No lazy players found');
            return;
        }
        
        // Create IntersectionObserver for lazy loading with optimized settings
        const observer = new IntersectionObserver((entries, observer) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    const player = entry.target;
                    console.log('Loading optimized player:', player.src);
                    
                    // Ensure canvas renderer is set
                    if (!player.getAttribute('renderer')) {
                        player.setAttribute('renderer', 'canvas');
                    }
                    
                    // Preload the animation data
                    if (player.preload) {
                        player.preload();
                    }
                    
                    // Start playing the animation
                    if (player.play) {
                        player.play();
                    }
                    
                    // Stop observing this element
                    observer.unobserve(player);
                }
            });
        }, {
            threshold: 0.1,
            rootMargin: '100px' // Increased margin for better perceived performance
        });
        
        // Observe all lazy players
        lottiePlayers.forEach((player, index) => {
            console.log(`Setting up optimized observer for player ${index + 1}:`, player.src);
            
            // Initially pause the player
            if (player.pause) {
                player.pause();
            }
            
            // Add event listeners for debugging
            player.addEventListener('ready', () => {
                console.log(`Optimized player ${index + 1} ready`);
            });
            
            player.addEventListener('error', (e) => {
                console.error(`Optimized player ${index + 1} error:`, e);
            });
            
            // Add loading event listener
            player.addEventListener('load', () => {
                console.log(`Optimized player ${index + 1} loaded from cache`);
            });
            
            // Start observing
            observer.observe(player);
        });
    };
    
    // Check if dotlottie-player is available, if not wait
    if (typeof customElements !== 'undefined' && customElements.get('lottie-player')) {
        initOptimizedPlayers();
    } else {
        // Wait for the custom element to be defined
        const checkInterval = setInterval(() => {
            if (typeof customElements !== 'undefined' && customElements.get('lottie-player')) {
                clearInterval(checkInterval);
                initOptimizedPlayers();
            }
        }, 100);
        
        // Timeout after 5 seconds
        setTimeout(() => {
            clearInterval(checkInterval);
            console.warn('dotlottie-player not loaded after 5 seconds');
        }, 5000);
    }
    
    // Performance tracking with enhanced metrics
    if ('performance' in window) {
        const perfData = performance.getEntriesByType('navigation')[0];
        console.log('Navigation timing:', perfData);
        
        // Track resource loading times
        const resources = performance.getEntriesByType('resource');
        const lottieResources = resources.filter(resource => 
            resource.name.includes('.lottie') || 
            resource.name.includes('dotlottie-player')
        );
        
        lottieResources.forEach(resource => {
            console.log(`Resource loaded: ${resource.name} in ${resource.duration}ms`);
        });
    }
});
