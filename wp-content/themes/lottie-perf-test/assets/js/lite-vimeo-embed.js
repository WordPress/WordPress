/**
 * Lite Vimeo Embed - Lightweight Vimeo video embed with lazy loading
 * Optimized for performance and CLS prevention
 */
class LiteVimeoEmbed extends HTMLElement {
  constructor() {
    super();
    this.videoId = this.getAttribute('videoid');
    this.poster = this.getAttribute('poster') || '';
    this.aspectRatio = this.getAttribute('aspect-ratio') || '16/9';
    this.autoplay = this.hasAttribute('autoplay');
    this.muted = this.hasAttribute('muted');
    this.loop = this.hasAttribute('loop');
    this.controls = this.hasAttribute('controls');
    
    this.observer = null;
    this.loaded = false;
    
    this.init();
  }
  
  init() {
    // Set up the container with proper aspect ratio
    this.style.aspectRatio = this.aspectRatio;
    this.style.position = 'relative';
    this.style.overflow = 'hidden';
    this.style.backgroundColor = '#000';
    this.style.borderRadius = '8px';
    
    // Create poster image
    this.createPoster();
    
    // Set up intersection observer for lazy loading
    this.setupIntersectionObserver();
  }
  
  createPoster() {
    const posterUrl = this.poster || `https://vumbnail.com/${this.videoId}.jpg`;
    
    this.innerHTML = `
      <div class="lite-vimeo-poster" style="
        position: absolute;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background-image: url('${posterUrl}');
        background-size: cover;
        background-position: center;
        cursor: pointer;
        display: flex;
        align-items: center;
        justify-content: center;
        transition: opacity 0.3s ease;
      ">
        <div class="play-button" style="
          width: 80px;
          height: 80px;
          background: rgba(0, 0, 0, 0.8);
          border-radius: 50%;
          display: flex;
          align-items: center;
          justify-content: center;
          transition: transform 0.2s ease;
        ">
          <svg width="32" height="32" viewBox="0 0 24 24" fill="white">
            <path d="M8 5v14l11-7z"/>
          </svg>
        </div>
      </div>
    `;
    
    // Add hover effect
    const playButton = this.querySelector('.play-button');
    this.addEventListener('mouseenter', () => {
      playButton.style.transform = 'scale(1.1)';
    });
    this.addEventListener('mouseleave', () => {
      playButton.style.transform = 'scale(1)';
    });
    
    // Add click handler
    this.addEventListener('click', () => this.loadVideo());
  }
  
  setupIntersectionObserver() {
    if ('IntersectionObserver' in window) {
      this.observer = new IntersectionObserver((entries) => {
        entries.forEach(entry => {
          if (entry.isIntersecting && !this.loaded) {
            this.loadVideo();
            this.observer.unobserve(this);
          }
        });
      }, {
        rootMargin: '50px 0px'
      });
      
      this.observer.observe(this);
    } else {
      // Fallback for older browsers
      this.loadVideo();
    }
  }
  
  loadVideo() {
    if (this.loaded) return;
    this.loaded = true;
    
    // Build Vimeo URL with parameters
    let vimeoUrl = `https://player.vimeo.com/video/${this.videoId}`;
    const params = [];
    
    if (this.autoplay) params.push('autoplay=1');
    if (this.muted) params.push('muted=1');
    if (this.loop) params.push('loop=1');
    if (!this.controls) params.push('controls=0');
    params.push('byline=0');
    params.push('title=0');
    
    if (params.length > 0) {
      vimeoUrl += '?' + params.join('&');
    }
    
    // Create iframe
    const iframe = document.createElement('iframe');
    iframe.src = vimeoUrl;
    iframe.style.width = '100%';
    iframe.style.height = '100%';
    iframe.style.position = 'absolute';
    iframe.style.top = '0';
    iframe.style.left = '0';
    iframe.style.border = 'none';
    iframe.allow = 'autoplay; fullscreen';
    iframe.allowFullscreen = true;
    iframe.loading = 'lazy';
    
    // Replace poster with iframe
    this.innerHTML = '';
    this.appendChild(iframe);
    
    // Add loaded class for styling
    this.classList.add('loaded');
  }
  
  disconnectedCallback() {
    if (this.observer) {
      this.observer.disconnect();
    }
  }
}

// Register the custom element
if (!customElements.get('lite-vimeo')) {
  customElements.define('lite-vimeo', LiteVimeoEmbed);
}
