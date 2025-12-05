(()=>{"use strict";var e={n:t=>{var n=t&&t.__esModule?()=>t.default:()=>t;return e.d(n,{a:n}),n},d:(t,n)=>{for(var o in n)e.o(n,o)&&!e.o(t,o)&&Object.defineProperty(t,o,{enumerable:!0,get:n[o]})},o:(e,t)=>Object.prototype.hasOwnProperty.call(e,t)};const t=window.wp.element,n=window.wp.i18n,o=window.yoast.styledComponents;var i=e.n(o);const a=window.ReactJSXRuntime,r=o.createGlobalStyle`
	@media only screen and (min-width: 1024px) {
		.BeaconFabButtonFrame.BeaconFabButtonFrame {
			${e=>"1"===e.isRtl?"left":"right"}: 340px !important;
		}
	}
`;function s(e){const n=document.createElement("div");n.setAttribute("id","yoast-helpscout-beacon"),(0,t.createRoot)(n).render(e),document.body.appendChild(n)}function c(){return!!document.getElementById("sidebar")}function l(e,t=""){!function(e,t){let n=e.Beacon||function(){};function o(){const e=t.getElementsByTagName("script")[0],n=t.createElement("script");n.type="text/javascript",n.async=!0,n.src="https://beacon-v2.helpscout.net",e.parentNode.insertBefore(n,e)}if(e.Beacon=n=function(t,n,o){e.Beacon.readyQueue.push({method:t,options:n,data:o})},n.readyQueue=[],"complete"===t.readyState)return o();e.attachEvent?e.attachEvent("onload",o):e.addEventListener("load",o,!1)}(window,document,window.Beacon),window.Beacon("init",e),function(e){""!==e&&(void 0!==(e=JSON.parse(e)).name&&void 0!==e.email&&(window.Beacon("prefill",{name:e.name,email:e.email}),delete e.name,delete e.email),window.Beacon("session-data",e))}(t),"1"===window.wpseoAdminGlobalL10n.isRtl&&window.Beacon("config",{display:{position:"left"}}),c()&&s((0,a.jsx)(r,{isRtl:window.wpseoAdminGlobalL10n.isRtl}))}window.wpseoHelpScoutBeacon=l,window.wpseoHelpScoutBeaconConsent=function(e,o=null){const d=i().div`
		border-radius: 60px;
		height: 60px;
		position: fixed;
		transform: scale(1);
		width: 60px;
		z-index: 1049;
		bottom: 40px;
		box-shadow: rgba(0, 0, 0, 0.1) 0 4px 7px;
		${e=>"1"===e.isRtl?"left":"right"}: 40px;
		top: auto;
		border-width: initial;
		border-style: none;
		border-color: initial;
		border-image: initial;
		transition: box-shadow 250ms ease 0s, opacity 0.4s ease 0s, scale 1000ms ease-in-out 0s, transform 0.2s ease-in-out 0s;
	`,p=i().span`
		-webkit-box-align: center;
		align-items: center;
		color: white;
		cursor: pointer;
		display: flex;
		height: 100%;
		-webkit-box-pack: center;
		justify-content: center;
		left: 0;
		pointer-events: none;
		position: absolute;
		text-indent: -99999px;
		top: 0;
		width: 60px;
		will-change: opacity, transform;
		opacity: 1 !important;
		transform: rotate(0deg) scale(1) !important;
		transition: opacity 80ms linear 0s, transform 160ms linear 0s;
	`,w=()=>(0,a.jsx)(p,{children:(0,a.jsx)("svg",{xmlns:"http://www.w3.org/2000/svg",width:"52",height:"52",children:(0,a.jsx)("path",{d:"M27.031 32h-2.488v-2.046c0-.635.077-1.21.232-1.72.154-.513.366-.972.639-1.381.272-.41.58-.779.923-1.109.345-.328.694-.652 1.049-.97l.995-.854a6.432 6.432 0 0 0 1.475-1.568c.39-.59.585-1.329.585-2.216 0-.635-.117-1.203-.355-1.703a3.7 3.7 0 0 0-.96-1.263 4.305 4.305 0 0 0-1.401-.783A5.324 5.324 0 0 0 26 16.114c-1.28 0-2.316.375-3.11 1.124-.795.75-1.286 1.705-1.475 2.865L19 19.693c.356-1.772 1.166-3.165 2.434-4.176C22.701 14.507 24.26 14 26.107 14c.947 0 1.842.131 2.682.392.84.262 1.57.648 2.185 1.16a5.652 5.652 0 0 1 1.475 1.892c.368.75.551 1.602.551 2.556 0 .728-.083 1.364-.248 1.909a5.315 5.315 0 0 1-.693 1.467 6.276 6.276 0 0 1-1.048 1.176c-.403.351-.83.71-1.28 1.073-.498.387-.918.738-1.26 1.057a4.698 4.698 0 0 0-.836 1.006 3.847 3.847 0 0 0-.462 1.176c-.095.432-.142.955-.142 1.568V32zM26 37a1.5 1.5 0 1 1 0-3 1.5 1.5 0 0 1 0 3z",fill:"#FFF"})})}),u=i().button`
		-webkit-appearance: none;
		-webkit-box-align: center;
		align-items: center;
		bottom: 0;
		display: block;
		height: 60px;
		-webkit-box-pack: center;
		justify-content: center;
		line-height: 60px;
		position: relative;
		user-select: none;
		z-index: 899;
		background-color: rgb(164, 40, 106);
		color: white;
		cursor: pointer;
		min-width: 60px;
		-webkit-tap-highlight-color: transparent;
		border-radius: 200px;
		margin: 0;
		outline: none;
		padding: 0;
		border-width: initial;
		border-style: none;
		border-color: initial;
		border-image: initial;
		transition: background-color 200ms linear 0s, transform 200ms linear 0s;
	`,m=()=>{const[i,s]=(0,t.useState)(!0),p=c();return(0,a.jsxs)(t.Fragment,{children:[p&&(0,a.jsx)(r,{isRtl:window.wpseoAdminGlobalL10n.isRtl}),i&&(0,a.jsx)(d,{className:p?"BeaconFabButtonFrame":"",isRtl:window.wpseoAdminGlobalL10n.isRtl,children:(0,a.jsx)(u,{type:"button",onClick:function(){const t=(0,n.__)("When you click OK we will open our HelpScout beacon where you can find answers to your questions. This beacon will load our support data and also potentially set cookies.","wordpress-seo");window.confirm(t)&&(l(e,o),window.Beacon("open"),window.setTimeout((()=>{s(!1)}),1e3))},children:(0,a.jsx)(w,{})})})]})};s((0,a.jsx)(m,{}))}})();