/**
 * Step 1: Basic Local Player Implementation
 * Simple initialization of Lottie players with local dotlottie-player-correct.mjs
 */

document.addEventListener('DOMContentLoaded', function() {
    console.log('Step 1: Basic Local Player loaded');
    console.log('Custom elements available:', customElements.get('dotlottie-player'));
    
    // Wait for custom element to be defined
    if (customElements.get('dotlottie-player')) {
        console.log('dotlottie-player already defined');
        initializePlayers();
    } else {
        console.log('Waiting for dotlottie-player to be defined...');
        customElements.whenDefined('dotlottie-player').then(() => {
            console.log('dotlottie-player is now defined');
            initializePlayers();
        });
    }
    
    function initializePlayers() {
        // Initialize all dotlottie players
        const dotlottiePlayers = document.querySelectorAll('dotlottie-player');
        console.log('Found', dotlottiePlayers.length, 'dotlottie-player elements');
        
        dotlottiePlayers.forEach((player, index) => {
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
