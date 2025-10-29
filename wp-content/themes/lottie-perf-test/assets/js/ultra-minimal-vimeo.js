/**
 * Ultra-Minimal Vimeo Embed - No external scripts
 * Just shows poster image with click to load
 */
class UltraMinimalVimeo extends HTMLElement {
  constructor() {
    super();
    this.videoId = this.getAttribute('videoid');
    this.poster = this.getAttribute('poster') || `https://vumbnail.com/${this.videoId}.jpg`;
    this.aspectRatio = this.getAttribute('aspect-ratio') || '16/9';
    this.loaded = false;
    
    this.init();
  }
  
  static get observedAttributes() {
    return ['videoid', 'poster', 'aspect-ratio'];
  }
  
  attributeChangedCallback(name, oldValue, newValue) {
    if (oldValue !== newValue && this.isConnected) {
      this.init();
    }
  }
  
  init() {
    if (!this.videoId) return;
    
    this.innerHTML = `
      <div class="vimeo-container" style="
        position: relative;
        width: 100%;
        aspect-ratio: ${this.aspectRatio};
        background: #000;
        border-radius: 8px;
        overflow: hidden;
        cursor: pointer;
      ">
        <img src="${this.poster}" 
             alt="Video thumbnail" 
             style="
               width: 100%;
               height: 100%;
               object-fit: cover;
               display: block;
             "
             onerror="this.style.display='none'">
        <div class="play-button" style="
          position: absolute;
          top: 50%;
          left: 50%;
          transform: translate(-50%, -50%);
          width: 80px;
          height: 80px;
          background: rgba(0,0,0,0.8);
          border-radius: 50%;
          display: flex;
          align-items: center;
          justify-content: center;
          transition: all 0.3s ease;
        ">
          <svg width="32" height="32" viewBox="0 0 24 24" fill="white">
            <path d="M8 5v14l11-7z"/>
          </svg>
        </div>
      </div>
    `;
    
    this.addEventListener('click', () => this.loadVideo());
  }
  
  loadVideo() {
    if (this.loaded) return;
    
    this.loaded = true;
    this.innerHTML = `
      <iframe 
        src="https://player.vimeo.com/video/${this.videoId}?autoplay=1&muted=1&loop=1&controls=0"
        style="
          position: absolute;
          top: 0;
          left: 0;
          width: 100%;
          height: 100%;
          border: none;
        "
        allow="autoplay; fullscreen"
        allowfullscreen>
      </iframe>
    `;
  }
}

// Register the custom element
if (!customElements.get('lite-vimeo')) {
  customElements.define('lite-vimeo', UltraMinimalVimeo);
}
