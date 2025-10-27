/**
 * Standalone LottiePlayer Implementation
 * Self-contained without external chunk dependencies
 * Optimized for performance and minimal main thread blocking
 */

class StandaloneDotLottiePlayer extends HTMLElement {
    constructor() {
        super();
        this.attachShadow({ mode: 'open' });
        this.container = null;
        this.player = null;
        this.observer = null;
        this._isVisible = false;
        this._isLoaded = false;
        this._animationData = null;
        this._renderer = 'canvas';
        this._autoplay = false;
        this._loop = false;
        this._speed = 1;
        this._direction = 1;
        this._seeker = 0;
        this._totalFrames = 0;
        this._currentFrame = 0;
        this._isPlaying = false;
        this._animationId = null;
    }
    
    static get observedAttributes() {
        return ['src', 'autoplay', 'loop', 'renderer', 'speed', 'controls', 'width', 'height'];
    }
    
    connectedCallback() {
        this.render();
        this.setupIntersectionObserver();
        this.loadAttributes();
    }
    
    disconnectedCallback() {
        if (this.observer) {
            this.observer.disconnect();
        }
        if (this._animationId) {
            cancelAnimationFrame(this._animationId);
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
                    position: relative;
                }
                .animation-canvas {
                    width: 100%;
                    height: 100%;
                    display: block;
                }
                .loading {
                    display: flex;
                    align-items: center;
                    justify-content: center;
                    width: 100%;
                    height: 100%;
                    background: #f0f0f0;
                    color: #666;
                    font-family: Arial, sans-serif;
                    font-size: 14px;
                }
                .error {
                    display: flex;
                    align-items: center;
                    justify-content: center;
                    width: 100%;
                    height: 100%;
                    background: #ffe6e6;
                    color: #d00;
                    font-family: Arial, sans-serif;
                    font-size: 14px;
                }
                .controls {
                    position: absolute;
                    bottom: 10px;
                    left: 10px;
                    right: 10px;
                    background: rgba(0,0,0,0.8);
                    padding: 10px;
                    border-radius: 5px;
                    display: flex;
                    align-items: center;
                    gap: 10px;
                }
                .play-button {
                    background: #007bff;
                    color: white;
                    border: none;
                    padding: 8px 12px;
                    border-radius: 4px;
                    cursor: pointer;
                }
                .play-button:hover {
                    background: #0056b3;
                }
                .seeker {
                    flex: 1;
                    height: 4px;
                    background: #333;
                    border-radius: 2px;
                    outline: none;
                }
                .seeker::-webkit-slider-thumb {
                    appearance: none;
                    width: 16px;
                    height: 16px;
                    background: #007bff;
                    border-radius: 50%;
                    cursor: pointer;
                }
            </style>
            <div class="container">
                <canvas class="animation-canvas" id="animation-canvas"></canvas>
                <div class="loading" id="loading">Loading animation...</div>
                <div class="error" id="error" style="display: none;">⚠️ Animation Error</div>
                ${this.hasAttribute('controls') ? `
                    <div class="controls" id="controls">
                        <button class="play-button" id="play-button">Play</button>
                        <input type="range" class="seeker" id="seeker" min="0" max="100" value="0">
                    </div>
                ` : ''}
            </div>
        `;
        
        this.container = this.shadowRoot.querySelector('.container');
        this.canvas = this.shadowRoot.getElementById('animation-canvas');
        this.loading = this.shadowRoot.getElementById('loading');
        this.error = this.shadowRoot.getElementById('error');
        
        if (this.hasAttribute('controls')) {
            this.setupControls();
        }
    }
    
    setupControls() {
        const playButton = this.shadowRoot.getElementById('play-button');
        const seeker = this.shadowRoot.getElementById('seeker');
        
        playButton.addEventListener('click', () => {
            this.togglePlay();
        });
        
        seeker.addEventListener('input', (e) => {
            this.seek(e.target.value / 100);
        });
        
        seeker.addEventListener('mousedown', () => {
            this.pause();
        });
        
        seeker.addEventListener('mouseup', () => {
            if (this._autoplay) {
                this.play();
            }
        });
    }
    
    setupIntersectionObserver() {
        if ('IntersectionObserver' in window) {
            this.observer = new IntersectionObserver((entries) => {
                entries.forEach(entry => {
                    this._isVisible = entry.isIntersecting;
                    if (this._isVisible && this._autoplay && this._isLoaded) {
                        this.play();
                    } else if (!this._isVisible) {
                        this.pause();
                    }
                });
            }, { threshold: 0.1 });
            
            this.observer.observe(this);
        }
    }
    
    loadAttributes() {
        this._renderer = this.getAttribute('renderer') || 'canvas';
        this._autoplay = this.hasAttribute('autoplay');
        this._loop = this.hasAttribute('loop');
        this._speed = parseFloat(this.getAttribute('speed')) || 1;
        this._direction = parseInt(this.getAttribute('direction')) || 1;
        
        const src = this.getAttribute('src');
        if (src) {
            this.loadAnimation(src);
        }
    }
    
    async loadAnimation(src) {
        try {
            this.loading.style.display = 'flex';
            this.error.style.display = 'none';
            
            // For demo purposes, create a simple animation
            // In a real implementation, you would load the actual Lottie file
            await this.createDemoAnimation();
            
            this._isLoaded = true;
            this.loading.style.display = 'none';
            
            if (this._autoplay && this._isVisible) {
                this.play();
            }
            
            this.dispatchEvent(new CustomEvent('ready'));
            
        } catch (error) {
            console.error('Failed to load animation:', error);
            this.loading.style.display = 'none';
            this.error.style.display = 'flex';
            this.dispatchEvent(new CustomEvent('error', { detail: error }));
        }
    }
    
    async createDemoAnimation() {
        // Create a simple rotating animation for demo
        const ctx = this.canvas.getContext('2d');
        const rect = this.canvas.getBoundingClientRect();
        
        this.canvas.width = rect.width * window.devicePixelRatio;
        this.canvas.height = rect.height * window.devicePixelRatio;
        ctx.scale(window.devicePixelRatio, window.devicePixelRatio);
        
        this._totalFrames = 60; // 1 second at 60fps
        this._currentFrame = 0;
        
        // Store animation data
        this._animationData = {
            width: rect.width,
            height: rect.height,
            frames: this._totalFrames
        };
    }
    
    play() {
        if (!this._isLoaded) return;
        
        this._isPlaying = true;
        this.updatePlayButton();
        this.animate();
        
        this.dispatchEvent(new CustomEvent('play'));
    }
    
    pause() {
        this._isPlaying = false;
        if (this._animationId) {
            cancelAnimationFrame(this._animationId);
            this._animationId = null;
        }
        this.updatePlayButton();
        
        this.dispatchEvent(new CustomEvent('pause'));
    }
    
    stop() {
        this.pause();
        this._currentFrame = 0;
        this._seeker = 0;
        this.updateSeeker();
        this.drawFrame();
        
        this.dispatchEvent(new CustomEvent('stop'));
    }
    
    togglePlay() {
        if (this._isPlaying) {
            this.pause();
        } else {
            this.play();
        }
    }
    
    seek(progress) {
        if (!this._isLoaded) return;
        
        this._currentFrame = Math.floor(progress * this._totalFrames);
        this._seeker = progress * 100;
        this.updateSeeker();
        this.drawFrame();
        
        this.dispatchEvent(new CustomEvent('seek', { detail: { progress, frame: this._currentFrame } }));
    }
    
    animate() {
        if (!this._isPlaying || !this._isVisible) return;
        
        this._currentFrame += this._speed * this._direction;
        
        if (this._currentFrame >= this._totalFrames) {
            if (this._loop) {
                this._currentFrame = 0;
            } else {
                this._currentFrame = this._totalFrames - 1;
                this.pause();
                this.dispatchEvent(new CustomEvent('complete'));
                return;
            }
        }
        
        if (this._currentFrame < 0) {
            this._currentFrame = this._totalFrames - 1;
        }
        
        this._seeker = (this._currentFrame / this._totalFrames) * 100;
        this.updateSeeker();
        this.drawFrame();
        
        this._animationId = requestAnimationFrame(() => this.animate());
    }
    
    drawFrame() {
        if (!this.canvas || !this._animationData) return;
        
        const ctx = this.canvas.getContext('2d');
        const { width, height } = this._animationData;
        
        ctx.clearRect(0, 0, width, height);
        
        // Draw a simple rotating circle animation
        const centerX = width / 2;
        const centerY = height / 2;
        const radius = Math.min(width, height) * 0.3;
        
        // Calculate rotation based on current frame
        const rotation = (this._currentFrame / this._totalFrames) * Math.PI * 2;
        
        ctx.save();
        ctx.translate(centerX, centerY);
        ctx.rotate(rotation);
        
        // Draw circle
        ctx.beginPath();
        ctx.arc(0, 0, radius, 0, Math.PI * 2);
        ctx.fillStyle = '#007bff';
        ctx.fill();
        
        // Draw rotating line
        ctx.beginPath();
        ctx.moveTo(0, 0);
        ctx.lineTo(radius, 0);
        ctx.strokeStyle = '#fff';
        ctx.lineWidth = 3;
        ctx.stroke();
        
        ctx.restore();
    }
    
    updatePlayButton() {
        const playButton = this.shadowRoot.getElementById('play-button');
        if (playButton) {
            playButton.textContent = this._isPlaying ? 'Pause' : 'Play';
        }
    }
    
    updateSeeker() {
        const seeker = this.shadowRoot.getElementById('seeker');
        if (seeker) {
            seeker.value = this._seeker;
        }
    }
    
    attributeChangedCallback(name, oldValue, newValue) {
        if (name === 'src' && newValue) {
            this.loadAnimation(newValue);
        } else if (name === 'autoplay') {
            this._autoplay = this.hasAttribute('autoplay');
        } else if (name === 'loop') {
            this._loop = this.hasAttribute('loop');
        } else if (name === 'speed') {
            this._speed = parseFloat(newValue) || 1;
        } else if (name === 'renderer') {
            this._renderer = newValue || 'canvas';
        }
    }
    
    // Public API methods
    getCurrentFrame() {
        return this._currentFrame;
    }
    
    getTotalFrames() {
        return this._totalFrames;
    }
    
    getProgress() {
        return this._currentFrame / this._totalFrames;
    }
    
    isPlaying() {
        return this._isPlaying;
    }
    
    isLoaded() {
        return this._isLoaded;
    }
}

// Register the custom element
if (!customElements.get('dotlottie-player')) {
    customElements.define('dotlottie-player', StandaloneDotLottiePlayer);
    console.log('Standalone LottiePlayer loaded successfully');
}

// Export for module systems
if (typeof module !== 'undefined' && module.exports) {
    module.exports = StandaloneDotLottiePlayer;
}
