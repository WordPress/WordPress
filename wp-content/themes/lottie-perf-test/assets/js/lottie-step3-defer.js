/**
 * Step 3: Deferred Loading Implementation
 * Canvas renderer with deferred script loading for better performance
 */

document.addEventListener('DOMContentLoaded', function() {
    console.log('Step 3: Deferred Loading loaded');
    
    // Wait for dotlottie-player to be available
    const initPlayers = () => {
        const lottiePlayers = document.querySelectorAll('lottie-player');
        
        lottiePlayers.forEach((player, index) => {
            console.log(`Initializing deferred canvas player ${index + 1}:`, player.src);
            
            // Ensure canvas renderer is set
            if (!player.getAttribute('renderer')) {
                player.setAttribute('renderer', 'canvas');
            }
            
            // Add event listeners for debugging
            player.addEventListener('ready', () => {
                console.log(`Deferred canvas player ${index + 1} ready`);
            });
            
            player.addEventListener('error', (e) => {
                console.error(`Deferred canvas player ${index + 1} error:`, e);
            });
        });
    };
    
    // Check if dotlottie-player is available, if not wait
    if (typeof customElements !== 'undefined' && customElements.get('lottie-player')) {
        initPlayers();
    } else {
        // Wait for the custom element to be defined
        const checkInterval = setInterval(() => {
            if (typeof customElements !== 'undefined' && customElements.get('lottie-player')) {
                clearInterval(checkInterval);
                initPlayers();
            }
        }, 100);
        
        // Timeout after 5 seconds
        setTimeout(() => {
            clearInterval(checkInterval);
            console.warn('dotlottie-player not loaded after 5 seconds');
        }, 5000);
    }
    
    // Performance tracking
    if ('performance' in window) {
        const perfData = performance.getEntriesByType('navigation')[0];
        console.log('Navigation timing:', perfData);
    }
});
