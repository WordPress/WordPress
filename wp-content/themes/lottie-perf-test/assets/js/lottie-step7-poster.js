/**
 * Step 7: Poster Fallback for Below-Fold Animations
 * All optimizations + poster images for below-fold animations
 */

document.addEventListener('DOMContentLoaded', function() {
    console.log('Step 7: Poster Fallback for Below-Fold Animations loaded');
    console.log('Custom elements available:', customElements.get('dotlottie-player'));
    
    // Wait for custom element to be defined
    if (customElements.get('dotlottie-player')) {
        console.log('dotlottie-player already defined');
        initializePosterPlayers();
    } else {
        console.log('Waiting for dotlottie-player to be defined...');
        customElements.whenDefined('dotlottie-player').then(() => {
            console.log('dotlottie-player is now defined');
            initializePosterPlayers();
        });
    }
    
    function initializePosterPlayers() {
        const lottiePlayers = document.querySelectorAll('dotlottie-player[data-lazy="true"]');
        console.log('Found', lottiePlayers.length, 'lazy dotlottie-player elements');
        
        if (lottiePlayers.length === 0) {
            console.log('No lazy players found on this page');
            return;
        }
        
        console.log(`Found ${lottiePlayers.length} lazy players with poster support`);
        
        // Create IntersectionObserver for lazy loading with poster support
        const observer = new IntersectionObserver((entries, observer) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    const player = entry.target;
                    console.log('Loading poster player:', player.src);
                    
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
            console.log(`Setting up poster observer for player ${index + 1}:`, player.src);
            
            // Check if poster is set
            const poster = player.getAttribute('poster');
            if (poster) {
                console.log(`Player ${index + 1} has poster: ${poster}`);
            }
            
            // Initially pause the player
            if (player.pause) {
                player.pause();
            }
            
            // Add event listeners for debugging
            player.addEventListener('ready', () => {
                console.log(`Poster player ${index + 1} ready`);
            });
            
            player.addEventListener('error', (e) => {
                console.error(`Poster player ${index + 1} error:`, e);
                // Fallback to poster if animation fails to load
                const poster = player.getAttribute('poster');
                if (poster) {
                    console.log(`Falling back to poster for player ${index + 1}`);
                    // You could implement poster fallback logic here
                }
            });
            
            // Add loading event listener
            player.addEventListener('load', () => {
                console.log(`Poster player ${index + 1} loaded from cache`);
            });
            
            // Add poster load event listener
            player.addEventListener('posterload', () => {
                console.log(`Poster loaded for player ${index + 1}`);
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
            resource.name.includes('dotlottie-player')
        );
        
        lottieResources.forEach(resource => {
            console.log(`Resource loaded: ${resource.name} in ${resource.duration}ms`);
        });
        
        // Track poster image loading
        const posterResources = resources.filter(resource => 
            resource.name.includes('poster') || 
            resource.name.includes('.png') ||
            resource.name.includes('.jpg') ||
            resource.name.includes('.webp')
        );
        
        posterResources.forEach(resource => {
            console.log(`Poster loaded: ${resource.name} in ${resource.duration}ms`);
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
