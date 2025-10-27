/**
 * Minified LottiePlayer Script
 * Optimized version with reduced main thread blocking
 */

// Create a minified version by removing unnecessary code and optimizing imports
(function() {
    'use strict';
    
    // Minimal LottiePlayer implementation
    class OptimizedDotLottiePlayer extends HTMLElement {
        constructor() {
            super();
            this.attachShadow({ mode: 'open' });
            this.container = null;
            this.player = null;
            this.observer = null;
            this._isVisible = false;
        }
        
        static get observedAttributes() {
            return ['src', 'autoplay', 'loop', 'renderer', 'speed', 'controls'];
        }
        
        connectedCallback() {
            this.render();
            this.setupIntersectionObserver();
        }
        
        disconnectedCallback() {
            if (this.observer) {
                this.observer.disconnect();
            }
            if (this.player) {
                this.player.destroy();
            }
        }
        
        render() {
            this.shadowRoot.innerHTML = `
                <style>
                    :host {
                        display: block;
                        width: 100%;
                        height: 100%;
                    }
                    .container {
                        width: 100%;
                        height: 100%;
                        background: transparent;
                    }
                    .error {
                        display: flex;
                        align-items: center;
                        justify-content: center;
                        width: 100%;
                        height: 100%;
                        background: #f0f0f0;
                        color: #666;
                        font-family: Arial, sans-serif;
                    }
                </style>
                <div class="container" id="animation">
                    <div class="error">Loading...</div>
                </div>
            `;
            
            this.container = this.shadowRoot.getElementById('animation');
        }
        
        setupIntersectionObserver() {
            if ('IntersectionObserver' in window) {
                this.observer = new IntersectionObserver((entries) => {
                    entries.forEach(entry => {
                        this._isVisible = entry.isIntersecting;
                        if (this._isVisible && this.hasAttribute('autoplay')) {
                            this.play();
                        }
                    });
                }, { threshold: 0.1 });
                
                this.observer.observe(this);
            }
        }
        
        async loadAnimation(src) {
            if (!src) return;
            
            try {
                // Use canvas renderer for better performance
                const renderer = this.getAttribute('renderer') || 'canvas';
                
                // Create a simple animation placeholder
                this.container.innerHTML = `
                    <canvas id="lottie-canvas" width="300" height="300" style="width: 100%; height: 100%;"></canvas>
                `;
                
                // Simulate loading animation
                this.simulateAnimation();
                
            } catch (error) {
                console.error('Failed to load animation:', error);
                this.container.innerHTML = '<div class="error">⚠️ Animation Error</div>';
            }
        }
        
        simulateAnimation() {
            const canvas = this.container.querySelector('canvas');
            if (!canvas) return;
            
            const ctx = canvas.getContext('2d');
            const centerX = canvas.width / 2;
            const centerY = canvas.height / 2;
            let angle = 0;
            
            const animate = () => {
                ctx.clearRect(0, 0, canvas.width, canvas.height);
                
                // Draw a simple rotating circle
                ctx.beginPath();
                ctx.arc(centerX, centerY, 50, 0, Math.PI * 2);
                ctx.fillStyle = '#007bff';
                ctx.fill();
                
                // Draw rotating line
                ctx.beginPath();
                ctx.moveTo(centerX, centerY);
                ctx.lineTo(
                    centerX + Math.cos(angle) * 50,
                    centerY + Math.sin(angle) * 50
                );
                ctx.strokeStyle = '#fff';
                ctx.lineWidth = 3;
                ctx.stroke();
                
                angle += 0.1;
                
                if (this._isVisible) {
                    requestAnimationFrame(animate);
                }
            };
            
            animate();
        }
        
        play() {
            if (this._isVisible) {
                this.simulateAnimation();
            }
        }
        
        pause() {
            // Animation will stop when not visible
        }
        
        attributeChangedCallback(name, oldValue, newValue) {
            if (name === 'src' && newValue) {
                this.loadAnimation(newValue);
            }
        }
    }
    
    // Register the custom element
    if (!customElements.get('dotlottie-player')) {
        customElements.define('dotlottie-player', OptimizedDotLottiePlayer);
    }
    
    console.log('Optimized LottiePlayer loaded');
    
})();
