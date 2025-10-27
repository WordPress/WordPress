/**
 * Step 6: Conditional Enqueue (Per-Page)
 * All optimizations + conditional script loading only on Lottie pages
 */

document.addEventListener('DOMContentLoaded', function() {
    console.log('Step 6: Conditional Enqueue (Per-Page) loaded');
    console.log('Custom elements available:', customElements.get('lottie-player'));
    
    // Wait for custom element to be defined
    if (customElements.get('lottie-player')) {
        console.log('lottie-player already defined');
        initializeConditionalPlayers();
    } else {
        console.log('Waiting for lottie-player to be defined...');
        customElements.whenDefined('lottie-player').then(() => {
            console.log('lottie-player is now defined');
            initializeConditionalPlayers();
        });
    }
    
    function initializeConditionalPlayers() {
        const lottiePlayers = document.querySelectorAll('lottie-player[data-lazy="true"]');
        console.log('Found', lottiePlayers.length, 'lazy lottie-player elements');
        
        if (lottiePlayers.length === 0) {
            console.log('No lazy players found on this page');
            return;
        }
        
        console.log(`Found ${lottiePlayers.length} lazy players on this page`);
        
        // Create IntersectionObserver for lazy loading with optimized settings
        const observer = new IntersectionObserver((entries, observer) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    const player = entry.target;
                    console.log('Loading conditional player:', player.src);
                    
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
            rootMargin: '100px'
        });
        
        // Observe all lazy players
        lottiePlayers.forEach((player, index) => {
            console.log(`Setting up conditional observer for player ${index + 1}:`, player.src);
            
            // Initially pause the player
            if (player.pause) {
                player.pause();
            }
            
            // Add event listeners for debugging
            player.addEventListener('ready', () => {
                console.log(`Conditional player ${index + 1} ready`);
            });
            
            player.addEventListener('error', (e) => {
                console.error(`Conditional player ${index + 1} error:`, e);
            });
            
            // Add loading event listener
            player.addEventListener('load', () => {
                console.log(`Conditional player ${index + 1} loaded from cache`);
            });
            
            // Start observing
            observer.observe(player);
        });
    }
    
    // Performance tracking with enhanced metrics
    if ('performance' in window) {
        const perfData = performance.getEntriesByType('navigation')[0];
        console.log('Navigation timing:', perfData);
        
        // Track resource loading times
        const resources = performance.getEntriesByType('resource');
        const lottieResources = resources.filter(resource => 
            resource.name.includes('.lottie') || 
            resource.name.includes('lottie-player')
        );
        
        lottieResources.forEach(resource => {
            console.log(`Resource loaded: ${resource.name} in ${resource.duration}ms`);
        });
        
        // Track script loading efficiency
        const scripts = resources.filter(resource => 
            resource.name.includes('.js')
        );
        
        console.log(`Total scripts loaded: ${scripts.length}`);
        scripts.forEach(script => {
            console.log(`Script: ${script.name} loaded in ${script.duration}ms`);
        });
    }
});
