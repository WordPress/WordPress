import { b, c as c$1, a as a$3 } from './chunk-ODPU3M3Z.mjs';
import { g, c, a as a$2, e, j, i, f } from './chunk-TRZ6EGBZ.mjs';
export { g as PlayMode } from './chunk-TRZ6EGBZ.mjs';
import './chunk-HDDX7F4A.mjs';
import { a as a$1 } from './chunk-ZWH2ESXT.mjs';

var M=(r,t)=>t.kind==="method"&&t.descriptor&&!("value"in t.descriptor)?{...t,finisher(e){e.createProperty(t.key,r);}}:{kind:"field",key:Symbol(),placement:"own",descriptor:{},originalKey:t.key,initializer(){typeof t.initializer=="function"&&(this[t.key]=t.initializer.call(this));},finisher(e){e.createProperty(t.key,r);}},x=(r,t,e)=>{t.constructor.createProperty(e,r);};function p(r){return (t,e)=>e!==void 0?x(r,t,e):M(r,t)}function k(r){return p({...r,state:!0})}var v=({finisher:r,descriptor:t})=>(e,i)=>{var n;if(i===void 0){let l=(n=e.originalKey)!==null&&n!==void 0?n:e.key,h=t!=null?{kind:"method",placement:"prototype",key:l,descriptor:t(e.key)}:{...e,key:l};return r!=null&&(h.finisher=function(b){r(b,l);}),h}{let l=e.constructor;t!==void 0&&Object.defineProperty(e,i,t(i)),r==null||r(l,i);}};function O(r,t){return v({descriptor:e=>{let i={get(){var n,l;return (l=(n=this.renderRoot)===null||n===void 0?void 0:n.querySelector(r))!==null&&l!==void 0?l:null},enumerable:!0,configurable:!0};if(t){let n=typeof e=="symbol"?Symbol():"__"+e;i.get=function(){var l,h;return this[n]===void 0&&(this[n]=(h=(l=this.renderRoot)===null||l===void 0?void 0:l.querySelector(r))!==null&&h!==void 0?h:null),this[n]};}return i}})}var L;((L=window.HTMLSlotElement)===null||L===void 0?void 0:L.prototype.assignedElements)!=null?(r,t)=>r.assignedElements(t):(r,t)=>r.assignedNodes(t).filter(e=>e.nodeType===Node.ELEMENT_NODE);var I={name:"@dotlottie/player-component",version:"2.7.12",description:"dotLottie animation player web component.",repository:"https://github.com/dotlottie/player-component.git",homepage:"https://dotlottie.io/players",bugs:"https://github.com/dotlottie/player-component/issues",author:"Jawish Hameed <jawish@lottiefiles.com>",license:"MIT",main:"dist/dotlottie-player.js",module:"dist/dotlottie-player.mjs",types:"dist/dotlottie-player.d.ts",files:["dist"],keywords:["dotlottie","animation","web component","component","lit-element","player"],scripts:{build:"tsup","cypress:open":"cypress open --component",dev:"tsup --watch",lint:"eslint .","lint:fix":"eslint --fix",test:"cypress run --component","type-check":"tsc --noEmit"},dependencies:{"@dotlottie/common":"workspace:*",lit:"^2.7.5"},devDependencies:{"@vitejs/plugin-legacy":"^4.1.0","axe-core":"^4.7.2",cypress:"^12.11.0","cypress-axe":"^1.4.0","cypress-ct-lit":"^0.3.2","lottie-web":"^5.12.2",terser:"^5.19.0",tsup:"^7.2.0",typescript:"^4.7.4",vite:"^4.3.9"},publishConfig:{access:"public"},browserslist:["> 3%"]};var T="dotlottie-player";var a=class extends b{defaultTheme="";container;playMode=g.Normal;autoplay=!1;background="transparent";controls=!1;direction=1;hover=!1;loop;renderer="svg";speed=1;src;intermission=0;activeAnimationId=null;light=!1;worker=!1;activeStateId;_seeker=0;_dotLottieCommonPlayer;_io;_loop;_renderer="svg";_unsubscribeListeners;_hasMultipleAnimations=!1;_hasMultipleThemes=!1;_hasMultipleStates=!1;_popoverIsOpen=!1;_animationsTabIsOpen=!1;_statesTabIsOpen=!1;_styleTabIsOpen=!1;_themesForCurrentAnimation=[];_statesForCurrentAnimation=[];_parseLoop(t){let e=parseInt(t,10);return Number.isInteger(e)&&e>0?(this._loop=e,e):typeof t=="string"&&["true","false"].includes(t)?(this._loop=t==="true",this._loop):(c("loop must be a positive integer or a boolean"),!1)}_handleSeekChange(t){let e=t.currentTarget;try{let i=parseInt(e.value,10);if(!this._dotLottieCommonPlayer)return;let n=i/100*this._dotLottieCommonPlayer.totalFrames;this.seek(n);}catch{throw a$2("Error while seeking animation")}}_initListeners(){let t=this._dotLottieCommonPlayer;if(t===void 0){c("player not initialized - cannot add event listeners","dotlottie-player-component");return}this._unsubscribeListeners=t.state.subscribe((e$1,i)=>{this._seeker=e$1.seeker,this.requestUpdate(),i.currentState!==e$1.currentState&&this.dispatchEvent(new CustomEvent(e$1.currentState)),this.dispatchEvent(new CustomEvent(e.Frame,{detail:{frame:e$1.frame,seeker:e$1.seeker}})),this.dispatchEvent(new CustomEvent(e.VisibilityChange,{detail:{visibilityPercentage:e$1.visibilityPercentage}}));}),t.addEventListener("complete",()=>{this.dispatchEvent(new CustomEvent(e.Complete));}),t.addEventListener("loopComplete",()=>{this.dispatchEvent(new CustomEvent(e.LoopComplete));}),t.addEventListener("DOMLoaded",()=>{let e$1=this.getManifest();e$1&&e$1.themes&&(this._themesForCurrentAnimation=e$1.themes.filter(i=>i.animations.includes(this.getCurrentAnimationId()||""))),e$1&&e$1.states&&(this._hasMultipleStates=e$1.states.length>0,this._statesForCurrentAnimation=[],e$1.states.forEach(i=>{this._statesForCurrentAnimation.push(i);})),this.dispatchEvent(new CustomEvent(e.Ready));}),t.addEventListener("data_ready",()=>{this.dispatchEvent(new CustomEvent(e.DataReady));}),t.addEventListener("data_failed",()=>{this.dispatchEvent(new CustomEvent(e.DataFail));}),window&&window.addEventListener("click",e=>this._clickOutListener(e));}async load(t,e,i){if(!this.shadowRoot)return;this._dotLottieCommonPlayer&&this._dotLottieCommonPlayer.destroy(),this._dotLottieCommonPlayer=new j(t,this.container,{rendererSettings:e!=null?e:{scaleMode:"noScale",clearCanvas:!0,progressiveLoad:!0,hideOnTransparent:!0},hover:this.hasAttribute("hover")?this.hover:void 0,renderer:this.hasAttribute("renderer")?this._renderer:void 0,loop:this.hasAttribute("loop")?this._loop:void 0,direction:this.hasAttribute("direction")?this.direction===1?1:-1:void 0,speed:this.hasAttribute("speed")?this.speed:void 0,intermission:this.hasAttribute("intermission")?Number(this.intermission):void 0,playMode:this.hasAttribute("playMode")?this.playMode:void 0,autoplay:this.hasAttribute("autoplay")?this.autoplay:void 0,activeAnimationId:this.hasAttribute("activeAnimationId")?this.activeAnimationId:void 0,defaultTheme:this.hasAttribute("defaultTheme")?this.defaultTheme:void 0,light:this.light,worker:this.worker,activeStateId:this.hasAttribute("activeStateId")?this.activeStateId:void 0}),await this._dotLottieCommonPlayer.load(i);let n=this.getManifest();this._hasMultipleAnimations=this.animationCount()>1,n&&(n.themes&&(this._themesForCurrentAnimation=n.themes.filter(l=>l.animations.includes(this.getCurrentAnimationId()||"")),this._hasMultipleThemes=n.themes.length>0),n.states&&(this._hasMultipleStates=n.states.length>0,this._statesForCurrentAnimation=[],n.states.forEach(l=>{this._statesForCurrentAnimation.push(l);}))),this._initListeners();}getCurrentAnimationId(){var t;return (t=this._dotLottieCommonPlayer)==null?void 0:t.currentAnimationId}animationCount(){var t;return this._dotLottieCommonPlayer&&((t=this._dotLottieCommonPlayer.getManifest())==null?void 0:t.animations.length)||0}animations(){if(!this._dotLottieCommonPlayer)return [];let t=this._dotLottieCommonPlayer.getManifest();return (t==null?void 0:t.animations.map(e=>e.id))||[]}currentAnimation(){return !this._dotLottieCommonPlayer||!this._dotLottieCommonPlayer.currentAnimationId?"":this._dotLottieCommonPlayer.currentAnimationId}getState(){return this._dotLottieCommonPlayer?this._dotLottieCommonPlayer.getState():i}getManifest(){var t;return (t=this._dotLottieCommonPlayer)==null?void 0:t.getManifest()}getLottie(){var t;return (t=this._dotLottieCommonPlayer)==null?void 0:t.getAnimationInstance()}getVersions(){return {lottieWebVersion:j.getLottieWebVersion(),dotLottiePlayerVersion:`${I.version}`}}previous(t){var e;(e=this._dotLottieCommonPlayer)==null||e.previous(t);}next(t){var e;(e=this._dotLottieCommonPlayer)==null||e.next(t);}reset(){var t;(t=this._dotLottieCommonPlayer)==null||t.reset();}play(t,e){this._dotLottieCommonPlayer&&this._dotLottieCommonPlayer.play(t,e);}pause(){this._dotLottieCommonPlayer&&this._dotLottieCommonPlayer.pause();}stop(){this._dotLottieCommonPlayer&&this._dotLottieCommonPlayer.stop();}playOnShow(t){this._dotLottieCommonPlayer&&this._dotLottieCommonPlayer.playOnShow(t);}stopPlayOnShow(){this._dotLottieCommonPlayer&&this._dotLottieCommonPlayer.stopPlayOnShow();}playOnScroll(t){this._dotLottieCommonPlayer&&this._dotLottieCommonPlayer.playOnScroll(t);}stopPlayOnScroll(){this._dotLottieCommonPlayer&&this._dotLottieCommonPlayer.stopPlayOnScroll();}seek(t){this._dotLottieCommonPlayer&&this._dotLottieCommonPlayer.seek(t);}snapshot(t=!0){if(!this.shadowRoot)return "";let e=this.shadowRoot.querySelector(".animation svg"),i=new XMLSerializer().serializeToString(e);if(t){let n=document.createElement("a");n.href=`data:image/svg+xml;charset=utf-8,${encodeURIComponent(i)}`,n.download=`download_${this._seeker}.svg`,document.body.appendChild(n),n.click(),document.body.removeChild(n);}return i}setTheme(t){var e;(e=this._dotLottieCommonPlayer)==null||e.setDefaultTheme(t);}themes(){var e;if(!this._dotLottieCommonPlayer)return [];let t=this._dotLottieCommonPlayer.getManifest();return ((e=t==null?void 0:t.themes)==null?void 0:e.map(i=>i.id))||[]}getDefaultTheme(){return this._dotLottieCommonPlayer?this._dotLottieCommonPlayer.defaultTheme:""}getActiveStateMachine(){return this._dotLottieCommonPlayer?this._dotLottieCommonPlayer.activeStateId:""}_freeze(){this._dotLottieCommonPlayer&&this._dotLottieCommonPlayer.freeze();}setSpeed(t=1){this._dotLottieCommonPlayer&&this._dotLottieCommonPlayer.setSpeed(t);}setDirection(t){this._dotLottieCommonPlayer&&this._dotLottieCommonPlayer.setDirection(t);}setLooping(t){this._dotLottieCommonPlayer&&this._dotLottieCommonPlayer.setLoop(t);}isLooping(){return this._dotLottieCommonPlayer?this._dotLottieCommonPlayer.loop:!1}togglePlay(){this._dotLottieCommonPlayer&&this._dotLottieCommonPlayer.togglePlay();}toggleLooping(){this._dotLottieCommonPlayer&&this._dotLottieCommonPlayer.toggleLoop();}setPlayMode(t){this._dotLottieCommonPlayer&&this._dotLottieCommonPlayer.setMode(t);}enterInteractiveMode(t){this._dotLottieCommonPlayer&&this._dotLottieCommonPlayer.enterInteractiveMode(t);}exitInteractiveMode(){this._dotLottieCommonPlayer&&this._dotLottieCommonPlayer.exitInteractiveMode();}revertToManifestValues(t){var e;(e=this._dotLottieCommonPlayer)==null||e.revertToManifestValues(t);}static get styles(){return c$1}async firstUpdated(){var t;this.container=(t=this.shadowRoot)==null?void 0:t.querySelector("#animation"),"IntersectionObserver"in window&&(this._io=new IntersectionObserver(e=>{var i,n;e[0]!==void 0&&e[0].isIntersecting?((i=this._dotLottieCommonPlayer)==null?void 0:i.currentState)===f.Frozen&&this.play():((n=this._dotLottieCommonPlayer)==null?void 0:n.currentState)===f.Playing&&this._freeze();}),this._io.observe(this.container)),this.loop?this._parseLoop(this.loop):this.hasAttribute("loop")&&this._parseLoop("true"),this.renderer==="svg"?this._renderer="svg":this.renderer==="canvas"?this._renderer="canvas":this.renderer==="html"&&(this._renderer="html"),this.src&&await this.load(this.src);}disconnectedCallback(){var t,e;this._io&&(this._io.disconnect(),this._io=void 0),(t=this._dotLottieCommonPlayer)==null||t.destroy(),(e=this._unsubscribeListeners)==null||e.call(this),window&&window.removeEventListener("click",i=>this._clickOutListener(i));}_clickOutListener(t){!t.composedPath().some(i=>i instanceof HTMLElement?i.classList.contains("popover")||i.id==="lottie-animation-options":!1)&&this._popoverIsOpen&&(this._popoverIsOpen=!1,this.requestUpdate());}renderControls(){var i,n,l,h,b;let t=((i=this._dotLottieCommonPlayer)==null?void 0:i.currentState)===f.Playing,e=((n=this._dotLottieCommonPlayer)==null?void 0:n.currentState)===f.Paused;return a$3`
      <div id="lottie-controls" aria-label="lottie-animation-controls" class="toolbar">
        ${this._hasMultipleAnimations?a$3`
              <button @click=${()=>this.previous()} aria-label="Previous animation" class="btn-spacing-left">
                <svg width="16" height="16" viewBox="0 0 16 16" fill="none" xmlns="http://www.w3.org/2000/svg">
                  <path
                    fill-rule="evenodd"
                    clip-rule="evenodd"
                    d="M1.69214 13.5C1.69214 13.7761 1.916 14 2.19214 14C2.46828 14 2.69214 13.7761 2.69214 13.5L2.69214 2.5C2.69214 2.22386 2.46828 2 2.19214 2C1.916 2 1.69214 2.22386 1.69214 2.5V13.5ZM12.5192 13.7828C13.1859 14.174 14.0254 13.6933 14.0254 12.9204L14.0254 3.0799C14.0254 2.30692 13.1859 1.8262 12.5192 2.21747L4.13612 7.13769C3.47769 7.52414 3.47769 8.4761 4.13612 8.86255L12.5192 13.7828Z"
                    fill="#20272C"
                  />
                </svg>
              </button>
            `:a$3``}
        <button
          id="lottie-play-button"
          @click=${()=>{this.togglePlay();}}
          class=${t||e?`active ${this._hasMultipleAnimations?"btn-spacing-center":"btn-spacing-right"}`:`${this._hasMultipleAnimations?"btn-spacing-center":"btn-spacing-right"}`}
          aria-label="play / pause animation"
        >
          ${t?a$3`
                <svg width="16" height="16" viewBox="0 0 16 16" fill="none" xmlns="http://www.w3.org/2000/svg">
                  <path
                    d="M3.99996 2C3.26358 2 2.66663 2.59695 2.66663 3.33333V12.6667C2.66663 13.403 3.26358 14 3.99996 14H5.33329C6.06967 14 6.66663 13.403 6.66663 12.6667V3.33333C6.66663 2.59695 6.06967 2 5.33329 2H3.99996Z"
                    fill="#20272C"
                  />
                  <path
                    d="M10.6666 2C9.93025 2 9.33329 2.59695 9.33329 3.33333V12.6667C9.33329 13.403 9.93025 14 10.6666 14H12C12.7363 14 13.3333 13.403 13.3333 12.6667V3.33333C13.3333 2.59695 12.7363 2 12 2H10.6666Z"
                    fill="#20272C"
                  />
                </svg>
              `:a$3`
                <svg width="16" height="16" viewBox="0 0 16 16" fill="none" xmlns="http://www.w3.org/2000/svg">
                  <path
                    d="M3.33337 3.46787C3.33337 2.52312 4.35948 1.93558 5.17426 2.41379L12.8961 6.94592C13.7009 7.41824 13.7009 8.58176 12.8961 9.05408L5.17426 13.5862C4.35948 14.0644 3.33337 13.4769 3.33337 12.5321V3.46787Z"
                    fill="#20272C"
                  />
                </svg>
              `}
        </button>
        ${this._hasMultipleAnimations?a$3`
              <button @click=${()=>this.next()} aria-label="Next animation" class="btn-spacing-right">
                <svg width="16" height="16" viewBox="0 0 16 16" fill="none" xmlns="http://www.w3.org/2000/svg">
                  <path
                    fill-rule="evenodd"
                    clip-rule="evenodd"
                    d="M14.3336 2.5C14.3336 2.22386 14.1097 2 13.8336 2C13.5574 2 13.3336 2.22386 13.3336 2.5V13.5C13.3336 13.7761 13.5574 14 13.8336 14C14.1097 14 14.3336 13.7761 14.3336 13.5V2.5ZM3.50618 2.21722C2.83954 1.82595 2 2.30667 2 3.07965V12.9201C2 13.6931 2.83954 14.1738 3.50618 13.7825L11.8893 8.86231C12.5477 8.47586 12.5477 7.52389 11.8893 7.13745L3.50618 2.21722Z"
                    fill="#20272C"
                  />
                </svg>
              </button>
            `:a$3``}
        <input
          id="lottie-seeker-input"
          class="seeker ${((l=this._dotLottieCommonPlayer)==null?void 0:l.direction)===-1?"to-left":""}"
          type="range"
          min="0"
          step="1"
          max="100"
          .value=${this._seeker}
          @input=${s=>this._handleSeekChange(s)}
          @mousedown=${()=>{this._freeze();}}
          @mouseup=${()=>{var s;(s=this._dotLottieCommonPlayer)==null||s.unfreeze();}}
          aria-valuemin="1"
          aria-valuemax="100"
          role="slider"
          aria-valuenow=${this._seeker}
          aria-label="lottie-seek-input"
          style=${`--seeker: ${this._seeker}`}
        />
        <button
          id="lottie-loop-toggle"
          @click=${()=>this.toggleLooping()}
          class=${(h=this._dotLottieCommonPlayer)!=null&&h.loop?"active btn-spacing-left":"btn-spacing-left"}
          aria-label="loop-toggle"
        >
          <svg width="16" height="16" viewBox="0 0 16 16" fill="none" xmlns="http://www.w3.org/2000/svg">
            <path
              d="M10.8654 2.31319C11.0607 2.11793 11.3772 2.11793 11.5725 2.31319L13.4581 4.19881C13.6534 4.39407 13.6534 4.71066 13.4581 4.90592L11.5725 6.79154C11.3772 6.9868 11.0607 6.9868 10.8654 6.79154C10.6701 6.59628 10.6701 6.27969 10.8654 6.08443L11.6162 5.33362H4V6.66695C4 7.03514 3.70152 7.33362 3.33333 7.33362C2.96514 7.33362 2.66666 7.03514 2.66666 6.66695L2.66666 4.66695C2.66666 4.29876 2.96514 4.00028 3.33333 4.00028H11.8454L10.8654 3.0203C10.6701 2.82504 10.6701 2.50846 10.8654 2.31319Z"
              fill="currentColor"
            />
            <path
              d="M12.4375 11.9999C12.8057 11.9999 13.1042 11.7014 13.1042 11.3332V9.33321C13.1042 8.96502 12.8057 8.66655 12.4375 8.66655C12.0693 8.66655 11.7708 8.96502 11.7708 9.33321V10.6665H4.15462L4.90543 9.91573C5.10069 9.72047 5.10069 9.40389 4.90543 9.20862C4.71017 9.01336 4.39359 9.01336 4.19832 9.20862L2.31271 11.0942C2.11744 11.2895 2.11744 11.6061 2.31271 11.8013L4.19832 13.687C4.39359 13.8822 4.71017 13.8822 4.90543 13.687C5.10069 13.4917 5.10069 13.1751 4.90543 12.9799L3.92545 11.9999H12.4375Z"
              fill="currentColor"
            />
          </svg>
        </button>
        ${this._hasMultipleAnimations||this._hasMultipleThemes||this._hasMultipleStates?a$3`
              <button
                id="lottie-animation-options"
                @click=${()=>{this._popoverIsOpen=!this._popoverIsOpen,this.requestUpdate();}}
                aria-label="options"
                class="btn-spacing-right"
                style=${`background-color: ${this._popoverIsOpen?"var(--lottie-player-toolbar-icon-hover-color)":""}`}
              >
                <svg width="16" height="16" viewBox="0 0 16 16" fill="none" xmlns="http://www.w3.org/2000/svg">
                  <path
                    d="M8.33337 11.6666C7.78109 11.6666 7.33337 12.1143 7.33337 12.6666C7.33337 13.2189 7.78109 13.6666 8.33337 13.6666C8.88566 13.6666 9.33337 13.2189 9.33337 12.6666C9.33337 12.1143 8.88566 11.6666 8.33337 11.6666Z"
                    fill="#20272C"
                  />
                  <path
                    d="M7.33337 7.99992C7.33337 7.44763 7.78109 6.99992 8.33337 6.99992C8.88566 6.99992 9.33338 7.44763 9.33338 7.99992C9.33338 8.5522 8.88566 8.99992 8.33337 8.99992C7.78109 8.99992 7.33337 8.5522 7.33337 7.99992Z"
                    fill="#20272C"
                  />
                  <path
                    d="M7.33337 3.33325C7.33337 2.78097 7.78109 2.33325 8.33337 2.33325C8.88566 2.33325 9.33338 2.78097 9.33338 3.33325C9.33338 3.88554 8.88566 4.33325 8.33337 4.33325C7.78109 4.33325 7.33337 3.88554 7.33337 3.33325Z"
                    fill="#20272C"
                  />
                </svg>
              </button>
            `:a$3``}
      </div>
      ${this._popoverIsOpen?a$3`
            <div
              id="popover"
              class="popover"
              tabindex="0"
              aria-label="lottie animations themes popover"
              style="min-height: ${this.themes().length>0?"84px":"auto"}"
            >
              ${!this._animationsTabIsOpen&&!this._styleTabIsOpen&&!this._statesTabIsOpen?a$3`
                    <button
                      class="popover-button"
                      tabindex="0"
                      aria-label="animations"
                      @click=${()=>{this._animationsTabIsOpen=!this._animationsTabIsOpen,this.requestUpdate();}}
                      @keydown=${s=>{(s.code==="Space"||s.code==="Enter")&&(this._animationsTabIsOpen=!this._animationsTabIsOpen,this.requestUpdate());}}
                    >
                      <div class="popover-button-text">Animations</div>
                      <div>
                        <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                          <path
                            fill-rule="evenodd"
                            clip-rule="evenodd"
                            d="M10.4697 17.5303C10.1768 17.2374 10.1768 16.7626 10.4697 16.4697L14.9393 12L10.4697 7.53033C10.1768 7.23744 10.1768 6.76256 10.4697 6.46967C10.7626 6.17678 11.2374 6.17678 11.5303 6.46967L16.5303 11.4697C16.8232 11.7626 16.8232 12.2374 16.5303 12.5303L11.5303 17.5303C11.2374 17.8232 10.7626 17.8232 10.4697 17.5303Z"
                            fill="#4C5863"
                          />
                        </svg>
                      </div>
                    </button>
                  `:a$3``}
              ${this._hasMultipleThemes&&!this._styleTabIsOpen&&!this._animationsTabIsOpen&&!this._statesTabIsOpen?a$3` <button
                    class="popover-button"
                    aria-label="Themes"
                    @click=${()=>{this._styleTabIsOpen=!this._styleTabIsOpen,this.requestUpdate();}}
                    @keydown=${s=>{(s.code==="Space"||s.code==="Enter")&&(this._styleTabIsOpen=!this._styleTabIsOpen,this.requestUpdate());}}
                  >
                    <div class="popover-button-text">Themes</div>
                    <div>
                      <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path
                          fill-rule="evenodd"
                          clip-rule="evenodd"
                          d="M10.4697 17.5303C10.1768 17.2374 10.1768 16.7626 10.4697 16.4697L14.9393 12L10.4697 7.53033C10.1768 7.23744 10.1768 6.76256 10.4697 6.46967C10.7626 6.17678 11.2374 6.17678 11.5303 6.46967L16.5303 11.4697C16.8232 11.7626 16.8232 12.2374 16.5303 12.5303L11.5303 17.5303C11.2374 17.8232 10.7626 17.8232 10.4697 17.5303Z"
                          fill="#4C5863"
                        />
                      </svg>
                    </div>
                  </button>`:""}
              ${this._hasMultipleStates&&!this._styleTabIsOpen&&!this._animationsTabIsOpen&&!this._statesTabIsOpen?a$3` <button
                    class="popover-button"
                    aria-label="States"
                    @click=${()=>{this._statesTabIsOpen=!this._statesTabIsOpen,this.requestUpdate();}}
                    @keydown=${s=>{(s.code==="Space"||s.code==="Enter")&&(this._statesTabIsOpen=!this._statesTabIsOpen,this.requestUpdate());}}
                  >
                    <div class="popover-button-text">States</div>
                    <div>
                      <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path
                          fill-rule="evenodd"
                          clip-rule="evenodd"
                          d="M10.4697 17.5303C10.1768 17.2374 10.1768 16.7626 10.4697 16.4697L14.9393 12L10.4697 7.53033C10.1768 7.23744 10.1768 6.76256 10.4697 6.46967C10.7626 6.17678 11.2374 6.17678 11.5303 6.46967L16.5303 11.4697C16.8232 11.7626 16.8232 12.2374 16.5303 12.5303L11.5303 17.5303C11.2374 17.8232 10.7626 17.8232 10.4697 17.5303Z"
                          fill="#4C5863"
                        />
                      </svg>
                    </div>
                  </button>`:""}
              ${this._animationsTabIsOpen?a$3`<button
                      class="option-title-button"
                      aria-label="Back to main popover menu"
                      @click=${()=>{this._animationsTabIsOpen=!this._animationsTabIsOpen,this.requestUpdate();}}
                    >
                      <div class="option-title-chevron">
                        <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                          <path
                            fill-rule="evenodd"
                            clip-rule="evenodd"
                            d="M13.5303 6.46967C13.8232 6.76256 13.8232 7.23744 13.5303 7.53033L9.06066 12L13.5303 16.4697C13.8232 16.7626 13.8232 17.2374 13.5303 17.5303C13.2374 17.8232 12.7626 17.8232 12.4697 17.5303L7.46967 12.5303C7.17678 12.2374 7.17678 11.7626 7.46967 11.4697L12.4697 6.46967C12.7626 6.17678 13.2374 6.17678 13.5303 6.46967Z"
                            fill="#20272C"
                          />
                        </svg>
                      </div>
                      <div>Animations</div>
                    </button>
                    <div class="option-title-separator"></div>
                    <div class="option-row">
                      <ul>
                        ${this.animations().map(s=>a$3`
                            <li>
                              <button
                                class="option-button"
                                aria-label=${`${s}`}
                                @click=${()=>{this._animationsTabIsOpen=!this._animationsTabIsOpen,this._popoverIsOpen=!this._popoverIsOpen,this.play(s),this.requestUpdate();}}
                                @keydown=${c=>{(c.code==="Space"||c.code==="Enter")&&(this._animationsTabIsOpen=!this._animationsTabIsOpen,this._popoverIsOpen=!this._popoverIsOpen,this.play(s),this.requestUpdate());}}
                              >
                                <div class="option-tick">
                                  ${this.currentAnimation()===s?a$3`
                                        <svg
                                          width="24"
                                          height="24"
                                          viewBox="0 0 24 24"
                                          fill="none"
                                          xmlns="http://www.w3.org/2000/svg"
                                        >
                                          <path
                                            fill-rule="evenodd"
                                            clip-rule="evenodd"
                                            d="M20.5281 5.9372C20.821 6.23009 20.821 6.70497 20.5281 6.99786L9.46297 18.063C9.32168 18.2043 9.12985 18.2833 8.93004 18.2826C8.73023 18.2819 8.53895 18.2015 8.39864 18.0593L3.46795 13.0596C3.1771 12.7647 3.1804 12.2898 3.47532 11.999C3.77024 11.7081 4.2451 11.7114 4.53595 12.0063L8.93634 16.4683L19.4675 5.9372C19.7604 5.64431 20.2352 5.64431 20.5281 5.9372Z"
                                            fill="#20272C"
                                          />
                                        </svg>
                                      `:a$3`<div style="width: 24px; height: 24px"></div>`}
                                </div>
                                <div>${s}</div>
                              </button>
                            </li>
                          `)}
                      </ul>
                    </div> `:a$3``}
              ${this._styleTabIsOpen?a$3`<div class="option-title-themes-row">
                      <button
                        class="option-title-button themes"
                        aria-label="Back to main popover menu"
                        @click=${()=>{this._styleTabIsOpen=!this._styleTabIsOpen,this.requestUpdate();}}
                      >
                        <div class="option-title-chevron">
                          <svg
                            width="24"
                            height="24"
                            viewBox="0 0 24 24"
                            fill="none"
                            xmlns="http://www.w3.org/2000/svg"
                          >
                            <path
                              fill-rule="evenodd"
                              clip-rule="evenodd"
                              d="M13.5303 6.46967C13.8232 6.76256 13.8232 7.23744 13.5303 7.53033L9.06066 12L13.5303 16.4697C13.8232 16.7626 13.8232 17.2374 13.5303 17.5303C13.2374 17.8232 12.7626 17.8232 12.4697 17.5303L7.46967 12.5303C7.17678 12.2374 7.17678 11.7626 7.46967 11.4697L12.4697 6.46967C12.7626 6.17678 13.2374 6.17678 13.5303 6.46967Z"
                              fill="#20272C"
                            />
                          </svg>
                        </div>
                        <div class="option-title-text">Themes</div>
                        ${((b=this._dotLottieCommonPlayer)==null?void 0:b.defaultTheme)===""?a$3``:a$3`
                              <button
                                class="reset-btn"
                                @click=${()=>{this.setTheme(""),this.requestUpdate();}}
                              >
                                Reset
                              </button>
                            `}
                      </button>
                    </div>
                    <div class="option-title-separator"></div>
                    <div class="option-row">
                      <ul>
                        ${this._themesForCurrentAnimation.map(s=>a$3`
                            <li>
                              <button
                                class="option-button"
                                aria-label="${s.id}"
                                @click=${()=>{this.setTheme(s.id);}}
                                @keydown=${c=>{(c.code==="Space"||c.code==="Enter")&&this.setTheme(s.id);}}
                              >
                                <div class="option-tick">
                                  ${this.getDefaultTheme()===s.id?a$3`
                                        <svg
                                          width="24"
                                          height="24"
                                          viewBox="0 0 24 24"
                                          fill="none"
                                          xmlns="http://www.w3.org/2000/svg"
                                        >
                                          <path
                                            fill-rule="evenodd"
                                            clip-rule="evenodd"
                                            d="M20.5281 5.9372C20.821 6.23009 20.821 6.70497 20.5281 6.99786L9.46297 18.063C9.32168 18.2043 9.12985 18.2833 8.93004 18.2826C8.73023 18.2819 8.53895 18.2015 8.39864 18.0593L3.46795 13.0596C3.1771 12.7647 3.1804 12.2898 3.47532 11.999C3.77024 11.7081 4.2451 11.7114 4.53595 12.0063L8.93634 16.4683L19.4675 5.9372C19.7604 5.64431 20.2352 5.64431 20.5281 5.9372Z"
                                            fill="#20272C"
                                          />
                                        </svg>
                                      `:a$3`<div style="width: 24px; height: 24px"></div>`}
                                </div>
                                <div>${s.id}</div>
                              </button>
                            </li>
                          `)}
                      </ul>
                    </div>`:a$3``}
              ${this._statesTabIsOpen?a$3`<div class="option-title-themes-row">
                      <button
                        class="option-title-button themes"
                        aria-label="Back to main popover menu"
                        @click=${()=>{this._statesTabIsOpen=!this._statesTabIsOpen,this.requestUpdate();}}
                      >
                        <div class="option-title-chevron">
                          <svg
                            width="24"
                            height="24"
                            viewBox="0 0 24 24"
                            fill="none"
                            xmlns="http://www.w3.org/2000/svg"
                          >
                            <path
                              fill-rule="evenodd"
                              clip-rule="evenodd"
                              d="M13.5303 6.46967C13.8232 6.76256 13.8232 7.23744 13.5303 7.53033L9.06066 12L13.5303 16.4697C13.8232 16.7626 13.8232 17.2374 13.5303 17.5303C13.2374 17.8232 12.7626 17.8232 12.4697 17.5303L7.46967 12.5303C7.17678 12.2374 7.17678 11.7626 7.46967 11.4697L12.4697 6.46967C12.7626 6.17678 13.2374 6.17678 13.5303 6.46967Z"
                              fill="#20272C"
                            />
                          </svg>
                        </div>
                        <div class="option-title-text">States</div>
                        <button
                          class="reset-btn"
                          @click=${()=>{this.exitInteractiveMode(),this.requestUpdate();}}
                        >
                          Reset
                        </button>
                      </button>
                    </div>
                    <div class="option-title-separator"></div>
                    <div class="option-row">
                      <ul>
                        ${this._statesForCurrentAnimation.map(s=>a$3`
                            <li>
                              <button
                                class="option-button"
                                aria-label="${s}"
                                @click=${()=>{this.enterInteractiveMode(s);}}
                                @keydown=${c=>{(c.code==="Space"||c.code==="Enter")&&this.enterInteractiveMode(s);}}
                              >
                                <div class="option-tick">
                                  ${this.getActiveStateMachine()===s?a$3`
                                        <svg
                                          width="24"
                                          height="24"
                                          viewBox="0 0 24 24"
                                          fill="none"
                                          xmlns="http://www.w3.org/2000/svg"
                                        >
                                          <path
                                            fill-rule="evenodd"
                                            clip-rule="evenodd"
                                            d="M20.5281 5.9372C20.821 6.23009 20.821 6.70497 20.5281 6.99786L9.46297 18.063C9.32168 18.2043 9.12985 18.2833 8.93004 18.2826C8.73023 18.2819 8.53895 18.2015 8.39864 18.0593L3.46795 13.0596C3.1771 12.7647 3.1804 12.2898 3.47532 11.999C3.77024 11.7081 4.2451 11.7114 4.53595 12.0063L8.93634 16.4683L19.4675 5.9372C19.7604 5.64431 20.2352 5.64431 20.5281 5.9372Z"
                                            fill="#20272C"
                                          />
                                        </svg>
                                      `:a$3`<div style="width: 24px; height: 24px"></div>`}
                                </div>
                                <div>${s}</div>
                              </button>
                            </li>
                          `)}
                      </ul>
                    </div>`:a$3``}
            </div>
          `:a$3``}
    `}render(){var i;let t=this.controls?"main controls":"main",e=this.controls?"animation controls":"animation";return a$3`
      <div id="animation-container" class=${t} lang="en" role="img" aria-label="lottie-animation-container">
        <div id="animation" class=${e} style="background:${this.background};">
          ${((i=this._dotLottieCommonPlayer)==null?void 0:i.currentState)===f.Error?a$3` <div class="error">⚠️</div> `:void 0}
        </div>
        ${this.controls?this.renderControls():void 0}
      </div>
    `}};a$1([p({type:String})],a.prototype,"defaultTheme",2),a$1([O("#animation")],a.prototype,"container",2),a$1([p()],a.prototype,"playMode",2),a$1([p({type:Boolean})],a.prototype,"autoplay",2),a$1([p({type:String})],a.prototype,"background",2),a$1([p({type:Boolean})],a.prototype,"controls",2),a$1([p({type:Number})],a.prototype,"direction",2),a$1([p({type:Boolean})],a.prototype,"hover",2),a$1([p({type:String})],a.prototype,"loop",2),a$1([p({type:String})],a.prototype,"renderer",2),a$1([p({type:Number})],a.prototype,"speed",2),a$1([p({type:String})],a.prototype,"src",2),a$1([p()],a.prototype,"intermission",2),a$1([p({type:String})],a.prototype,"activeAnimationId",2),a$1([p({type:Boolean})],a.prototype,"light",2),a$1([p({type:Boolean})],a.prototype,"worker",2),a$1([p({type:String})],a.prototype,"activeStateId",2),a$1([k()],a.prototype,"_seeker",2);customElements.get(T)||customElements.define(T,a);/*! Bundled license information:

@lit/reactive-element/decorators/custom-element.js:
  (**
   * @license
   * Copyright 2017 Google LLC
   * SPDX-License-Identifier: BSD-3-Clause
   *)

@lit/reactive-element/decorators/property.js:
  (**
   * @license
   * Copyright 2017 Google LLC
   * SPDX-License-Identifier: BSD-3-Clause
   *)

@lit/reactive-element/decorators/state.js:
  (**
   * @license
   * Copyright 2017 Google LLC
   * SPDX-License-Identifier: BSD-3-Clause
   *)

@lit/reactive-element/decorators/base.js:
  (**
   * @license
   * Copyright 2017 Google LLC
   * SPDX-License-Identifier: BSD-3-Clause
   *)

@lit/reactive-element/decorators/event-options.js:
  (**
   * @license
   * Copyright 2017 Google LLC
   * SPDX-License-Identifier: BSD-3-Clause
   *)

@lit/reactive-element/decorators/query.js:
  (**
   * @license
   * Copyright 2017 Google LLC
   * SPDX-License-Identifier: BSD-3-Clause
   *)

@lit/reactive-element/decorators/query-all.js:
  (**
   * @license
   * Copyright 2017 Google LLC
   * SPDX-License-Identifier: BSD-3-Clause
   *)

@lit/reactive-element/decorators/query-async.js:
  (**
   * @license
   * Copyright 2017 Google LLC
   * SPDX-License-Identifier: BSD-3-Clause
   *)

@lit/reactive-element/decorators/query-assigned-elements.js:
  (**
   * @license
   * Copyright 2021 Google LLC
   * SPDX-License-Identifier: BSD-3-Clause
   *)

@lit/reactive-element/decorators/query-assigned-nodes.js:
  (**
   * @license
   * Copyright 2017 Google LLC
   * SPDX-License-Identifier: BSD-3-Clause
   *)
*/

export { a as DotLottiePlayer };
//# sourceMappingURL=out.js.map
//# sourceMappingURL=dotlottie-player.mjs.map