/**
 * Step 3: Deferred Loading Implementation
 * Canvas renderer with deferred script loading for better performance
 */

document.addEventListener('DOMContentLoaded', function() {
    console.log('Step 3: Deferred Loading loaded');
    console.log('Custom elements available:', customElements.get('lottie-player'));
    
    // Wait for custom element to be defined
    if (customElements.get('lottie-player')) {
        console.log('lottie-player already defined');
        initializeDeferredPlayers();
    } else {
        console.log('Waiting for lottie-player to be defined...');
        customElements.whenDefined('lottie-player').then(() => {
            console.log('lottie-player is now defined');
            initializeDeferredPlayers();
        });
    }
    
    function initializeDeferredPlayers() {
        const lottiePlayers = document.querySelectorAll('lottie-player');
        console.log('Found', lottiePlayers.length, 'lottie-player elements');
        
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
    }
    
    // Performance tracking
    if ('performance' in window) {
        const perfData = performance.getEntriesByType('navigation')[0];
        console.log('Navigation timing:', perfData);
    }
});
