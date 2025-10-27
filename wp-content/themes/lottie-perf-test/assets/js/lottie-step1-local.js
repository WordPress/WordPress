/**
 * Step 1: Basic Local Player Implementation
 * Simple initialization of Lottie players with local dotlottie-player.min.js
 */

document.addEventListener('DOMContentLoaded', function() {
    console.log('Step 1: Basic Local Player loaded');
    console.log('Custom elements available:', customElements.get('lottie-player'));
    
    // Wait for custom element to be defined
    if (customElements.get('lottie-player')) {
        console.log('lottie-player already defined');
        initializePlayers();
    } else {
        console.log('Waiting for lottie-player to be defined...');
        customElements.whenDefined('lottie-player').then(() => {
            console.log('lottie-player is now defined');
            initializePlayers();
        });
    }
    
    function initializePlayers() {
        // Initialize all lottie players
        const lottiePlayers = document.querySelectorAll('lottie-player');
        console.log('Found', lottiePlayers.length, 'lottie-player elements');
        
        lottiePlayers.forEach((player, index) => {
            console.log(`Initializing player ${index + 1}:`, player.src);
            
            // Add event listeners for debugging
            player.addEventListener('ready', () => {
                console.log(`Player ${index + 1} ready`);
            });
            
            player.addEventListener('error', (e) => {
                console.error(`Player ${index + 1} error:`, e);
            });
        });
    }
    
    // Performance tracking
    if ('performance' in window) {
        const perfData = performance.getEntriesByType('navigation')[0];
        console.log('Navigation timing:', perfData);
    }
});
