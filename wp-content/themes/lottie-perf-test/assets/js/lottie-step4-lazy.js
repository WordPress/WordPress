/**
 * Step 4: Lazy Loading with IntersectionObserver
 * Canvas renderer with deferred loading + lazy loading for below-fold animations
 */

document.addEventListener('DOMContentLoaded', function() {
    console.log('Step 4: Lazy Loading with IntersectionObserver loaded');
    console.log('Custom elements available:', customElements.get('lottie-player'));
    
    // Wait for custom element to be defined
    if (customElements.get('lottie-player')) {
        console.log('lottie-player already defined');
        initializeLazyPlayers();
    } else {
        console.log('Waiting for lottie-player to be defined...');
        customElements.whenDefined('lottie-player').then(() => {
            console.log('lottie-player is now defined');
            initializeLazyPlayers();
        });
    }
    
    function initializeLazyPlayers() {
        const lottiePlayers = document.querySelectorAll('lottie-player[data-lazy="true"]');
        console.log('Found', lottiePlayers.length, 'lazy lottie-player elements');
        
        if (lottiePlayers.length === 0) {
            console.log('No lazy players found');
            return;
        }
        
        // Create IntersectionObserver for lazy loading
        const observer = new IntersectionObserver((entries, observer) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    const player = entry.target;
                    console.log('Lazy loading player:', player.src);
                    
                    // Ensure canvas renderer is set
                    if (!player.getAttribute('renderer')) {
                        player.setAttribute('renderer', 'canvas');
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
            threshold: 0.1, // Trigger when 10% of the element is visible
            rootMargin: '50px' // Start loading 50px before the element comes into view
        });
        
        // Observe all lazy players
        lottiePlayers.forEach((player, index) => {
            console.log(`Setting up lazy observer for player ${index + 1}:`, player.src);
            
            // Initially pause the player
            if (player.pause) {
                player.pause();
            }
            
            // Add event listeners for debugging
            player.addEventListener('ready', () => {
                console.log(`Lazy player ${index + 1} ready`);
            });
            
            player.addEventListener('error', (e) => {
                console.error(`Lazy player ${index + 1} error:`, e);
            });
            
            // Start observing
            observer.observe(player);
        });
    }
    
    // Performance tracking
    if ('performance' in window) {
        const perfData = performance.getEntriesByType('navigation')[0];
        console.log('Navigation timing:', perfData);
    }
});
