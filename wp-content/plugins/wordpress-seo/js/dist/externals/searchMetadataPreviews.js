(()=>{var e={30888:(e,t,n)=>{"use strict";Object.defineProperty(t,"__esModule",{value:!0}),t.YoastSlideToggle=void 0;var i=l(n(99196)),r=l(n(85890)),o=l(n(98487)),s=n(64317),a=n(90876);function l(e){return e&&e.__esModule?e:{default:e}}const d=o.default.div`
	& > :first-child {
		overflow: hidden;
		transition: height ${e=>`${e.duration}ms`} ease-out;
	}
`;class u extends i.default.Component{resetHeight(e){e.style.height="0"}setHeight(e){const t=(0,a.getHeight)(e);e.style.height=t+"px"}removeHeight(e){e.style.height=null}render(){return i.default.createElement(d,{duration:this.props.duration},i.default.createElement(s.CSSTransition,{in:this.props.isOpen,timeout:this.props.duration,classNames:"slide",unmountOnExit:!0,onEnter:this.resetHeight,onEntering:this.setHeight,onEntered:this.removeHeight,onExit:this.setHeight,onExiting:this.resetHeight},this.props.children))}}t.YoastSlideToggle=u,u.propTypes={isOpen:r.default.bool.isRequired,duration:r.default.number,children:r.default.node},u.defaultProps={duration:300,children:[]}},90876:(e,t)=>{"use strict";Object.defineProperty(t,"__esModule",{value:!0}),t.getHeight=function(e){return Math.max(e.clientHeight,e.offsetHeight,e.scrollHeight)}},76990:(e,t,n)=>{"use strict";Object.defineProperty(t,"__esModule",{value:!0}),t.getTitleProgress=t.getDescriptionProgress=void 0;var i=n(42982);t.getTitleProgress=e=>{const t=i.helpers.measureTextWidth(e),n=new i.assessments.seo.PageTitleWidthAssessment({scores:{widthTooShort:9}},!0),r=n.calculateScore(t);return{max:n.getMaximumLength(),actual:t,score:r}},t.getDescriptionProgress=(e,t,n,r,o)=>{const s=i.languageProcessing.countMetaDescriptionLength(t,e),a=n&&!r?new i.assessments.seo.MetaDescriptionLengthAssessment({scores:{tooLong:3,tooShort:3}}):new i.assessments.seo.MetaDescriptionLengthAssessment,l=a.calculateScore(s,o);return{max:a.getMaximumLength(o),actual:s,score:l}}},40412:(e,t,n)=>{"use strict";Object.defineProperty(t,"__esModule",{value:!0}),t.safeCreateInterpolateElement=void 0;var i=n(69307);t.safeCreateInterpolateElement=(e,t)=>{try{return(0,i.createInterpolateElement)(e,t)}catch(t){return console.error("Error in translation for:",e,t),e}}},90695:(e,t,n)=>{"use strict";Object.defineProperty(t,"__esModule",{value:!0}),t.default=void 0;var i=function(e,t){if(e&&e.__esModule)return e;if(null===e||"object"!=typeof e&&"function"!=typeof e)return{default:e};var n=c(t);if(n&&n.has(e))return n.get(e);var i={__proto__:null},r=Object.defineProperty&&Object.getOwnPropertyDescriptor;for(var o in e)if("default"!==o&&Object.prototype.hasOwnProperty.call(e,o)){var s=r?Object.getOwnPropertyDescriptor(e,o):null;s&&(s.get||s.set)?Object.defineProperty(i,o,s):i[o]=e[o]}return i.default=e,n&&n.set(e,i),i}(n(99196)),r=p(n(98487)),o=n(65736),s=p(n(85890)),a=n(81413),l=n(23695),d=n(37188),u=n(99806);function p(e){return e&&e.__esModule?e:{default:e}}function c(e){if("function"!=typeof WeakMap)return null;var t=new WeakMap,n=new WeakMap;return(c=function(e){return e?n:t})(e)}const h=r.default.fieldset`
	border: 0;
	padding: 0;
	margin: 0 0 16px;
`,f=r.default.legend`
	margin: 8px 0;
	padding: 0;
	color: ${d.colors.$color_headings};
	font-size: 14px;
	font-weight: 600;
`,g=(0,r.default)(a.Label)`
	${(0,l.getDirectionalStyle)("margin-right: 16px","margin-left: 16px")};
	color: inherit;
	font-size: 14px;
	line-height: 1.71428571;
	cursor: pointer;
	/* Helps RTL in Chrome */
	display: inline-block;
`,m=(0,r.default)(a.Input)`
	&& {
		${(0,l.getDirectionalStyle)("margin: 0 8px 0 0","margin: 0 0 0 8px")};
		cursor: pointer;
	}
`;class v extends i.Component{constructor(e){super(e),this.switchToMobile=this.props.onChange.bind(this,"mobile"),this.switchToDesktop=this.props.onChange.bind(this,"desktop")}render(){const{active:e,mobileModeInputId:t,desktopModeInputId:n}=this.props,r=t.length>0?t:"yoast-google-preview-mode-mobile",s=n.length>0?n:"yoast-google-preview-mode-desktop";return i.default.createElement(h,null,i.default.createElement(f,null,(0,o.__)("Preview as:","wordpress-seo")),i.default.createElement(m,{onChange:this.switchToMobile,type:"radio",name:"screen",value:"mobile",optionalAttributes:{id:r,checked:e===u.MODE_MOBILE}}),i.default.createElement(g,{for:r},(0,o.__)("Mobile result","wordpress-seo")),i.default.createElement(m,{onChange:this.switchToDesktop,type:"radio",name:"screen",value:"desktop",optionalAttributes:{id:s,checked:e===u.MODE_DESKTOP}}),i.default.createElement(g,{for:s},(0,o.__)("Desktop result","wordpress-seo")))}}v.propTypes={onChange:s.default.func.isRequired,active:s.default.oneOf(u.MODES),mobileModeInputId:s.default.string,desktopModeInputId:s.default.string},v.defaultProps={active:u.MODE_MOBILE,mobileModeInputId:"",desktopModeInputId:""},t.default=v},24861:(e,t,n)=>{"use strict";Object.defineProperty(t,"__esModule",{value:!0}),t.default=void 0;var i=b(n(98487)),r=b(n(99196)),o=b(n(85890)),s=n(65736),a=n(92819),l=n(42982),d=n(81413),u=n(37188),p=n(23695),c=n(10224),h=n(76990),f=n(99806),g=b(n(64475)),m=b(n(17582)),v=n(95157),E=b(n(90695));function b(e){return e&&e.__esModule?e:{default:e}}function x(){return x=Object.assign?Object.assign.bind():function(e){for(var t=1;t<arguments.length;t++){var n=arguments[t];for(var i in n)Object.prototype.hasOwnProperty.call(n,i)&&(e[i]=n[i])}return e},x.apply(this,arguments)}const y=i.default.legend`
	margin: 0 0 16px;
	padding: 0;
	color: ${u.colors.$color_headings};
	font-size: 12px;
	font-weight: 300;
`,w=(0,i.default)(d.Button)`
	height: 33px;
	border: 1px solid #dbdbdb;
	box-shadow: none;
	font-family: Arial, Roboto-Regular, HelveticaNeue, sans-serif;
`,_=(0,i.default)(w)`
	margin: ${(0,p.getDirectionalStyle)("10px 0 0 4px","10px 4px 0 0")};
	fill: ${u.colors.$color_grey_dark};
	padding-left: 8px;

	& svg {
		${(0,p.getDirectionalStyle)("margin-right","margin-left")}: 7px;
	}
`,M=(0,i.default)(w)`
	margin-top: 24px;
`,S=new RegExp("(%%sep%%|%%sitename%%)","g");class O extends r.default.Component{constructor(e){super(e);const t=this.mapDataToMeasurements(e.data);this.state={isOpen:!e.showCloseButton,activeField:null,hoveredField:null,titleLengthProgress:(0,h.getTitleProgress)(t.filteredSEOTitle),descriptionLengthProgress:(0,h.getDescriptionProgress)(t.description,this.props.date,this.props.isCornerstone,this.props.isTaxonomy,this.props.locale)},this.setFieldFocus=this.setFieldFocus.bind(this),this.unsetFieldFocus=this.unsetFieldFocus.bind(this),this.onChangeMode=this.onChangeMode.bind(this),this.onMouseUp=this.onMouseUp.bind(this),this.onMouseEnter=this.onMouseEnter.bind(this),this.onMouseLeave=this.onMouseLeave.bind(this),this.open=this.open.bind(this),this.close=this.close.bind(this),this.setEditButtonRef=this.setEditButtonRef.bind(this),this.handleChange=this.handleChange.bind(this),this.haveReplaceVarsChanged=this.haveReplaceVarsChanged.bind(this)}shallowCompareData(e,t){let n=!1;return e.data.description===t.data.description&&e.data.slug===t.data.slug&&e.data.title===t.data.title&&e.isCornerstone===t.isCornerstone&&e.isTaxonomy===t.isTaxonomy&&e.locale===t.locale||(n=!0),this.haveReplaceVarsChanged(e.replacementVariables,t.replacementVariables)&&(n=!0),n}haveReplaceVarsChanged(e,t){return JSON.stringify(e)!==JSON.stringify(t)}componentDidUpdate(e){if(this.shallowCompareData(this.props,e)){const e=this.mapDataToMeasurements(this.props.data,this.props.replacementVariables);this.setState({titleLengthProgress:(0,h.getTitleProgress)(e.filteredSEOTitle),descriptionLengthProgress:(0,h.getDescriptionProgress)(e.description,this.props.date,this.props.isCornerstone,this.props.isTaxonomy,this.props.locale)}),this.props.onChangeAnalysisData(e)}}handleChange(e,t){this.props.onChange(e,t);const n=this.mapDataToMeasurements({...this.props.data,[e]:t});this.props.onChangeAnalysisData(n)}renderEditor(){const{data:e,descriptionEditorFieldPlaceholder:t,onReplacementVariableSearchChange:n,replacementVariables:i,recommendedReplacementVariables:o,hasPaperStyle:a,showCloseButton:l,idSuffix:d}=this.props,{activeField:u,hoveredField:c,isOpen:h,titleLengthProgress:f,descriptionLengthProgress:g}=this.state;return h?r.default.createElement(r.default.Fragment,null,r.default.createElement(m.default,{data:e,activeField:u,hoveredField:c,onChange:this.handleChange,onFocus:this.setFieldFocus,onBlur:this.unsetFieldFocus,onReplacementVariableSearchChange:n,replacementVariables:i,recommendedReplacementVariables:o,titleLengthProgress:f,descriptionLengthProgress:g,descriptionEditorFieldPlaceholder:t,containerPadding:a?"0 20px":"0",titleInputId:(0,p.join)(["yoast-google-preview-title",d]),slugInputId:(0,p.join)(["yoast-google-preview-slug",d]),descriptionInputId:(0,p.join)(["yoast-google-preview-description",d])}),l&&r.default.createElement(M,{onClick:this.close},(0,s.__)("Close snippet editor","wordpress-seo"))):null}setFieldFocus(e){e=this.mapFieldToEditor(e),this.setState({activeField:e})}unsetFieldFocus(){this.setState({activeField:null})}onChangeMode(e){this.props.onChange("mode",e)}onMouseUp(e){this.state.isOpen?this.setFieldFocus(e):this.open().then(this.setFieldFocus.bind(this,e))}onMouseEnter(e){this.setState({hoveredField:this.mapFieldToEditor(e)})}onMouseLeave(){this.setState({hoveredField:null})}open(){return new Promise((e=>{this.setState({isOpen:!0},e)}))}close(){this.setState({isOpen:!1,activeField:null},(()=>{this._editButton.focus()}))}processReplacementVariables(e,t=this.props.replacementVariables){if(this.props.applyReplacementVariables)return this.props.applyReplacementVariables(e);for(const{name:n,value:i}of t)e=e.replace(new RegExp("%%"+(0,a.escapeRegExp)(n)+"%%","g"),i);return e}mapDataToMeasurements(e,t=this.props.replacementVariables){const{baseUrl:n,mapEditorDataToPreview:i}=this.props;let r=this.processReplacementVariables(e.description,t);r=l.languageProcessing.stripSpaces(r);const o=n.replace(/^https?:\/\//i,""),s=e.title.replace(S,""),a={title:this.processReplacementVariables(e.title,t),url:n+e.slug,description:r,filteredSEOTitle:this.processReplacementVariables(s,t)};return i?i(a,{shortenedBaseUrl:o}):a}mapDataToPreview(e){return{title:e.title,url:e.url,description:e.description}}mapFieldToPreview(e){return"slug"===e&&(e="url"),e}mapFieldToEditor(e){return"url"===e&&(e="slug"),e}setEditButtonRef(e){this._editButton=e}render(){const{data:e,mode:t,date:n,locale:i,keyword:o,wordsToHighlight:a,showCloseButton:l,faviconSrc:u,mobileImageSrc:c,idSuffix:h,shoppingData:f,siteName:m}=this.props,{activeField:v,hoveredField:b,isOpen:w}=this.state,M=this.mapDataToMeasurements(e),S=this.mapDataToPreview(M);return r.default.createElement(d.ErrorBoundary,null,r.default.createElement("div",null,r.default.createElement(y,null,(0,s.__)("Determine how your post should look in the search results.","wordpress-seo")),r.default.createElement(E.default,{onChange:this.onChangeMode,active:t,mobileModeInputId:(0,p.join)(["yoast-google-preview-mode-mobile",h]),desktopModeInputId:(0,p.join)(["yoast-google-preview-mode-desktop",h])}),r.default.createElement(g.default,x({keyword:o,wordsToHighlight:a,mode:t,date:n,siteName:m,activeField:this.mapFieldToPreview(v),hoveredField:this.mapFieldToPreview(b),onMouseEnter:this.onMouseEnter,onMouseLeave:this.onMouseLeave,onMouseUp:this.onMouseUp,locale:i,faviconSrc:u,mobileImageSrc:c,shoppingData:f},S)),l&&r.default.createElement(_,{onClick:w?this.close:this.open,"aria-expanded":w,ref:this.setEditButtonRef},r.default.createElement(d.SvgIcon,{icon:"edit"}),(0,s.__)("Edit snippet","wordpress-seo")),this.renderEditor()))}}O.propTypes={onReplacementVariableSearchChange:o.default.func,replacementVariables:c.replacementVariablesShape,recommendedReplacementVariables:c.recommendedReplacementVariablesShape,data:o.default.shape({title:o.default.string.isRequired,slug:o.default.string.isRequired,description:o.default.string.isRequired}).isRequired,descriptionEditorFieldPlaceholder:o.default.string,baseUrl:o.default.string.isRequired,mode:o.default.oneOf(f.MODES),date:o.default.string,onChange:o.default.func.isRequired,onChangeAnalysisData:o.default.func,titleLengthProgress:v.lengthProgressShape,descriptionLengthProgress:v.lengthProgressShape,applyReplacementVariables:o.default.func,mapEditorDataToPreview:o.default.func,keyword:o.default.string,wordsToHighlight:o.default.array,locale:o.default.string,hasPaperStyle:o.default.bool,showCloseButton:o.default.bool,faviconSrc:o.default.string,mobileImageSrc:o.default.string,idSuffix:o.default.string,shoppingData:o.default.object,isCornerstone:o.default.bool,isTaxonomy:o.default.bool,siteName:o.default.string.isRequired},O.defaultProps={mode:f.DEFAULT_MODE,date:"",wordsToHighlight:[],onReplacementVariableSearchChange:null,replacementVariables:[],recommendedReplacementVariables:[],titleLengthProgress:{max:600,actual:0,score:0},descriptionLengthProgress:{max:156,actual:0,score:0},applyReplacementVariables:null,mapEditorDataToPreview:null,keyword:"",locale:"en",descriptionEditorFieldPlaceholder:"",onChangeAnalysisData:a.noop,hasPaperStyle:!0,showCloseButton:!0,faviconSrc:"",mobileImageSrc:"",idSuffix:"",shoppingData:{},isCornerstone:!1,isTaxonomy:!1},t.default=O},17582:(e,t,n)=>{"use strict";Object.defineProperty(t,"__esModule",{value:!0}),t.default=void 0;var i=c(n(99196)),r=c(n(98487)),o=c(n(85890)),s=c(n(12049)),a=n(65736),l=n(37188),d=n(10224),u=n(81413),p=n(95157);function c(e){return e&&e.__esModule?e:{default:e}}const h=r.default.input`
	border: none;
	width: 100%;
	height: inherit;
	line-height: 1.71428571; // 24px based on 14px font-size
	font-family: inherit;
	font-size: inherit;
	color: inherit;

	&:focus {
		outline: 0;
	}
`,f=(0,l.withCaretStyles)(u.VariableEditorInputContainer);class g extends i.default.Component{constructor(e){super(e),this.elements={title:null,slug:null,description:null},this.uniqueId=(0,s.default)("snippet-editor-field-"),this.setRef=this.setRef.bind(this),this.setTitleRef=this.setTitleRef.bind(this),this.setSlugRef=this.setSlugRef.bind(this),this.setDescriptionRef=this.setDescriptionRef.bind(this),this.triggerReplacementVariableSuggestions=this.triggerReplacementVariableSuggestions.bind(this),this.onFocusTitle=this.onFocusTitle.bind(this),this.onChangeTitle=this.onChangeTitle.bind(this),this.onFocusSlug=this.onFocusSlug.bind(this),this.focusSlug=this.focusSlug.bind(this),this.onChangeSlug=this.onChangeSlug.bind(this),this.onFocusDescription=this.onFocusDescription.bind(this),this.onChangeDescription=this.onChangeDescription.bind(this)}setRef(e,t){this.elements[e]=t}setTitleRef(e){this.setRef("title",e)}setSlugRef(e){this.setRef("slug",e)}setDescriptionRef(e){this.setRef("description",e)}componentDidUpdate(e){e.activeField!==this.props.activeField&&this.focusOnActiveFieldChange()}focusOnActiveFieldChange(){const{activeField:e}=this.props,t=e?this.elements[e]:null;t&&t.focus()}triggerReplacementVariableSuggestions(e){this.elements[e].triggerReplacementVariableSuggestions()}onFocusTitle(){this.props.onFocus("title")}onChangeTitle(e){this.props.onChange("title",e)}onFocusSlug(){this.props.onFocus("slug")}focusSlug(){this.elements.slug.focus()}onChangeSlug(e){this.props.onChange("slug",e.target.value)}onFocusDescription(){this.props.onFocus("description")}onChangeDescription(e){this.props.onChange("description",e)}render(){const{activeField:e,hoveredField:t,onReplacementVariableSearchChange:n,replacementVariables:r,recommendedReplacementVariables:o,titleLengthProgress:s,descriptionLengthProgress:l,onBlur:p,descriptionEditorFieldPlaceholder:c,data:{title:g,slug:m,description:v},containerPadding:E,titleInputId:b,slugInputId:x,descriptionInputId:y}=this.props,w=`${this.uniqueId}-slug`;return i.default.createElement(d.StyledEditor,{padding:E},i.default.createElement(d.ReplacementVariableEditor,{withCaret:!0,label:(0,a.__)("SEO title","wordpress-seo"),onFocus:this.onFocusTitle,onBlur:p,isActive:"title"===e,isHovered:"title"===t,editorRef:this.setTitleRef,replacementVariables:r,recommendedReplacementVariables:o,content:g,onChange:this.onChangeTitle,onSearchChange:n,fieldId:b,type:"title"}),i.default.createElement(u.ProgressBar,{max:s.max,value:s.actual,progressColor:this.getProgressColor(s.score)}),i.default.createElement(u.SimulatedLabel,{id:w,onClick:this.onFocusSlug},(0,a.__)("Slug","wordpress-seo")),i.default.createElement(f,{onClick:this.focusSlug,isActive:"slug"===e,isHovered:"slug"===t},i.default.createElement(h,{value:m,onChange:this.onChangeSlug,onFocus:this.onFocusSlug,onBlur:p,ref:this.setSlugRef,"aria-labelledby":this.uniqueId+"-slug",id:x})),i.default.createElement(d.ReplacementVariableEditor,{withCaret:!0,type:"description",placeholder:c,label:(0,a.__)("Meta description","wordpress-seo"),onFocus:this.onFocusDescription,onBlur:p,isActive:"description"===e,isHovered:"description"===t,editorRef:this.setDescriptionRef,replacementVariables:r,recommendedReplacementVariables:o,content:v,onChange:this.onChangeDescription,onSearchChange:n,fieldId:y}),i.default.createElement(u.ProgressBar,{max:l.max,value:l.actual,progressColor:this.getProgressColor(l.score)}))}getProgressColor(e){return e>=7?l.colors.$color_good:e>=5?l.colors.$color_ok:l.colors.$color_bad}}g.propTypes={replacementVariables:d.replacementVariablesShape,recommendedReplacementVariables:d.recommendedReplacementVariablesShape,onChange:o.default.func.isRequired,onFocus:o.default.func,onBlur:o.default.func,onReplacementVariableSearchChange:o.default.func,data:o.default.shape({title:o.default.string.isRequired,slug:o.default.string.isRequired,description:o.default.string.isRequired}).isRequired,activeField:o.default.oneOf(["title","slug","description"]),hoveredField:o.default.oneOf(["title","slug","description"]),titleLengthProgress:p.lengthProgressShape,descriptionLengthProgress:p.lengthProgressShape,descriptionEditorFieldPlaceholder:o.default.string,containerPadding:o.default.string,titleInputId:o.default.string,slugInputId:o.default.string,descriptionInputId:o.default.string},g.defaultProps={replacementVariables:[],recommendedReplacementVariables:[],onFocus:()=>{},onBlur:()=>{},onReplacementVariableSearchChange:null,activeField:null,hoveredField:null,titleLengthProgress:{max:600,actual:0,score:0},descriptionLengthProgress:{max:156,actual:0,score:0},descriptionEditorFieldPlaceholder:null,containerPadding:"0 20px",titleInputId:"yoast-google-preview-title",slugInputId:"yoast-google-preview-slug",descriptionInputId:"yoast-google-preview-description"},t.default=g},95157:(e,t,n)=>{"use strict";Object.defineProperty(t,"__esModule",{value:!0}),t.lengthProgressShape=void 0;var i,r=(i=n(85890))&&i.__esModule?i:{default:i};t.lengthProgressShape=r.default.shape({max:r.default.number,actual:r.default.number,score:r.default.number})},12330:(e,t,n)=>{"use strict";Object.defineProperty(t,"__esModule",{value:!0}),t.default=void 0;var i=function(e,t){if(e&&e.__esModule)return e;if(null===e||"object"!=typeof e&&"function"!=typeof e)return{default:e};var n=d(t);if(n&&n.has(e))return n.get(e);var i={__proto__:null},r=Object.defineProperty&&Object.getOwnPropertyDescriptor;for(var o in e)if("default"!==o&&Object.prototype.hasOwnProperty.call(e,o)){var s=r?Object.getOwnPropertyDescriptor(e,o):null;s&&(s.get||s.set)?Object.defineProperty(i,o,s):i[o]=e[o]}return i.default=e,n&&n.set(e,i),i}(n(99196)),r=l(n(98487)),o=l(n(85890)),s=l(n(25853)),a=n(65736);function l(e){return e&&e.__esModule?e:{default:e}}function d(e){if("function"!=typeof WeakMap)return null;var t=new WeakMap,n=new WeakMap;return(d=function(e){return e?n:t})(e)}const u=/mobi/i,p=r.default.div`
	overflow: auto;
	width: ${e=>e.widthValue}px;
	padding: 0 ${e=>e.paddingValue}px;
	max-width: 100%;
	box-sizing: border-box;
`,c=r.default.div`
	width: ${e=>e.widthValue}px;
`,h=r.default.div`
	text-align: center;
	margin: 1rem 0 0.5rem;
`,f=r.default.div`
	display: inline-block;
	box-sizing: border-box;

	&:before{
		display: inline-block;
		margin-right: 10px;
		font-size: 20px;
		line-height: 20px;
		vertical-align: text-top;
		content: "\\21c4";
		box-sizing: border-box;
	}
`;class g extends i.Component{constructor(e){super(e),this.state={showScrollHint:!1,isMobileUserAgent:!1},this.setContainerRef=this.setContainerRef.bind(this),this.determineSize=(0,s.default)(this.determineSize.bind(this),100)}setContainerRef(e){if(!e)return null;this._container=e,this.determineSize(),window.addEventListener("resize",this.determineSize)}determineSize(){var e,t,n,i;this.setState({showScrollHint:(null===(e=this._container)||void 0===e?void 0:e.offsetWidth)!==(null===(t=this._container)||void 0===t?void 0:t.scrollWidth),isMobileUserAgent:u.test(null===(n=window)||void 0===n||null===(i=n.navigator)||void 0===i?void 0:i.userAgent)})}componentWillUnmount(){window.removeEventListener("resize",this.determineSize)}render(){const{width:e,padding:t,children:n,className:r,id:o}=this.props,s=r||o,l=e-2*t;return i.default.createElement("div",{className:`${s}__wrapper`},i.default.createElement(p,{id:o,className:s,widthValue:e,paddingValue:t,ref:this.setContainerRef},i.default.createElement(c,{widthValue:l},n)),this.state.showScrollHint&&i.default.createElement(h,null,i.default.createElement(f,null,this.state.isMobileUserAgent?(0,a.__)("Drag to view the full preview.","wordpress-seo"):(0,a.__)("Scroll to see the preview content.","wordpress-seo"))))}}t.default=g,g.propTypes={id:o.default.string,width:o.default.number.isRequired,padding:o.default.number,children:o.default.node.isRequired,className:o.default.string},g.defaultProps={id:"",padding:0,className:""}},78854:(e,t,n)=>{"use strict";Object.defineProperty(t,"__esModule",{value:!0}),t.default=void 0;var i=p(n(99196)),r=p(n(85890)),o=p(n(98487)),s=p(n(12049)),a=n(81413),l=n(23695),d=n(37188),u=n(30888);function p(e){return e&&e.__esModule?e:{default:e}}const c=o.default.div`
	max-width: 600px;
	font-weight: normal;
	// Don't apply a bottom margin to avoid "jumpiness".
	margin: ${(0,l.getDirectionalStyle)("0 20px 0 25px","0 20px 0 15px")};
`,h=o.default.div`
	max-width: ${e=>e.panelMaxWidth};
`,f=(0,o.default)(a.Button)`
	min-width: 14px;
	min-height: 14px;
	width: 30px;
	height: 30px;
	border-radius: 50%;
	border: 1px solid transparent;
	box-shadow: none;
	display: block;
	margin: -44px -10px 10px 0;
	background-color: transparent;
	float: ${(0,l.getDirectionalStyle)("right","left")};
	padding: ${(0,l.getDirectionalStyle)("3px 0 0 6px","3px 0 0 5px")};

	&:hover {
		color: ${d.colors.$color_blue};
	}
	&:focus {
		border: 1px solid ${d.colors.$color_blue};
		outline: none;
		box-shadow: 0 0 3px ${(0,d.rgba)(d.colors.$color_blue_dark,.8)};

		svg {
			fill: ${d.colors.$color_blue};
			color: ${d.colors.$color_blue};
		}
	}
	&:active {
		box-shadow: none;
	}
`,g=(0,o.default)(a.SvgIcon)`
	&:hover {
		fill: ${d.colors.$color_blue};
	}
`;class m extends i.default.Component{constructor(e){super(e),this.state={isExpanded:!1},this.uniqueId=(0,s.default)("yoast-help-"),this.onButtonClick=this.onButtonClick.bind(this)}onButtonClick(){this.setState((e=>({isExpanded:!e.isExpanded})))}render(){const e=`${this.uniqueId}-panel`,{isExpanded:t}=this.state;return i.default.createElement(c,{className:this.props.className},i.default.createElement(f,{className:this.props.className+"__button",onClick:this.onButtonClick,"aria-expanded":t,"aria-controls":t?e:null,"aria-label":this.props.helpTextButtonLabel},i.default.createElement(g,{size:"16px",color:d.colors.$color_grey_text,icon:"question-circle"})),i.default.createElement(u.YoastSlideToggle,{isOpen:t},i.default.createElement(h,{id:e,className:this.props.className+"__panel",panelMaxWidth:this.props.panelMaxWidth},i.default.createElement(a.HelpText,null,this.props.helpText))))}}m.propTypes={className:r.default.string,helpTextButtonLabel:r.default.string.isRequired,panelMaxWidth:r.default.string,helpText:r.default.oneOfType([r.default.string,r.default.array])},m.defaultProps={className:"yoast-help",panelMaxWidth:null,helpText:""},t.default=m},72676:(e,t,n)=>{"use strict";Object.defineProperty(t,"__esModule",{value:!0}),t.default=void 0;var i=function(e,t){if(e&&e.__esModule)return e;if(null===e||"object"!=typeof e&&"function"!=typeof e)return{default:e};var n=u(t);if(n&&n.has(e))return n.get(e);var i={__proto__:null},r=Object.defineProperty&&Object.getOwnPropertyDescriptor;for(var o in e)if("default"!==o&&Object.prototype.hasOwnProperty.call(e,o)){var s=r?Object.getOwnPropertyDescriptor(e,o):null;s&&(s.get||s.set)?Object.defineProperty(i,o,s):i[o]=e[o]}return i.default=e,n&&n.set(e,i),i}(n(99196)),r=d(n(85890)),o=d(n(98487)),s=n(65736),a=n(92819),l=n(81413);function d(e){return e&&e.__esModule?e:{default:e}}function u(e){if("function"!=typeof WeakMap)return null;var t=new WeakMap,n=new WeakMap;return(u=function(e){return e?n:t})(e)}const p=o.default.span`
	color: #70757a;
	line-height: 1.7;
`;function c(e){const{shoppingData:t}=e,n=(0,s.sprintf)((0,s.__)("Rating: %s","wordpress-seo"),(0,a.round)(2*t.rating,1)+"/10"),r=(0,s.sprintf)((0,s.__)("%s reviews","wordpress-seo"),t.reviewCount);
/* Translators: %s expands to the actual rating, e.g. 8/10. */return i.default.createElement(p,null,t.reviewCount>0&&i.default.createElement(i.Fragment,null,i.default.createElement(l.StarRating,{rating:t.rating}),i.default.createElement("span",null," ",n," · "),i.default.createElement("span",null,r," · ")),t.price&&i.default.createElement(i.Fragment,null,i.default.createElement("span",{dangerouslySetInnerHTML:{__html:t.price}})),t.availability&&i.default.createElement("span",null,` · ${(0,a.capitalize)(t.availability)}`))}t.default=c,c.propTypes={shoppingData:r.default.shape({rating:r.default.number,reviewCount:r.default.number,availability:r.default.string,price:r.default.string}).isRequired}},98463:(e,t,n)=>{"use strict";Object.defineProperty(t,"__esModule",{value:!0}),t.default=void 0;var i=d(n(99196)),r=d(n(85890)),o=d(n(98487)),s=n(65736),a=n(92819),l=n(81413);function d(e){return e&&e.__esModule?e:{default:e}}const u=o.default.div`
	display: flex;
	margin-top: -16px;
	line-height: 1.6;
`,p=o.default.div`
	flex: 1;
	max-width: 50%;
`,c=o.default.div`
	flex: 1;
	max-width: 25%;
`,h=o.default.div`
	color: #70757a;
`;function f(e){const{shoppingData:t}=e;return i.default.createElement(u,null,t.rating>0&&i.default.createElement(p,{className:"yoast-shopping-data-preview__column"},i.default.createElement("div",{className:"yoast-shopping-data-preview__upper"},(0,s.__)("Rating","wordpress-seo")),i.default.createElement(h,{className:"yoast-shopping-data-preview__lower"},i.default.createElement("span",null,(0,a.round)(2*t.rating,1),"/10 "),i.default.createElement(l.StarRating,{rating:t.rating}),i.default.createElement("span",null," (",t.reviewCount,")"))),t.price&&i.default.createElement(c,{className:"yoast-shopping-data-preview__column"},i.default.createElement("div",{className:"yoast-shopping-data-preview__upper"},(0,s.__)("Price","wordpress-seo")),i.default.createElement(h,{className:"yoast-shopping-data-preview__lower",dangerouslySetInnerHTML:{__html:t.price}})),t.availability&&i.default.createElement(c,{className:"yoast-shopping-data-preview__column"},i.default.createElement("div",{className:"yoast-shopping-data-preview__upper"},(0,s.__)("Availability","wordpress-seo")),i.default.createElement(h,{className:"yoast-shopping-data-preview__lower"},(0,a.capitalize)(t.availability))))}t.default=f,f.propTypes={shoppingData:r.default.shape({rating:r.default.number,reviewCount:r.default.number,availability:r.default.string,price:r.default.string}).isRequired}},64475:(e,t,n)=>{"use strict";Object.defineProperty(t,"__esModule",{value:!0}),t.default=void 0;var i=function(e,t){if(e&&e.__esModule)return e;if(null===e||"object"!=typeof e&&"function"!=typeof e)return{default:e};var n=E(t);if(n&&n.has(e))return n.get(e);var i={__proto__:null},r=Object.defineProperty&&Object.getOwnPropertyDescriptor;for(var o in e)if("default"!==o&&Object.prototype.hasOwnProperty.call(e,o)){var s=r?Object.getOwnPropertyDescriptor(e,o):null;s&&(s.get||s.set)?Object.defineProperty(i,o,s):i[o]=e[o]}return i.default=e,n&&n.set(e,i),i}(n(99196)),r=v(n(98487)),o=v(n(85890)),s=v(n(38550)),a=n(65736),l=n(37188),d=n(42982),u=n(23695),p=n(81413),c=n(40412),h=v(n(12330)),f=v(n(72676)),g=v(n(98463)),m=n(99806);function v(e){return e&&e.__esModule?e:{default:e}}function E(e){if("function"!=typeof WeakMap)return null;var t=new WeakMap,n=new WeakMap;return(E=function(e){return e?n:t})(e)}function b(){return b=Object.assign?Object.assign.bind():function(e){for(var t=1;t<arguments.length;t++){var n=arguments[t];for(var i in n)Object.prototype.hasOwnProperty.call(n,i)&&(e[i]=n[i])}return e},b.apply(this,arguments)}const{transliterate:x,createRegexFromArray:y,replaceDiacritics:w}=d.languageProcessing,_=600,M=(0,r.default)(h.default)`
	background-color: #fff;
	font-family: arial, sans-serif;
	box-sizing: border-box;
`,S=r.default.div`
	border-bottom: 1px hidden #fff;
	border-radius: 8px;
	box-shadow: 0 1px 6px rgba(32, 33, 36, 0.28);
	font-family: Arial, Roboto-Regular, HelveticaNeue, sans-serif;
	max-width: ${400}px;
	box-sizing: border-box;
	font-size: 14px;
`,O=r.default.div`
	cursor: pointer;
	position: relative;
`;function C(e,t,n){return(0,r.default)(e)`
		&::before {
			display: block;
			position: absolute;
			top: 0;
			${(0,u.getDirectionalStyle)("left","right")}: ${()=>n===m.MODE_DESKTOP?"-22px":"-40px"};
			width: 22px;
			height: 22px;
			background-image: url( ${(0,u.getDirectionalStyle)((0,l.angleRight)(t),(0,l.angleLeft)(t))} );
			background-size: 24px;
			background-repeat: no-repeat;
			background-position: center;
			content: "";
		}
	`}const D=r.default.div`
	color: ${e=>e.screenMode===m.MODE_DESKTOP?"#1a0dab":"#1558d6"};
	text-decoration: none;
	font-size: ${e=>(e.screenMode,m.MODE_DESKTOP,"20px")};
	line-height: ${e=>e.screenMode===m.MODE_DESKTOP?"1.3":"26px"};
	font-weight: normal;
	margin: 0;
	display: inline-block;
	overflow: hidden;
	max-width: ${_}px;
	vertical-align: top;
	text-overflow: ellipsis;
`,P=(0,r.default)(D)`
	max-width: ${_}px;
	vertical-align: top;
	text-overflow: ellipsis;
`,T=r.default.span`
	display: inline-block;
	max-width: ${240}px;
	overflow: hidden;
	vertical-align: top;

	text-overflow: ellipsis;
	margin-left: 4px;
`,R=r.default.span`
	white-space: nowrap;
`,F=r.default.span`
	display: inline-block;
	max-height: 52px; // max two lines of text
	padding-top: 1px;
	vertical-align: top;
	overflow: hidden;
	text-overflow: ellipsis;
`,A=r.default.div`
	display: inline-block;
	cursor: pointer;
	position: relative;
	width: calc( 100% + 7px );
	white-space: nowrap;
	font-size: 14px;
	line-height: 16px;
	vertical-align: top;
`;A.displayName="BaseUrl";const N=(0,r.default)(A)`
	display: flex;
	align-items: center;
	overflow: hidden;
	justify-content: space-between;
	text-overflow: ellipsis;
	max-width: 100%;
	margin-bottom: 12px;
	padding-top: 1px;
	line-height: 20px;
	vertical-align: bottom;
	column-gap: 12px;
`;N.displayName="BaseUrlOverflowContainer";const j=r.default.span`
	font-size: ${e=>e.screenMode===m.MODE_DESKTOP?"14px":"12px"};
	line-height: ${e=>e.screenMode===m.MODE_DESKTOP?"1.3":"20px"};
	color: ${e=>e.screenMode===m.MODE_DESKTOP?"#4d5156":"#3c4043"};
	flex-grow: 1;
`,I=r.default.span`
	color: ${e=>e.screenMode===m.MODE_DESKTOP?"#4d5156":"#70757a"};
`,k=r.default.div`
width: 28px;
height: 28px;
border-radius: 50px;
display: flex;
align-items: center;
justify-content: center;
background: #f1f3f4;
min-width: 28px;
`;N.displayName="SnippetPreview__BaseUrlOverflowContainer";const L=r.default.div`
	color: ${e=>(e.isDescriptionPlaceholder,"#4d5156")};
	cursor: pointer;
	position: relative;
	max-width: ${_}px;
	padding-top: ${e=>e.screenMode===m.MODE_DESKTOP?"0":"1px"};
	font-size: 14px;
	line-height: 1.58;
`,U=r.default.div`
	color: ${"#3c4043"};
	font-size: 14px;
	cursor: pointer;
	position: relative;
	line-height: 1.4;
	max-width: ${_}px;

	/* Clearing pseudo element to contain the floated image. */
	&:after {
		display: table;
		content: "";
		clear: both;
	}
`,V=r.default.div`
	float: right;
	width: 104px;
	height: 104px;
	margin: 4px 0 4px 16px;
	border-radius: 8px;
	overflow: hidden;
`,B=r.default.img`
	/* Higher specificity is necessary to make sure inherited CSS rules don't alter the image ratio. */
	&&& {
		display: block;
		width: 104px;
		height: 104px;
		object-fit: cover;
	}
`,$=r.default.div`
	padding: 12px 16px;

	&:first-child {
		margin-bottom: -16px;
	}
`,W=r.default.div`
	line-height: 18px;
	font-size: 14px;
	color: black;
	max-width: ${e=>e.screenMode===m.MODE_DESKTOP?"100%":"300px"};
	overflow: hidden;
`,z=r.default.div`
`,H=r.default.span`
	display: inline-block;
	height: 18px;
	line-height: 18px;
	padding-left: 8px;
	vertical-align:bottom;
`,K=r.default.span`
	color: ${e=>e.screenMode===m.MODE_DESKTOP?"#777":"#70757a"};
`,q=r.default.img`
	width: 18px;
	height: 18px;
	margin: 0 5px;
	vertical-align: middle;
`,G=r.default.div`
	background-size: 100% 100%;
	display: inline-block;
	height: 12px;
	width: 12px;
	margin-bottom: -1px;
	opacity: 0.46;
	margin-right: 6px;
	background-image: url( ${"data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAACQAAAAkCAQAAABLCVATAAABr0lEQVR4AbWWJYCUURhFD04Zi7hrLzgFd4nzV9x6wKHinmYb7g4zq71gIw2LWBnZ3Q8df/fh96Tn/t2HVIw4CVKk+fSFNCkSxInxW1pFkhLmoMRjVvFLmkEX5ocuZuBVPw5jv8hh+iEU5QEmuMK+prz7RN3dPMMEGQYzxpH/lGjzou5jgl7mAvOdZfcbF+jbm3MAbFZ7VX9SJnlL1D8UMyjLe+BrAYDb+jJUr59JrlNWRtcqX9GkrPCR4QBAf4qYJAkQoyQrbKKs8RiaEjEI0GvvQ1mLMC9xaBFFBaZS1TbMSwJSomg39erDF+TxpCCNOXjGQJTCvG6qn4ZPzkcxA61Tjhaf4KMj+6Q3XvW6Lopraa8IozRQxIi0a7NXorULc5JyHX/3F3q+0PsFYytVTaGgjz/AvCyiegE69IUsPxHNBMpa738i6tGWlzkAABjKe/+j9YeRHGVd9oWRnwe2ewDASp/L/UqoPQ5AmFeYZMavBP8dAJz0GWWDHQlzXApMdz4KYUfKICcxkKeOfGmQyrIPcgE9m+g/+kT812/Nr3+0kqzitxQjoKXh6xfor99nlEdFjyvH15gAAAAASUVORK5CYII="} );
`,Y=e=>{try{return decodeURI(e)}catch(t){return e}},Q=({screenMode:e})=>i.default.createElement("svg",{xmlns:"http://www.w3.org/2000/svg",viewBox:"0 0 24 24",fill:e===m.MODE_DESKTOP?"#4d5156":"#70757a",style:{width:"18px"}},i.default.createElement("path",{d:"M12 8c1.1 0 2-.9 2-2s-.9-2-2-2-2 .9-2 2 .9 2 2 2zm0 2c-1.1 0-2 .9-2 2s.9 2 2 2 2-.9 2-2-.9-2-2-2zm0 6c-1.1 0-2 .9-2 2s.9 2 2 2 2-.9 2-2-.9-2-2-2z"}));Q.propTypes={screenMode:o.default.string.isRequired};class X extends i.PureComponent{constructor(e){super(e),this.state={title:e.title,description:e.description,isDescriptionPlaceholder:!0},this.setTitleRef=this.setTitleRef.bind(this),this.setDescriptionRef=this.setDescriptionRef.bind(this)}setTitleRef(e){this._titleElement=e}setDescriptionRef(e){this._descriptionElement=e}hasOverflowedContent(e){return Math.abs(e.clientHeight-e.scrollHeight)>=2}fitTitle(){const e=this._titleElement;if(this.hasOverflowedContent(e)){let t=this.state.title;const n=e.clientWidth/3;t.length>n&&(t=t.substring(0,n));const i=this.dropLastWord(t);this.setState({title:i})}}dropLastWord(e){const t=e.split(" ");return t.pop(),t.join(" ")}getTitle(){return this.props.title!==this.state.title?this.state.title+" ...":this.props.title}getDescription(){return this.props.description?(0,s.default)(this.props.description,{length:156,separator:" ",omission:" ..."}):(0,a.__)("Please provide a meta description by editing the snippet below. If you don’t, Google will try to find a relevant part of your post to show in the search results.","wordpress-seo")}renderDate(){const e=this.props.mode===m.MODE_DESKTOP?"—":"－";return this.props.date&&i.default.createElement(K,{screenMode:this.props.mode},this.props.date," ",e," ")}addCaretStyles(e,t){const{mode:n,hoveredField:i,activeField:r}=this.props;return r===e?C(t,l.colors.$color_snippet_active,n):i===e?C(t,l.colors.$color_snippet_hover,n):t}getBreadcrumbs(e){const{breadcrumbs:t}=this.props;let n;try{n=new URL(e)}catch(t){return{hostname:e,breadcrumbs:""}}const i=Y(n.hostname);let r=t||n.pathname.split("/");return r=r.filter((e=>Boolean(e))).map((e=>Y(e))),{hostname:i,breadcrumbs:" › "+r.join(" › ")}}renderUrl(){const{url:e,onMouseUp:t,onMouseEnter:n,onMouseLeave:r,mode:o,faviconSrc:s,siteName:l}=this.props,d=o===m.MODE_MOBILE,{hostname:u,breadcrumbs:c}=this.getBreadcrumbs(e),h=this.addCaretStyles("url",A);return i.default.createElement(i.default.Fragment,null,i.default.createElement(p.ScreenReaderText,null,/* translators: Hidden accessibility text. */
(0,a.__)("Url preview","wordpress-seo")+":"),i.default.createElement(h,null,i.default.createElement(N,{onMouseUp:t.bind(null,"url"),onMouseEnter:n.bind(null,"url"),onMouseLeave:r.bind(null),screenMode:o},i.default.createElement(k,null,i.default.createElement(q,{src:s||"data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAABAAAAAQCAYAAAAf8/9hAAABs0lEQVR4AWL4//8/RRjO8Iucx+noO0MWUDo16FYABMGP6ZfUcRnWtm27jVPbtm3bttuH2t3eFPcY9pLz7NxiLjCyVd87pKnHyqXyxtCs8APd0rnyxiu4qSeA3QEDrAwBDrT1s1Rc/OrjLZwqVmOSu6+Lamcpp2KKMA9PH1BYXMe1mUP5qotvXTywsOEEYHXxrY+3cqk6TMkYpNr2FeoY3KIr0RPtn9wQ2unlA+GMkRw6+9TFw4YTwDUzx/JVvARj9KaedXRO8P5B1Du2S32smzqUrcKGEyA+uAgQjKX7zf0boWHGfn71jIKj2689gxp7OAGShNcBUmLMPVjZuiKcA2vuWHHDCQxMCz629kXAIU4ApY15QwggAFbfOP9DhgBJ+nWVJ1AZAfICAj1pAlY6hCADZnveQf7bQIwzVONGJonhLIlS9gr5mFg44Xd+4S3XHoGNPdJl1INIwKyEgHckEhgTe1bGiFY9GSFBYUwLh1IkiJUbY407E7syBSFxKTszEoiE/YdrgCEayDmtaJwCI9uu8TKMuZSVfSa4BpGgzvomBR/INhLGzrqDotp01ZR8pn/1L0JN9d9XNyx0AAAAAElFTkSuQmCC",alt:""})),i.default.createElement(j,{screenMode:o},i.default.createElement(W,{screenMode:o},l),i.default.createElement(I,{screenMode:o},u),!d&&i.default.createElement(T,null,c),!d&&i.default.createElement(H,null,i.default.createElement(Q,{screenMode:o}))),d&&i.default.createElement(Q,{screenMode:o}))))}componentDidUpdate(e){const t={};this.props.title!==e.title&&(t.title=this.props.title),this.props.description!==e.description&&(t.description=this.props.description),this.setState({...t,isDescriptionPlaceholder:!this.props.description}),this.props.mode===m.MODE_MOBILE&&(clearTimeout(this.fitTitleTimeout),this.fitTitleTimeout=setTimeout((()=>{this.fitTitle()}),10))}componentDidMount(){this.setState({isDescriptionPlaceholder:!this.props.description})}componentWillUnmount(){clearTimeout(this.fitTitleTimeout)}renderDescription(){const{wordsToHighlight:e,locale:t,onMouseUp:n,onMouseLeave:r,onMouseEnter:o,mode:s,mobileImageSrc:a}=this.props,l=this.renderDate(),d={isDescriptionPlaceholder:this.state.isDescriptionPlaceholder,onMouseUp:n.bind(null,"description"),onMouseEnter:o.bind(null,"description"),onMouseLeave:r.bind(null)};if(s===m.MODE_DESKTOP){const n=this.addCaretStyles("description",L);return i.default.createElement(n,b({},d,{ref:this.setDescriptionRef}),l,function(e,t,n,r){if(0===t.length)return n;let o=n;const s=[];t.forEach((function(t){s.push(t);const n=x(t,e);n!==t&&s.push(n)}));const a=y(s,!1,"",!1);return o=o.replace(a,(function(e){return`<strong>${e}</strong>`})),(0,c.safeCreateInterpolateElement)(o,{strong:i.default.createElement("strong",null)})}(t,e,this.getDescription()))}if(s===m.MODE_MOBILE){const e=this.addCaretStyles("description",U);return i.default.createElement(e,d,i.default.createElement(U,{isDescriptionPlaceholder:this.state.isDescriptionPlaceholder,ref:this.setDescriptionRef},a&&i.default.createElement(V,null,i.default.createElement(B,{src:a,alt:""})),l,this.getDescription()))}return null}renderProductData(e){const{mode:t,shoppingData:n}=this.props;if(0===Object.values(n).length)return null;const r={availability:n.availability||"",price:n.price?(0,u.decodeHTML)(n.price):"",rating:n.rating||0,reviewCount:n.reviewCount||0};return t===m.MODE_DESKTOP?i.default.createElement(e,{className:"yoast-shopping-data-preview--desktop"},i.default.createElement(p.ScreenReaderText,null,/* translators: Hidden accessibility text. */
(0,a.__)("Shopping data preview:","wordpress-seo")),i.default.createElement(f.default,{shoppingData:r})):t===m.MODE_MOBILE?i.default.createElement(e,{className:"yoast-shopping-data-preview--mobile"},i.default.createElement(p.ScreenReaderText,null,/* translators: Hidden accessibility text. */
(0,a.__)("Shopping data preview:","wordpress-seo")),i.default.createElement(g.default,{shoppingData:r})):null}render(){const{onMouseUp:e,onMouseLeave:t,onMouseEnter:n,mode:r,isAmp:o}=this.props,{PartContainer:s,Container:l,TitleUnbounded:d,SnippetTitle:u}=this.getPreparedComponents(r),c=r===m.MODE_DESKTOP,h=c||!o?null:i.default.createElement(G,null);return i.default.createElement("section",{className:"yoast-snippet-preview-section"},i.default.createElement(l,{id:"yoast-snippet-preview-container",className:"yoast-snippet-preview-container",width:c?640:null,padding:20},i.default.createElement(s,null,this.renderUrl(),i.default.createElement(p.ScreenReaderText,null,(0,a.__)("SEO title preview","wordpress-seo")+":"),i.default.createElement(u,{onMouseUp:e.bind(null,"title"),onMouseEnter:n.bind(null,"title"),onMouseLeave:t.bind(null)},i.default.createElement(P,{screenMode:r},i.default.createElement(d,{ref:this.setTitleRef},this.getTitle()))),h),i.default.createElement(s,null,i.default.createElement(p.ScreenReaderText,null,(0,a.__)("Meta description preview:","wordpress-seo")),this.renderDescription()),this.renderProductData(s)))}getPreparedComponents(e){return{PartContainer:e===m.MODE_DESKTOP?z:$,Container:e===m.MODE_DESKTOP?M:S,TitleUnbounded:e===m.MODE_DESKTOP?R:F,SnippetTitle:this.addCaretStyles("title",O)}}}t.default=X,X.propTypes={title:o.default.string.isRequired,url:o.default.string.isRequired,siteName:o.default.string.isRequired,description:o.default.string.isRequired,date:o.default.string,breadcrumbs:o.default.array,hoveredField:o.default.string,activeField:o.default.string,keyword:o.default.string,wordsToHighlight:o.default.array,locale:o.default.string,mode:o.default.oneOf(m.MODES),isAmp:o.default.bool,faviconSrc:o.default.string,mobileImageSrc:o.default.string,shoppingData:o.default.object,onMouseUp:o.default.func.isRequired,onHover:o.default.func,onMouseEnter:o.default.func,onMouseLeave:o.default.func},X.defaultProps={date:"",keyword:"",wordsToHighlight:[],breadcrumbs:null,locale:"en",hoveredField:"",activeField:"",mode:m.DEFAULT_MODE,isAmp:!1,faviconSrc:"",mobileImageSrc:"",shoppingData:{},onHover:()=>{},onMouseEnter:()=>{},onMouseLeave:()=>{}}},99806:(e,t)=>{"use strict";Object.defineProperty(t,"__esModule",{value:!0}),t.default=t.MODE_MOBILE=t.MODE_DESKTOP=t.MODES=t.DEFAULT_MODE=void 0;const n=t.MODE_MOBILE="mobile",i=t.MODE_DESKTOP="desktop",r=t.MODES=[i,n],o=t.DEFAULT_MODE=n;t.default={MODE_MOBILE:n,MODE_DESKTOP:i,MODES:r,DEFAULT_MODE:o}},98141:(e,t,n)=>{"use strict";var i=n(64836);t.__esModule=!0,t.default=function(e,t){e.classList?e.classList.add(t):(0,r.default)(e,t)||("string"==typeof e.className?e.className=e.className+" "+t:e.setAttribute("class",(e.className&&e.className.baseVal||"")+" "+t))};var r=i(n(90404));e.exports=t.default},90404:(e,t)=>{"use strict";t.__esModule=!0,t.default=function(e,t){return e.classList?!!t&&e.classList.contains(t):-1!==(" "+(e.className.baseVal||e.className)+" ").indexOf(" "+t+" ")},e.exports=t.default},10602:e=>{"use strict";function t(e,t){return e.replace(new RegExp("(^|\\s)"+t+"(?:\\s|$)","g"),"$1").replace(/\s+/g," ").replace(/^\s*|\s*$/g,"")}e.exports=function(e,n){e.classList?e.classList.remove(n):"string"==typeof e.className?e.className=t(e.className,n):e.setAttribute("class",t(e.className&&e.className.baseVal||"",n))}},46871:(e,t,n)=>{"use strict";function i(){var e=this.constructor.getDerivedStateFromProps(this.props,this.state);null!=e&&this.setState(e)}function r(e){this.setState(function(t){var n=this.constructor.getDerivedStateFromProps(e,t);return null!=n?n:null}.bind(this))}function o(e,t){try{var n=this.props,i=this.state;this.props=e,this.state=t,this.__reactInternalSnapshotFlag=!0,this.__reactInternalSnapshot=this.getSnapshotBeforeUpdate(n,i)}finally{this.props=n,this.state=i}}function s(e){var t=e.prototype;if(!t||!t.isReactComponent)throw new Error("Can only polyfill class components");if("function"!=typeof e.getDerivedStateFromProps&&"function"!=typeof t.getSnapshotBeforeUpdate)return e;var n=null,s=null,a=null;if("function"==typeof t.componentWillMount?n="componentWillMount":"function"==typeof t.UNSAFE_componentWillMount&&(n="UNSAFE_componentWillMount"),"function"==typeof t.componentWillReceiveProps?s="componentWillReceiveProps":"function"==typeof t.UNSAFE_componentWillReceiveProps&&(s="UNSAFE_componentWillReceiveProps"),"function"==typeof t.componentWillUpdate?a="componentWillUpdate":"function"==typeof t.UNSAFE_componentWillUpdate&&(a="UNSAFE_componentWillUpdate"),null!==n||null!==s||null!==a){var l=e.displayName||e.name,d="function"==typeof e.getDerivedStateFromProps?"getDerivedStateFromProps()":"getSnapshotBeforeUpdate()";throw Error("Unsafe legacy lifecycles will not be called for components using new component APIs.\n\n"+l+" uses "+d+" but also contains the following legacy lifecycles:"+(null!==n?"\n  "+n:"")+(null!==s?"\n  "+s:"")+(null!==a?"\n  "+a:"")+"\n\nThe above lifecycles should be removed. Learn more about this warning here:\nhttps://fb.me/react-async-component-lifecycle-hooks")}if("function"==typeof e.getDerivedStateFromProps&&(t.componentWillMount=i,t.componentWillReceiveProps=r),"function"==typeof t.getSnapshotBeforeUpdate){if("function"!=typeof t.componentDidUpdate)throw new Error("Cannot polyfill getSnapshotBeforeUpdate() for components that do not define componentDidUpdate() on the prototype");t.componentWillUpdate=o;var u=t.componentDidUpdate;t.componentDidUpdate=function(e,t,n){var i=this.__reactInternalSnapshotFlag?this.__reactInternalSnapshot:n;u.call(this,e,t,i)}}return e}n.r(t),n.d(t,{polyfill:()=>s}),i.__suppressDeprecationWarning=!0,r.__suppressDeprecationWarning=!0,o.__suppressDeprecationWarning=!0},80129:(e,t,n)=>{"use strict";t.__esModule=!0,t.default=void 0,function(e){if(e&&e.__esModule)return e;var t={};if(null!=e)for(var n in e)if(Object.prototype.hasOwnProperty.call(e,n)){var i=Object.defineProperty&&Object.getOwnPropertyDescriptor?Object.getOwnPropertyDescriptor(e,n):{};i.get||i.set?Object.defineProperty(t,n,i):t[n]=e[n]}t.default=e}(n(85890));var i=a(n(98141)),r=a(n(10602)),o=a(n(99196)),s=a(n(60644));function a(e){return e&&e.__esModule?e:{default:e}}function l(){return l=Object.assign||function(e){for(var t=1;t<arguments.length;t++){var n=arguments[t];for(var i in n)Object.prototype.hasOwnProperty.call(n,i)&&(e[i]=n[i])}return e},l.apply(this,arguments)}n(54726);var d=function(e,t){return e&&t&&t.split(" ").forEach((function(t){return(0,i.default)(e,t)}))},u=function(e,t){return e&&t&&t.split(" ").forEach((function(t){return(0,r.default)(e,t)}))},p=function(e){var t,n;function i(){for(var t,n=arguments.length,i=new Array(n),r=0;r<n;r++)i[r]=arguments[r];return(t=e.call.apply(e,[this].concat(i))||this).onEnter=function(e,n){var i=t.getClassNames(n?"appear":"enter").className;t.removeClasses(e,"exit"),d(e,i),t.props.onEnter&&t.props.onEnter(e,n)},t.onEntering=function(e,n){var i=t.getClassNames(n?"appear":"enter").activeClassName;t.reflowAndAddClass(e,i),t.props.onEntering&&t.props.onEntering(e,n)},t.onEntered=function(e,n){var i=t.getClassNames("appear").doneClassName,r=t.getClassNames("enter").doneClassName,o=n?i+" "+r:r;t.removeClasses(e,n?"appear":"enter"),d(e,o),t.props.onEntered&&t.props.onEntered(e,n)},t.onExit=function(e){var n=t.getClassNames("exit").className;t.removeClasses(e,"appear"),t.removeClasses(e,"enter"),d(e,n),t.props.onExit&&t.props.onExit(e)},t.onExiting=function(e){var n=t.getClassNames("exit").activeClassName;t.reflowAndAddClass(e,n),t.props.onExiting&&t.props.onExiting(e)},t.onExited=function(e){var n=t.getClassNames("exit").doneClassName;t.removeClasses(e,"exit"),d(e,n),t.props.onExited&&t.props.onExited(e)},t.getClassNames=function(e){var n=t.props.classNames,i="string"==typeof n,r=i?(i&&n?n+"-":"")+e:n[e];return{className:r,activeClassName:i?r+"-active":n[e+"Active"],doneClassName:i?r+"-done":n[e+"Done"]}},t}n=e,(t=i).prototype=Object.create(n.prototype),t.prototype.constructor=t,t.__proto__=n;var r=i.prototype;return r.removeClasses=function(e,t){var n=this.getClassNames(t),i=n.className,r=n.activeClassName,o=n.doneClassName;i&&u(e,i),r&&u(e,r),o&&u(e,o)},r.reflowAndAddClass=function(e,t){t&&(e&&e.scrollTop,d(e,t))},r.render=function(){var e=l({},this.props);return delete e.classNames,o.default.createElement(s.default,l({},e,{onEnter:this.onEnter,onEntered:this.onEntered,onEntering:this.onEntering,onExit:this.onExit,onExiting:this.onExiting,onExited:this.onExited}))},i}(o.default.Component);p.defaultProps={classNames:""},p.propTypes={};var c=p;t.default=c,e.exports=t.default},26093:(e,t,n)=>{"use strict";t.__esModule=!0,t.default=void 0,s(n(85890));var i=s(n(99196)),r=n(91850),o=s(n(92381));function s(e){return e&&e.__esModule?e:{default:e}}var a=function(e){var t,n;function s(){for(var t,n=arguments.length,i=new Array(n),r=0;r<n;r++)i[r]=arguments[r];return(t=e.call.apply(e,[this].concat(i))||this).handleEnter=function(){for(var e=arguments.length,n=new Array(e),i=0;i<e;i++)n[i]=arguments[i];return t.handleLifecycle("onEnter",0,n)},t.handleEntering=function(){for(var e=arguments.length,n=new Array(e),i=0;i<e;i++)n[i]=arguments[i];return t.handleLifecycle("onEntering",0,n)},t.handleEntered=function(){for(var e=arguments.length,n=new Array(e),i=0;i<e;i++)n[i]=arguments[i];return t.handleLifecycle("onEntered",0,n)},t.handleExit=function(){for(var e=arguments.length,n=new Array(e),i=0;i<e;i++)n[i]=arguments[i];return t.handleLifecycle("onExit",1,n)},t.handleExiting=function(){for(var e=arguments.length,n=new Array(e),i=0;i<e;i++)n[i]=arguments[i];return t.handleLifecycle("onExiting",1,n)},t.handleExited=function(){for(var e=arguments.length,n=new Array(e),i=0;i<e;i++)n[i]=arguments[i];return t.handleLifecycle("onExited",1,n)},t}n=e,(t=s).prototype=Object.create(n.prototype),t.prototype.constructor=t,t.__proto__=n;var a=s.prototype;return a.handleLifecycle=function(e,t,n){var o,s=this.props.children,a=i.default.Children.toArray(s)[t];a.props[e]&&(o=a.props)[e].apply(o,n),this.props[e]&&this.props[e]((0,r.findDOMNode)(this))},a.render=function(){var e=this.props,t=e.children,n=e.in,r=function(e,t){if(null==e)return{};var n,i,r={},o=Object.keys(e);for(i=0;i<o.length;i++)n=o[i],t.indexOf(n)>=0||(r[n]=e[n]);return r}(e,["children","in"]),s=i.default.Children.toArray(t),a=s[0],l=s[1];return delete r.onEnter,delete r.onEntering,delete r.onEntered,delete r.onExit,delete r.onExiting,delete r.onExited,i.default.createElement(o.default,r,n?i.default.cloneElement(a,{key:"first",onEnter:this.handleEnter,onEntering:this.handleEntering,onEntered:this.handleEntered}):i.default.cloneElement(l,{key:"second",onEnter:this.handleExit,onEntering:this.handleExiting,onEntered:this.handleExited}))},s}(i.default.Component);a.propTypes={};var l=a;t.default=l,e.exports=t.default},60644:(e,t,n)=>{"use strict";t.__esModule=!0,t.default=t.EXITING=t.ENTERED=t.ENTERING=t.EXITED=t.UNMOUNTED=void 0;var i=function(e){if(e&&e.__esModule)return e;var t={};if(null!=e)for(var n in e)if(Object.prototype.hasOwnProperty.call(e,n)){var i=Object.defineProperty&&Object.getOwnPropertyDescriptor?Object.getOwnPropertyDescriptor(e,n):{};i.get||i.set?Object.defineProperty(t,n,i):t[n]=e[n]}return t.default=e,t}(n(85890)),r=a(n(99196)),o=a(n(91850)),s=n(46871);function a(e){return e&&e.__esModule?e:{default:e}}n(54726);var l="unmounted";t.UNMOUNTED=l;var d="exited";t.EXITED=d;var u="entering";t.ENTERING=u;var p="entered";t.ENTERED=p;var c="exiting";t.EXITING=c;var h=function(e){var t,n;function i(t,n){var i;i=e.call(this,t,n)||this;var r,o=n.transitionGroup,s=o&&!o.isMounting?t.enter:t.appear;return i.appearStatus=null,t.in?s?(r=d,i.appearStatus=u):r=p:r=t.unmountOnExit||t.mountOnEnter?l:d,i.state={status:r},i.nextCallback=null,i}n=e,(t=i).prototype=Object.create(n.prototype),t.prototype.constructor=t,t.__proto__=n;var s=i.prototype;return s.getChildContext=function(){return{transitionGroup:null}},i.getDerivedStateFromProps=function(e,t){return e.in&&t.status===l?{status:d}:null},s.componentDidMount=function(){this.updateStatus(!0,this.appearStatus)},s.componentDidUpdate=function(e){var t=null;if(e!==this.props){var n=this.state.status;this.props.in?n!==u&&n!==p&&(t=u):n!==u&&n!==p||(t=c)}this.updateStatus(!1,t)},s.componentWillUnmount=function(){this.cancelNextCallback()},s.getTimeouts=function(){var e,t,n,i=this.props.timeout;return e=t=n=i,null!=i&&"number"!=typeof i&&(e=i.exit,t=i.enter,n=void 0!==i.appear?i.appear:t),{exit:e,enter:t,appear:n}},s.updateStatus=function(e,t){if(void 0===e&&(e=!1),null!==t){this.cancelNextCallback();var n=o.default.findDOMNode(this);t===u?this.performEnter(n,e):this.performExit(n)}else this.props.unmountOnExit&&this.state.status===d&&this.setState({status:l})},s.performEnter=function(e,t){var n=this,i=this.props.enter,r=this.context.transitionGroup?this.context.transitionGroup.isMounting:t,o=this.getTimeouts(),s=r?o.appear:o.enter;t||i?(this.props.onEnter(e,r),this.safeSetState({status:u},(function(){n.props.onEntering(e,r),n.onTransitionEnd(e,s,(function(){n.safeSetState({status:p},(function(){n.props.onEntered(e,r)}))}))}))):this.safeSetState({status:p},(function(){n.props.onEntered(e)}))},s.performExit=function(e){var t=this,n=this.props.exit,i=this.getTimeouts();n?(this.props.onExit(e),this.safeSetState({status:c},(function(){t.props.onExiting(e),t.onTransitionEnd(e,i.exit,(function(){t.safeSetState({status:d},(function(){t.props.onExited(e)}))}))}))):this.safeSetState({status:d},(function(){t.props.onExited(e)}))},s.cancelNextCallback=function(){null!==this.nextCallback&&(this.nextCallback.cancel(),this.nextCallback=null)},s.safeSetState=function(e,t){t=this.setNextCallback(t),this.setState(e,t)},s.setNextCallback=function(e){var t=this,n=!0;return this.nextCallback=function(i){n&&(n=!1,t.nextCallback=null,e(i))},this.nextCallback.cancel=function(){n=!1},this.nextCallback},s.onTransitionEnd=function(e,t,n){this.setNextCallback(n);var i=null==t&&!this.props.addEndListener;e&&!i?(this.props.addEndListener&&this.props.addEndListener(e,this.nextCallback),null!=t&&setTimeout(this.nextCallback,t)):setTimeout(this.nextCallback,0)},s.render=function(){var e=this.state.status;if(e===l)return null;var t=this.props,n=t.children,i=function(e,t){if(null==e)return{};var n,i,r={},o=Object.keys(e);for(i=0;i<o.length;i++)n=o[i],t.indexOf(n)>=0||(r[n]=e[n]);return r}(t,["children"]);if(delete i.in,delete i.mountOnEnter,delete i.unmountOnExit,delete i.appear,delete i.enter,delete i.exit,delete i.timeout,delete i.addEndListener,delete i.onEnter,delete i.onEntering,delete i.onEntered,delete i.onExit,delete i.onExiting,delete i.onExited,"function"==typeof n)return n(e,i);var o=r.default.Children.only(n);return r.default.cloneElement(o,i)},i}(r.default.Component);function f(){}h.contextTypes={transitionGroup:i.object},h.childContextTypes={transitionGroup:function(){}},h.propTypes={},h.defaultProps={in:!1,mountOnEnter:!1,unmountOnExit:!1,appear:!1,enter:!0,exit:!0,onEnter:f,onEntering:f,onEntered:f,onExit:f,onExiting:f,onExited:f},h.UNMOUNTED=0,h.EXITED=1,h.ENTERING=2,h.ENTERED=3,h.EXITING=4;var g=(0,s.polyfill)(h);t.default=g},92381:(e,t,n)=>{"use strict";t.__esModule=!0,t.default=void 0;var i=a(n(85890)),r=a(n(99196)),o=n(46871),s=n(40537);function a(e){return e&&e.__esModule?e:{default:e}}function l(){return l=Object.assign||function(e){for(var t=1;t<arguments.length;t++){var n=arguments[t];for(var i in n)Object.prototype.hasOwnProperty.call(n,i)&&(e[i]=n[i])}return e},l.apply(this,arguments)}function d(e){if(void 0===e)throw new ReferenceError("this hasn't been initialised - super() hasn't been called");return e}var u=Object.values||function(e){return Object.keys(e).map((function(t){return e[t]}))},p=function(e){var t,n;function i(t,n){var i,r=(i=e.call(this,t,n)||this).handleExited.bind(d(d(i)));return i.state={handleExited:r,firstRender:!0},i}n=e,(t=i).prototype=Object.create(n.prototype),t.prototype.constructor=t,t.__proto__=n;var o=i.prototype;return o.getChildContext=function(){return{transitionGroup:{isMounting:!this.appeared}}},o.componentDidMount=function(){this.appeared=!0,this.mounted=!0},o.componentWillUnmount=function(){this.mounted=!1},i.getDerivedStateFromProps=function(e,t){var n=t.children,i=t.handleExited;return{children:t.firstRender?(0,s.getInitialChildMapping)(e,i):(0,s.getNextChildMapping)(e,n,i),firstRender:!1}},o.handleExited=function(e,t){var n=(0,s.getChildMapping)(this.props.children);e.key in n||(e.props.onExited&&e.props.onExited(t),this.mounted&&this.setState((function(t){var n=l({},t.children);return delete n[e.key],{children:n}})))},o.render=function(){var e=this.props,t=e.component,n=e.childFactory,i=function(e,t){if(null==e)return{};var n,i,r={},o=Object.keys(e);for(i=0;i<o.length;i++)n=o[i],t.indexOf(n)>=0||(r[n]=e[n]);return r}(e,["component","childFactory"]),o=u(this.state.children).map(n);return delete i.appear,delete i.enter,delete i.exit,null===t?o:r.default.createElement(t,i,o)},i}(r.default.Component);p.childContextTypes={transitionGroup:i.default.object.isRequired},p.propTypes={},p.defaultProps={component:"div",childFactory:function(e){return e}};var c=(0,o.polyfill)(p);t.default=c,e.exports=t.default},64317:(e,t,n)=>{"use strict";var i=a(n(80129)),r=a(n(26093)),o=a(n(92381)),s=a(n(60644));function a(e){return e&&e.__esModule?e:{default:e}}e.exports={Transition:s.default,TransitionGroup:o.default,ReplaceTransition:r.default,CSSTransition:i.default}},40537:(e,t,n)=>{"use strict";t.__esModule=!0,t.getChildMapping=r,t.mergeChildMappings=o,t.getInitialChildMapping=function(e,t){return r(e.children,(function(n){return(0,i.cloneElement)(n,{onExited:t.bind(null,n),in:!0,appear:s(n,"appear",e),enter:s(n,"enter",e),exit:s(n,"exit",e)})}))},t.getNextChildMapping=function(e,t,n){var a=r(e.children),l=o(t,a);return Object.keys(l).forEach((function(r){var o=l[r];if((0,i.isValidElement)(o)){var d=r in t,u=r in a,p=t[r],c=(0,i.isValidElement)(p)&&!p.props.in;!u||d&&!c?u||!d||c?u&&d&&(0,i.isValidElement)(p)&&(l[r]=(0,i.cloneElement)(o,{onExited:n.bind(null,o),in:p.props.in,exit:s(o,"exit",e),enter:s(o,"enter",e)})):l[r]=(0,i.cloneElement)(o,{in:!1}):l[r]=(0,i.cloneElement)(o,{onExited:n.bind(null,o),in:!0,exit:s(o,"exit",e),enter:s(o,"enter",e)})}})),l};var i=n(99196);function r(e,t){var n=Object.create(null);return e&&i.Children.map(e,(function(e){return e})).forEach((function(e){n[e.key]=function(e){return t&&(0,i.isValidElement)(e)?t(e):e}(e)})),n}function o(e,t){function n(n){return n in t?t[n]:e[n]}e=e||{},t=t||{};var i,r=Object.create(null),o=[];for(var s in e)s in t?o.length&&(r[s]=o,o=[]):o.push(s);var a={};for(var l in t){if(r[l])for(i=0;i<r[l].length;i++){var d=r[l][i];a[r[l][i]]=n(d)}a[l]=n(l)}for(i=0;i<o.length;i++)a[o[i]]=n(o[i]);return a}function s(e,t,n){return null!=n[t]?n[t]:e.props[t]}},54726:(e,t,n)=>{"use strict";var i;t.__esModule=!0,t.classNamesShape=t.timeoutsShape=void 0,(i=n(85890))&&i.__esModule,t.timeoutsShape=null,t.classNamesShape=null},99196:e=>{"use strict";e.exports=window.React},91850:e=>{"use strict";e.exports=window.ReactDOM},92819:e=>{"use strict";e.exports=window.lodash},25853:e=>{"use strict";e.exports=window.lodash.debounce},38550:e=>{"use strict";e.exports=window.lodash.truncate},12049:e=>{"use strict";e.exports=window.lodash.uniqueId},69307:e=>{"use strict";e.exports=window.wp.element},65736:e=>{"use strict";e.exports=window.wp.i18n},42982:e=>{"use strict";e.exports=window.yoast.analysis},81413:e=>{"use strict";e.exports=window.yoast.componentsNew},23695:e=>{"use strict";e.exports=window.yoast.helpers},85890:e=>{"use strict";e.exports=window.yoast.propTypes},10224:e=>{"use strict";e.exports=window.yoast.replacementVariableEditor},37188:e=>{"use strict";e.exports=window.yoast.styleGuide},98487:e=>{"use strict";e.exports=window.yoast.styledComponents},64836:e=>{e.exports=function(e){return e&&e.__esModule?e:{default:e}},e.exports.__esModule=!0,e.exports.default=e.exports}},t={};function n(i){var r=t[i];if(void 0!==r)return r.exports;var o=t[i]={exports:{}};return e[i](o,o.exports,n),o.exports}n.d=(e,t)=>{for(var i in t)n.o(t,i)&&!n.o(e,i)&&Object.defineProperty(e,i,{enumerable:!0,get:t[i]})},n.o=(e,t)=>Object.prototype.hasOwnProperty.call(e,t),n.r=e=>{"undefined"!=typeof Symbol&&Symbol.toStringTag&&Object.defineProperty(e,Symbol.toStringTag,{value:"Module"}),Object.defineProperty(e,"__esModule",{value:!0})};var i={};(()=>{"use strict";var e=i;Object.defineProperty(e,"__esModule",{value:!0}),Object.defineProperty(e,"FixedWidthContainer",{enumerable:!0,get:function(){return t.default}}),Object.defineProperty(e,"HelpTextWrapper",{enumerable:!0,get:function(){return r.default}}),Object.defineProperty(e,"SnippetEditor",{enumerable:!0,get:function(){return s.default}}),Object.defineProperty(e,"SnippetPreview",{enumerable:!0,get:function(){return o.default}}),Object.defineProperty(e,"getDescriptionProgress",{enumerable:!0,get:function(){return l.getDescriptionProgress}}),Object.defineProperty(e,"getTitleProgress",{enumerable:!0,get:function(){return l.getTitleProgress}}),Object.defineProperty(e,"lengthProgressShape",{enumerable:!0,get:function(){return a.lengthProgressShape}});var t=d(n(12330)),r=d(n(78854)),o=d(n(64475)),s=d(n(24861)),a=n(95157),l=n(76990);function d(e){return e&&e.__esModule?e:{default:e}}})(),(window.yoast=window.yoast||{}).searchMetadataPreviews=i})();