/**
 * Step 1: Basic Local Player Implementation
 * Simple initialization of Lottie players with local dotlottie-player.min.js
 */

document.addEventListener('DOMContentLoaded', function() {
    console.log('Step 1: Basic Local Player loaded');
    
    // Initialize all lottie players
    const lottiePlayers = document.querySelectorAll('lottie-player');
    
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
    
    // Performance tracking
    if ('performance' in window) {
        const perfData = performance.getEntriesByType('navigation')[0];
        console.log('Navigation timing:', perfData);
    }
});
