/**
 * Step 2: Canvas Renderer Implementation
 * Local player with Canvas renderer for improved performance
 */

document.addEventListener('DOMContentLoaded', function() {
    console.log('Step 2: Canvas Renderer loaded');
    console.log('Custom elements available:', customElements.get('dotlottie-player'));
    
    // Wait for custom element to be defined
    if (customElements.get('dotlottie-player')) {
        console.log('dotlottie-player already defined');
        initializeCanvasPlayers();
    } else {
        console.log('Waiting for dotlottie-player to be defined...');
        customElements.whenDefined('dotlottie-player').then(() => {
            console.log('dotlottie-player is now defined');
            initializeCanvasPlayers();
        });
    }
    
    function initializeCanvasPlayers() {
        // Initialize all lottie players with canvas renderer
        const lottiePlayers = document.querySelectorAll('dotlottie-player');
        console.log('Found', lottiePlayers.length, 'dotlottie-player elements');
        
        lottiePlayers.forEach((player, index) => {
            console.log(`Initializing canvas player ${index + 1}:`, player.src);
            
            // Ensure canvas renderer is set
            if (!player.getAttribute('renderer')) {
                player.setAttribute('renderer', 'canvas');
            }
            
            // Add event listeners for debugging
            player.addEventListener('ready', () => {
                console.log(`Canvas player ${index + 1} ready`);
            });
            
            player.addEventListener('error', (e) => {
                console.error(`Canvas player ${index + 1} error:`, e);
            });
        });
    }
    
    // Performance tracking
    if ('performance' in window) {
        const perfData = performance.getEntriesByType('navigation')[0];
        console.log('Navigation timing:', perfData);
    }
});
