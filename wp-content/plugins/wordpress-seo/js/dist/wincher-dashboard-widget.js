(()=>{"use strict";var e={n:t=>{var s=t&&t.__esModule?()=>t.default:()=>t;return e.d(s,{a:s}),s},d:(t,s)=>{for(var r in s)e.o(s,r)&&!e.o(t,r)&&Object.defineProperty(t,r,{enumerable:!0,get:s[r]})},o:(e,t)=>Object.prototype.hasOwnProperty.call(e,t)};const t=window.wp.element,s=window.lodash,r=window.wp.i18n,n=window.yoast.styledComponents;var o=e.n(n);const i=window.yoast.propTypes;var a=e.n(i);const c=window.yoast.helpers,l=window.yoast.componentsNew,d=(e,s)=>{try{return(0,t.createInterpolateElement)(e,s)}catch(t){return console.error("Error in translation for:",e,t),e}},p=window.ReactJSXRuntime,h=({className:e=""})=>(0,p.jsx)(l.Alert,{type:"warning",className:e,children:(0,r.sprintf)(/* translators: %s: Expands to "Wincher". */
(0,r.__)('Your %s account does not contain any keyphrases for this website yet. You can track keyphrases by using the "Track SEO Performance" button in the post editor.',"wordpress-seo"),"Wincher")});h.propTypes={className:a().string};const u=h,w=({onReconnect:e,className:t=""})=>{const s=(0,r.sprintf)(/* translators: %s expands to a link to open the Wincher login popup. */
(0,r.__)("It seems like something went wrong when retrieving your website's data. Please %s and try again.","wordpress-seo"),"<reconnectToWincher/>","Wincher");return(0,p.jsx)(l.Alert,{type:"error",className:t,children:d(s,{reconnectToWincher:(0,p.jsx)("a",{href:"#",onClick:t=>{t.preventDefault(),e()},children:(0,r.sprintf)(/* translators: %s : Expands to "Wincher". */
(0,r.__)("reconnect to %s","wordpress-seo"),"Wincher")})})})};w.propTypes={onReconnect:a().func.isRequired,className:a().string};const b=w,m=window.yoast.styleGuide,g=window.wp.apiFetch;var y=e.n(g);async function f(e){try{return await y()(e)}catch(e){return e.error&&e.status?e:e instanceof Response&&await e.json()}}const x=o().p`
	color: ${m.colors.$color_pink_dark};
	font-size: 14px;
	font-weight: 700;
	margin: 13px 0 10px;
`,k=o()(l.SvgIcon)`
	margin-right: 5px;
	vertical-align: middle;
`,j=o().button`
	position: absolute;
	top: 9px;
	right: 9px;
	border: none;
	background: none;
	cursor: pointer;
`,_=o().p`
	font-size: 13px;
	font-weight: 500;
	margin: 10px 0 13px;
`,T=o().div`
	position: relative;
	background: ${e=>e.isTitleShortened?"#f5f7f7":"transparent"};
	border: 1px solid #c7c7c7;
	border-left: 4px solid${m.colors.$color_pink_dark};
	padding: 0 16px;
	margin-bottom: 1.5em;
`,v=({limit:e,usage:t,isTitleShortened:s=!1,isFreeAccount:n=!1})=>{const o=(0,r.sprintf)(
/* Translators: %1$s expands to the number of used keywords.
   * %2$s expands to the account keywords limit.
   */
(0,r.__)("Your are tracking %1$s out of %2$s keyphrases included in your free account.","wordpress-seo"),t,e),i=(0,r.sprintf)(
/* Translators: %1$s expands to the number of used keywords.
   * %2$s expands to the account keywords limit.
   */
(0,r.__)("Your are tracking %1$s out of %2$s keyphrases included in your account.","wordpress-seo"),t,e),a=n?o:i,c=(0,r.sprintf)(
/* Translators: %1$s expands to the number of used keywords.
   * %2$s expands to the account keywords limit.
   */
(0,r.__)("Keyphrases tracked: %1$s/%2$s","wordpress-seo"),t,e),l=s?c:a;return(0,p.jsxs)(x,{children:[s&&(0,p.jsx)(k,{icon:"exclamation-triangle",color:m.colors.$color_pink_dark,size:"14px"}),l]})};v.propTypes={limit:a().number.isRequired,usage:a().number.isRequired,isTitleShortened:a().bool,isFreeAccount:a().bool};const C=(0,c.makeOutboundLink)(),R=({discount:e,months:t})=>{const s=(0,p.jsx)(C,{href:wpseoAdminGlobalL10n["links.wincher.upgrade"],style:{fontWeight:600},children:(0,r.sprintf)(/* Translators: %s : Expands to "Wincher". */
(0,r.__)("Click here to upgrade your %s plan","wordpress-seo"),"Wincher")});if(!e||!t)return(0,p.jsx)(_,{children:s});const n=100*e,o=(0,r.sprintf)(
/* Translators: %1$s expands to upgrade account link.
   * %2$s expands to the upgrade discount value.
   * %3$s expands to the upgrade discount duration e.g. 2 months.
   */
(0,r.__)("%1$s and get an exclusive %2$s discount for %3$s month(s).","wordpress-seo"),"<wincherAccountUpgradeLink/>",n+"%",t);return(0,p.jsx)(_,{children:d(o,{wincherAccountUpgradeLink:s})})};R.propTypes={discount:a().number,months:a().number};const q=({onClose:e=null,isTitleShortened:s=!1,trackingInfo:n=null})=>{const o=(()=>{const[e,s]=(0,t.useState)(null);return(0,t.useEffect)((()=>{e||async function(){return await f({path:"yoast/v1/wincher/account/upgrade-campaign",method:"GET"})}().then((e=>s(e)))}),[e]),e})();if(null===n)return null;const{limit:i,usage:a}=n;if(!(i&&a/i>=.8))return null;const c=Boolean(null==o?void 0:o.discount);return(0,p.jsxs)(T,{isTitleShortened:s,children:[e&&(0,p.jsx)(j,{type:"button","aria-label":(0,r.__)("Close the upgrade callout","wordpress-seo"),onClick:e,children:(0,p.jsx)(l.SvgIcon,{icon:"times-circle",color:m.colors.$color_pink_dark,size:"14px"})}),(0,p.jsx)(v,{...n,isTitleShortened:s,isFreeAccount:c}),(0,p.jsx)(R,{discount:null==o?void 0:o.discount,months:null==o?void 0:o.months})]})};q.propTypes={onClose:a().func,isTitleShortened:a().bool,trackingInfo:a().object};const D=q;window.moment;const N=({data:e,mapChartDataToTableData:t=null,dataTableCaption:s,dataTableHeaderLabels:n,isDataTableVisuallyHidden:o=!0})=>e.length!==n.length?(0,p.jsx)("p",{children:(0,r.__)("The number of headers and header labels don't match.","wordpress-seo")}):(0,p.jsx)("div",{className:o?"screen-reader-text":null,children:(0,p.jsxs)("table",{children:[(0,p.jsx)("caption",{children:s}),(0,p.jsx)("thead",{children:(0,p.jsx)("tr",{children:n.map(((e,t)=>(0,p.jsx)("th",{children:e},t)))})}),(0,p.jsx)("tbody",{children:(0,p.jsx)("tr",{children:e.map(((e,s)=>(0,p.jsx)("td",{children:t(e.y)},s)))})})]})});N.propTypes={data:a().arrayOf(a().shape({x:a().number,y:a().number})).isRequired,mapChartDataToTableData:a().func,dataTableCaption:a().string.isRequired,dataTableHeaderLabels:a().array.isRequired,isDataTableVisuallyHidden:a().bool};const S=N,I=({data:e,width:s,height:r,fillColor:n=null,strokeColor:o="#000000",strokeWidth:i=1,className:a="",mapChartDataToTableData:c=null,dataTableCaption:l,dataTableHeaderLabels:d,isDataTableVisuallyHidden:h=!0})=>{const u=Math.max(1,Math.max(...e.map((e=>e.x)))),w=Math.max(1,Math.max(...e.map((e=>e.y)))),b=r-i,m=e.map((e=>`${e.x/u*s},${b-e.y/w*b+i}`)).join(" "),g=`0,${b+i} `+m+` ${s},${b+i}`;return(0,p.jsxs)(t.Fragment,{children:[(0,p.jsxs)("svg",{width:s,height:r,viewBox:`0 0 ${s} ${r}`,className:a,role:"img","aria-hidden":"true",focusable:"false",children:[(0,p.jsx)("polygon",{fill:n,points:g}),(0,p.jsx)("polyline",{fill:"none",stroke:o,strokeWidth:i,strokeLinejoin:"round",strokeLinecap:"round",points:m})]}),c&&(0,p.jsx)(S,{data:e,mapChartDataToTableData:c,dataTableCaption:l,dataTableHeaderLabels:d,isDataTableVisuallyHidden:h})]})};I.propTypes={data:a().arrayOf(a().shape({x:a().number,y:a().number})).isRequired,width:a().number.isRequired,height:a().number.isRequired,fillColor:a().string,strokeColor:a().string,strokeWidth:a().number,className:a().string,mapChartDataToTableData:a().func,dataTableCaption:a().string.isRequired,dataTableHeaderLabels:a().array.isRequired,isDataTableVisuallyHidden:a().bool};const L=I;o()(l.SvgIcon)`
	margin-left: 2px;
	flex-shrink: 0;
	rotate: ${e=>e.isImproving?"-90deg":"90deg"};
`,o().span`
	color: ${e=>e.isImproving?"#69AB56":"#DC3332"};
	font-size: 13px;
	font-weight: 600;
	line-height: 20px;
	margin-right: 2px;
	margin-left: 12px;
`;function E(e){return Math.round(100*e)}function A({chartData:e={}}){if((0,s.isEmpty)(e)||(0,s.isEmpty)(e.position))return"?";const t=function(e){return Array.from({length:e.position.history.length},((e,t)=>t+1)).map((e=>(0,r.sprintf)((0,r._n)("%d day","%d days",e,"wordpress-seo"),e)))}(e),n=e.position.history.map(((e,t)=>({x:t,y:31-e.value})));return(0,p.jsx)(L,{width:66,height:24,data:n,strokeWidth:1.8,strokeColor:"#498afc",fillColor:"#ade3fc",mapChartDataToTableData:E,dataTableCaption:(0,r.__)("Keyphrase position in the last 90 days on a scale from 0 to 30.","wordpress-seo"),dataTableHeaderLabels:t})}function W(e){return!e||!e.position||e.position.value>30?"> 30":e.position.value}o().td`
	padding-right: 0 !important;

	& > div {
		margin: 0px;
	}
`,o().td`
	padding-left: 2px !important;
`,o().td.attrs({className:"yoast-table--nopadding"})`
	& > div {
		justify-content: center;
	}
`,o().div`
	display: flex;
	align-items: center;
	& > a {
		box-sizing: border-box;
	}
`,o().button`
	background: none;
	color: inherit;
	border: none;
	padding: 0;
	font: inherit;
	cursor: pointer;
	outline: inherit;
    display: flex;
    align-items: center;
`,o().tr`
	background-color: ${e=>e.isEnabled?"#FFFFFF":"#F9F9F9"} !important;
`,A.propTypes={chartData:a().object};a().object,a().object,a().string.isRequired,a().func,a().func,a().bool,a().bool,a().bool,a().string,a().bool.isRequired,a().func.isRequired;const $=(0,c.makeOutboundLink)(),P=(0,c.makeOutboundLink)(),H=(0,c.makeOutboundLink)(),F=(0,c.makeOutboundLink)(),B=o().div`
	& .wincher-performance-report-alert {
		margin-bottom: 1em;
	}
`,O=o().table`
	pointer-events: none;
	user-select: none;
`,G=o().div`
	position: relative;
	width: 100%;
	overflow-y: auto;
`,Y=o().div`
	margin: 0;
	-webkit-filter: blur(4px);
	-moz-filter: blur(4px);
	-o-filter: blur(4px);
	-ms-filter: blur(4px);
	filter: blur(4px);
`,z=o().p`
	top: 47%;
	left: 50%;
	position: absolute;
`,M=({websiteId:e,id:t})=>`https://app.wincher.com/websites/${e}/keywords?serp=${t}&utm_medium=plugin&utm_source=yoast&referer=yoast&partner=yoast`,V=({isLoggedIn:e,onConnectAction:t})=>e?null:(0,p.jsx)(z,{children:(0,p.jsx)(l.NewButton,{onClick:t,variant:"primary",style:{left:"-50%",backgroundColor:"#2371b0"},children:(0,r.sprintf)(/* translators: %s expands to Wincher */
(0,r.__)("Connect with %s","wordpress-seo"),"Wincher")})});V.propTypes={isLoggedIn:a().bool.isRequired,onConnectAction:a().func.isRequired};const K=({isBlurred:e,children:t})=>e?(0,p.jsx)("td",{children:(0,p.jsx)(Y,{children:t})}):(0,p.jsx)("td",{children:t});K.propTypes={isBlurred:a().bool.isRequired,children:a().oneOfType([a().string,a().number,a().object]).isRequired};const U=({keyphrase:e,websiteId:t,isBlurred:s})=>(0,p.jsxs)("tr",{children:[(0,p.jsx)(K,{isBlurred:s,children:e.keyword}),(0,p.jsx)(K,{isBlurred:s,children:W(e)}),(0,p.jsx)(K,{isBlurred:s,className:"yoast-table--nopadding",children:(0,p.jsx)(A,{chartData:e})}),(0,p.jsx)(K,{isBlurred:s,className:"yoast-table--nobreak",children:(0,p.jsx)($,{href:M({websiteId:t,id:e.id}),children:(0,r.__)("View","wordpress-seo")})})]});U.propTypes={keyphrase:a().object.isRequired,websiteId:a().string.isRequired,isBlurred:a().bool.isRequired};const X=()=>(0,p.jsx)(l.Alert,{type:"error",className:"wincher-performance-report-alert",children:(0,r.__)("Network Error: Unable to connect to the server. Please check your internet connection and try again later.","wordpress-seo")}),J=({data:e})=>!(0,s.isEmpty)(e)&&(0,s.isEmpty)(e.results)?(0,p.jsx)(l.Alert,{type:"success",className:"wincher-performance-report-alert",children:(0,r.sprintf)(/* translators: %1$s and %2$s: Expands to "Wincher". */
(0,r.__)('You have successfully connected with %1$s. Your %2$s account does not contain any keyphrases for this website yet. You can track keyphrases by using the "Track SEO Performance" button in the post editor.',"wordpress-seo"),"Wincher","Wincher")}):(0,p.jsx)(l.Alert,{type:"success",className:"wincher-performance-report-alert",children:(0,r.sprintf)(/* translators: %s: Expands to "Wincher". */
(0,r.__)("You have successfully connected with %s.","wordpress-seo"),"Wincher")});J.propTypes={data:a().object.isRequired};const Q=({data:e,onConnectAction:t,isConnectSuccess:s,isNetworkError:r,isFailedRequest:n})=>r?(0,p.jsx)(X,{}):s?(0,p.jsx)(J,{data:e}):n?(0,p.jsx)(b,{onReconnect:t,className:"wincher-performance-report-alert"}):null;Q.propTypes={data:a().object.isRequired,onConnectAction:a().func.isRequired,isConnectSuccess:a().bool.isRequired,isNetworkError:a().bool.isRequired,isFailedRequest:a().bool.isRequired};const Z=({data:e,onConnectAction:t,isNetworkError:r,isConnectSuccess:n})=>{const o=(e=>e&&[401,403,404].includes(e.status))(e);return r||n||o?(0,p.jsx)(Q,{data:e,onConnectAction:t,isConnectSuccess:n,isNetworkError:r,isFailedRequest:o}):!e||(0,s.isEmpty)(e.results)?(0,p.jsx)(u,{className:"wincher-performance-report-alert"}):null};Z.propTypes={data:a().object.isRequired,onConnectAction:a().func.isRequired,isConnectSuccess:a().bool.isRequired,isNetworkError:a().bool.isRequired};const ee=({isLoggedIn:e})=>{const t=(0,r.sprintf)(/* translators: %s expands to a link to Wincher login */
(0,r.__)("This overview only shows you keyphrases added to Yoast SEO. There may be other keyphrases added to your %s.","wordpress-seo"),"<wincherAccountLink/>"),s=(0,r.sprintf)(/* translators: %s expands to a link to Wincher login */
(0,r.__)("This overview will show you your top performing keyphrases in Google. Connect with %s to get started.","wordpress-seo"),"<wincherLink/>"),n=e?t:s;return(0,p.jsx)("p",{children:d(n,{wincherAccountLink:(0,p.jsx)(H,{href:wpseoAdminGlobalL10n["links.wincher.login"],children:(0,r.sprintf)(/* translators: %s : Expands to "Wincher". */
(0,r.__)("%s account","wordpress-seo"),"Wincher")}),wincherLink:(0,p.jsx)(F,{href:wpseoAdminGlobalL10n["links.wincher.about"],children:"Wincher"})})})};ee.propTypes={isLoggedIn:a().bool.isRequired};const te=({isBlurred:e,children:t})=>e?(0,p.jsx)(O,{className:"yoast yoast-table",children:t}):(0,p.jsx)("table",{className:"yoast yoast-table",children:t});te.propTypes={isBlurred:a().bool.isRequired,children:a().node.isRequired};const se=({className:e="wincher-seo-performance",data:n,websiteId:o,isLoggedIn:i,isConnectSuccess:a,isNetworkError:c,onConnectAction:l})=>{const d=!i,h=(e=>e&&!(0,s.isEmpty)(e)&&!(0,s.isEmpty)(e.results))(n),u=(e=>{const[s,r]=(0,t.useState)(null);return(0,t.useEffect)((()=>{e&&!s&&async function(){return await f({path:"yoast/v1/wincher/account/limit",method:"GET"})}().then((e=>r(e)))}),[s]),s})(i);return(0,p.jsxs)(B,{className:e,children:[i&&(0,p.jsx)(D,{isTitleShortened:!0,trackingInfo:u}),(0,p.jsx)(Z,{data:n,onConnectAction:l,isNetworkError:c,isConnectSuccess:a&&i}),h&&(0,p.jsxs)(t.Fragment,{children:[(0,p.jsx)(ee,{isLoggedIn:i}),(0,p.jsxs)(G,{children:[(0,p.jsxs)(te,{isBlurred:d,children:[(0,p.jsx)("thead",{children:(0,p.jsxs)("tr",{children:[(0,p.jsx)("th",{scope:"col",abbr:(0,r.__)("Keyphrase","wordpress-seo"),children:(0,r.__)("Keyphrase","wordpress-seo")}),(0,p.jsx)("th",{scope:"col",abbr:(0,r.__)("Position","wordpress-seo"),children:(0,r.__)("Position","wordpress-seo")}),(0,p.jsx)("th",{scope:"col",abbr:(0,r.__)("Position over time","wordpress-seo"),children:(0,r.__)("Position over time","wordpress-seo")}),(0,p.jsx)("td",{className:"yoast-table--nobreak"})]})}),(0,p.jsx)("tbody",{children:(0,s.map)(n.results,((e,t)=>(0,p.jsx)(U,{keyphrase:e,websiteId:o,isBlurred:d},`keyphrase-${t}`)))})]}),(0,p.jsx)(V,{isLoggedIn:i,onConnectAction:l})]}),(0,p.jsx)("p",{style:{marginBottom:0,position:"relative"},children:(0,p.jsx)(P,{href:wpseoAdminGlobalL10n["links.wincher.login"],children:(0,r.sprintf)(/* translators: %s expands to Wincher */
(0,r.__)("Get more insights over at %s","wordpress-seo"),"Wincher")})})]})]})};se.propTypes={className:a().string,data:a().object.isRequired,websiteId:a().string.isRequired,isLoggedIn:a().bool.isRequired,isConnectSuccess:a().bool.isRequired,isNetworkError:a().bool.isRequired,onConnectAction:a().func.isRequired};const re=se;class ne{constructor(e,t={},s={}){this.url=e,this.origin=new URL(e).origin,this.eventHandlers=Object.assign({success:{type:"",callback:()=>{}},error:{type:"",callback:()=>{}}},t),this.options=Object.assign({height:570,width:340,title:""},s),this.popup=null,this.createPopup=this.createPopup.bind(this),this.messageHandler=this.messageHandler.bind(this),this.getPopup=this.getPopup.bind(this)}createPopup(){const{height:e,width:t,title:s}=this.options,r=["top="+(window.top.outerHeight/2+window.top.screenY-e/2),"left="+(window.top.outerWidth/2+window.top.screenX-t/2),"width="+t,"height="+e,"resizable=1","scrollbars=1","status=0"];this.popup&&!this.popup.closed||(this.popup=window.open(this.url,s,r.join(","))),this.popup&&this.popup.focus(),window.addEventListener("message",this.messageHandler,!1)}async messageHandler(e){const{data:t,source:s,origin:r}=e;r===this.origin&&this.popup===s&&(t.type===this.eventHandlers.success.type&&(this.popup.close(),window.removeEventListener("message",this.messageHandler,!1),await this.eventHandlers.success.callback(t)),t.type===this.eventHandlers.error.type&&(this.popup.close(),window.removeEventListener("message",this.messageHandler,!1),await this.eventHandlers.error.callback(t)))}getPopup(){return this.popup}isClosed(){return!this.popup||this.popup.closed}focus(){this.isClosed()||this.popup.focus()}}class oe extends t.Component{constructor(){super(),this.state={wincherData:{},wincherWebsiteId:wpseoWincherDashboardWidgetL10n.wincher_website_id,wincherIsLoggedIn:"1"===wpseoWincherDashboardWidgetL10n.wincher_is_logged_in,isDataFetched:!1,isConnectSuccess:!1,isNetworkError:!1},this.onConnect=this.onConnect.bind(this),this.getWincherData=this.getWincherData.bind(this),this.performAuthenticationRequest=this.performAuthenticationRequest.bind(this),this.onConnectSuccess=this.onConnectSuccess.bind(this),this.onNetworkDisconnectionError=this.onNetworkDisconnectionError.bind(this)}componentDidMount(){const e=jQuery("#wpseo-wincher-dashboard-overview-hide");e.is(":checked")&&this.fetchData(),e.on("click",(()=>{this.fetchData()}))}fetchData(){this.state.isDataFetched||(this.state.wincherIsLoggedIn&&this.getWincherData(),this.setState({isDataFetched:!0}))}async getWincherData(){const e=await async function(e=null,t=null,s=null,r){return await f({path:"yoast/v1/wincher/keyphrases",method:"POST",data:{keyphrases:e,permalink:s,startAt:t},signal:r})}();if(200===e.status){const t=(0,s.filter)(e.results,(e=>!(0,s.isEmpty)(e.position))),r=(0,s.sortBy)(t,(e=>e.position.value)).splice(0,5);this.setState({wincherData:{results:r,status:e.status}})}else this.setState({wincherData:{results:[],status:e.status}})}async performAuthenticationRequest(e){if(200!==(await async function(e){const{code:t,websiteId:s}=e;return await f({path:"yoast/v1/wincher/authenticate",method:"POST",data:{code:t,websiteId:s}})}(e)).status)return;this.setState({wincherIsLoggedIn:!0,wincherWebsiteId:e.websiteId.toString()}),await this.getWincherData();const t=this.loginPopup.getPopup();t&&t.close()}async onConnectSuccess(e){this.setState({isConnectSuccess:!0,isNetworkError:!1}),await this.performAuthenticationRequest(e)}async onNetworkDisconnectionError(){this.setState({isConnectSuccess:!1,isNetworkError:!0})}async onConnect(){if(this.loginPopup&&!this.loginPopup.isClosed())return void this.loginPopup.focus();const{url:e}=await async function(){return await f({path:"yoast/v1/wincher/authorization-url",method:"GET"})}();e&&void 0!==e?(this.loginPopup=new ne(e,{success:{type:"wincher:oauth:success",callback:e=>this.onConnectSuccess(e)},error:{type:"wincher:oauth:error",callback:()=>{}}},{title:"Wincher_login",width:500,height:700}),this.loginPopup.createPopup()):this.onNetworkDisconnectionError()}render(){return(0,p.jsx)(re,{data:this.state.wincherData,websiteId:this.state.wincherWebsiteId,isLoggedIn:this.state.wincherIsLoggedIn,isConnectSuccess:this.state.isConnectSuccess,isNetworkError:this.state.isNetworkError,onConnectAction:this.onConnect},"wincher-performance-report")}}const ie=document.getElementById("yoast-seo-wincher-dashboard-widget");ie&&(0,t.createRoot)(ie).render((0,p.jsx)(oe,{}))})();