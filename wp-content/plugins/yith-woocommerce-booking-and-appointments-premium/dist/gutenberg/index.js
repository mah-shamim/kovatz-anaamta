!function(){var e={703:function(e,t,o){"use strict";var r=o(414);function c(){}function s(){}s.resetWarningCache=c,e.exports=function(){function e(e,t,o,c,s,n){if(n!==r){var a=new Error("Calling PropTypes validators directly is not supported by the `prop-types` package. Use PropTypes.checkPropTypes() to call them. Read more at http://fb.me/use-check-prop-types");throw a.name="Invariant Violation",a}}function t(){return e}e.isRequired=e;var o={array:e,bool:e,func:e,number:e,object:e,string:e,symbol:e,any:e,arrayOf:t,element:e,elementType:e,instanceOf:t,node:e,objectOf:t,oneOf:t,oneOfType:t,shape:t,exact:t,checkPropTypes:s,resetWarningCache:c};return o.PropTypes=o,o}},697:function(e,t,o){e.exports=o(703)()},414:function(e){"use strict";e.exports="SECRET_DO_NOT_PASS_THIS_OR_YOU_WILL_BE_FIRED"}},t={};function o(r){var c=t[r];if(void 0!==c)return c.exports;var s=t[r]={exports:{}};return e[r](s,s.exports,o),s.exports}o.n=function(e){var t=e&&e.__esModule?function(){return e.default}:function(){return e};return o.d(t,{a:t}),t},o.d=function(e,t){for(var r in t)o.o(t,r)&&!o.o(e,r)&&Object.defineProperty(e,r,{enumerable:!0,get:t[r]})},o.o=function(e,t){return Object.prototype.hasOwnProperty.call(e,t)},function(){"use strict";var e=window.wp.blocks,t=window.wp.i18n,r=window.wp.element;const c=(0,r.createElement)("svg",null,(0,r.createElement)("path",{width:"22",height:"22",d:"M 18.24 7.628 C 17.291 8.284 16.076 8.971 14.587 9.688 C 15.344 7.186 15.765 4.851 15.849 2.684 C 15.912 0.939 15.133 0.045 13.514 0.003 C 11.558 -0.06 10.275 1.033 9.665 3.284 C 10.007 3.137 10.359 3.063 10.723 3.063 C 11.021 3.063 11.267 3.184 11.459 3.426 C 11.651 3.668 11.736 3.947 11.715 4.262 C 11.695 5.082 11.276 5.961 10.46 6.896 C 9.644 7.833 8.918 8.3 8.282 8.3 C 7.837 8.3 7.625 7.922 7.646 7.165 C 7.667 6.765 7.804 5.955 8.056 4.735 C 8.287 3.579 8.403 2.801 8.403 2.401 C 8.403 1.707 8.224 1.144 7.867 0.713 C 7.509 0.282 6.994 0.098 6.321 0.161 C 5.858 0.203 5.175 0.624 4.27 1.422 C 3.596 2.035 2.923 2.644 2.25 3.254 L 2.976 4.106 C 3.564 3.664 3.922 3.443 4.048 3.443 C 4.448 3.443 4.637 3.717 4.617 4.263 C 4.617 4.306 4.427 4.968 4.049 6.251 C 3.671 7.534 3.471 8.491 3.449 9.122 C 3.407 9.985 3.565 10.647 3.924 11.109 C 4.367 11.677 5.106 11.919 6.142 11.835 C 7.366 11.751 8.591 11.298 9.816 10.479 C 10.323 10.142 10.808 9.753 11.273 9.311 C 11.105 10.153 10.905 10.868 10.673 11.457 C 8.402 12.487 6.762 13.37 5.752 14.107 C 4.321 15.137 3.554 16.241 3.449 17.419 C 3.259 19.459 4.29 20.479 6.541 20.479 C 8.055 20.479 9.517 19.554 10.926 17.703 C 12.125 16.126 13.166 14.022 14.049 11.394 C 15.578 10.635 16.87 9.892 17.928 9.164 C 17.894 9.409 18.319 7.308 18.24 7.628 Z  M 7.393 16.095 C 7.056 16.095 6.898 15.947 6.919 15.653 C 6.961 15.106 7.908 14.38 9.759 13.476 C 8.791 15.221 8.002 16.095 7.393 16.095 Z"}));var s=window.wp.components,n=window.wp.blockEditor,a=window.wp.url,l=o(697),i=o.n(l),d=window.wp.compose,u=window.React,m=window.wp.apiFetch,h=o.n(m),p=window.lodash;const g=({queryArgs:e={},...t})=>(e.type="booking",(({selected:e=[],search:t="",queryArgs:o={}})=>{const r=(({selected:e=[],search:t="",queryArgs:o={}})=>{const r=bkBlocks.productCount>100,c={per_page:r?100:0,catalog_visibility:"any",search:t,orderby:"title",order:"asc"},s=[(0,a.addQueryArgs)("/wc/store/products",{...c,...o})];return r&&e.length&&s.push((0,a.addQueryArgs)("/wc/store/products",{catalog_visibility:"any",include:e,per_page:0})),s})({selected:e,search:t,queryArgs:o});return Promise.all(r.map((e=>h()({path:e})))).then((e=>(0,p.uniqBy)((0,p.flatten)(e),"id").map((e=>({...e,parent:0}))))).catch((e=>{throw e}))})({...t,queryArgs:e}));function y(){return y=Object.assign||function(e){for(var t=1;t<arguments.length;t++){var o=arguments[t];for(var r in o)Object.prototype.hasOwnProperty.call(o,r)&&(e[r]=o[r])}return e},y.apply(this,arguments)}const b={clear:(0,t.__)("Clear all","yith-booking-for-woocommerce"),noItems:(0,t.__)("No items found.","yith-booking-for-woocommerce"),noResults:(0,t.__)("No results for %s","yith-booking-for-woocommerce"),search:(0,t.__)("Search for items","yith-booking-for-woocommerce"),selected:e=>(0,t.sprintf)(// translators: Number of items selected from list.
(0,t._n)("%d item selected","%d items selected",e,"yith-booking-for-woocommerce"),e)},f=({className:e,search:o,onSearch:c,onChange:n,selected:a,isLoading:l,list:i,ifFirstLoading:d,messages:m})=>{const[h,p]=(0,u.useState)(!1),g=(0,u.useRef)(),y=(0,u.useRef)(),f={...b,...m},k=e=>{y.current.contains(e.target)||p(!1)};(0,u.useEffect)((()=>(document.addEventListener("mousedown",k),()=>document.removeEventListener("mousedown",k))));const w=i.filter((e=>a.includes(e.id))),_=i.filter((e=>e.name.toLowerCase().indexOf(o.toLowerCase())>=0)),C=e=>{const t=[...a].filter((t=>t!==e));n(t)};return(0,r.createElement)("div",{className:"yith-wcbk-search-list-control"},(0,r.createElement)("div",{className:"yith-wcbk-search-list-control__selected-header"},d?(0,r.createElement)(s.Spinner,null):(0,r.createElement)(r.Fragment,null,(0,r.createElement)("span",{className:"yith-wcbk-search-list-control__selected-header__count"},f.selected(w.length)),(0,r.createElement)(s.Button,{isLink:!0,onClick:()=>{n([])}},f.clear))),(0,r.createElement)("div",{className:"yith-wcbk-search-list-control__selected-items"},w.map((e=>(0,r.createElement)("span",{key:e.id,className:"yith-wcbk-search-list-control__selected-item"},e.name,(0,r.createElement)("span",{className:"yith-wcbk-search-list-control__selected-item__remove yith-icon yith-icon-close",onClick:()=>{C(e.id)}}))))),(0,r.createElement)("div",{className:"yith-wcbk-search-list-control__search-and-suggestions",ref:y},(0,r.createElement)("input",{ref:g,className:"yith-wcbk-search-list-control__search",value:o,onChange:e=>c(e.target.value),onFocus:()=>p(!0),placeholder:f.search}),h&&(0,r.createElement)("div",{className:`yith-wcbk-search-list-control__suggestions ${l&&"loading"} ${!_.length&&"no-results"}`},l?(0,r.createElement)("div",{className:"yith-wcbk-search-list-control__suggestions__message"},(0,t.__)("Loading...","yith-booking-for-woocommerce")):_.length?_.map((e=>{const t=a.includes(e.id),o=t?"selected":"";return(0,r.createElement)("span",{key:e.id,className:`yith-wcbk-search-list-control__suggestion ${o}`,onClick:()=>{var o;t||(o=e.id,n([...a,o]),g.current.focus())}},e.name,t&&(0,r.createElement)("span",{className:"yith-wcbk-search-list-control__suggestion__remove yith-icon yith-icon-close",onClick:()=>{C(e.id)}}))})):(0,r.createElement)("div",{className:"yith-wcbk-search-list-control__suggestions__message"},i.length&&o?(0,t.sprintf)(f.noResults,o):f.noItems))))};f.propTypes={className:i().string,isLoading:i().bool,list:i().arrayOf(i().shape({id:i().number,name:i().string})),messages:i().shape({clear:i().string,noItems:i().string,noResults:i().string,search:i().string,selected:i().func}),onChange:i().func.isRequired,onSearch:i().func,selected:i().array.isRequired,search:i().string,ifFirstLoading:i().bool},f.defaultProps={selected:[],className:"",list:[],onSearch:()=>{},isLoading:!1,ifFirstLoading:!1};var k,w=(k=f,({selected:e,asyncSearch:t,loadItems:o,queryArgs:c,...s})=>{const[n,a]=(0,u.useState)(!0),[l,i]=(0,u.useState)(!0),[m,h]=(0,u.useState)([]),[p,g]=(0,u.useState)(""),b=(0,d.useDebounce)((t=>{o({selected:e,search:t,queryArgs:c||{}}).then((e=>{h(e),a(!1),i(!1)}))}),300);return t&&(0,u.useEffect)((()=>{a(!0),b(p)}),[p]),(0,u.useEffect)((()=>{a(!0);let r={...c||{}};t&&(r.limit=20),o({selected:e,queryArgs:r}).then((e=>{h(e),a(!1),i(!1)}))}),[]),(0,r.createElement)(k,y({selected:e},s,{search:p,onSearch:g,isLoading:n,ifFirstLoading:l,list:m}))});const _=({label:e,className:o,onChange:c,selected:n})=>{const a=bkBlocks.productCount>100,l=`inspector-yith-wcbk-products-control-${(0,d.useInstanceId)(_)}`;return(0,r.createElement)(s.BaseControl,{label:e,id:l,className:`yith-wcbk-products-control__wrap ${o}`},(0,r.createElement)(w,{messages:{clear:(0,t.__)("Clear all","yith-booking-for-woocommerce"),noItems:(0,t.__)("No products found.","yith-booking-for-woocommerce"),noResults:(0,t.__)("No results for %s","yith-booking-for-woocommerce"),search:(0,t.__)("Search products...","yith-booking-for-woocommerce"),selected:e=>(0,t.sprintf)(// translators: Number of products selected from list.
(0,t._n)("%d product selected","%d products selected",e,"yith-booking-for-woocommerce"),e)},onChange:c,selected:n,loadItems:g,asyncSearch:a}))};_.propTypes={onChange:i().func.isRequired,selected:i().array},_.defaultProps={selected:[],label:"",className:""};var C=_;const v=({selected:e=[],search:t="",queryArgs:o={}})=>{const r=(({selected:e=[],search:t="",queryArgs:o={}})=>{const r=bkBlocks.categoryCount>100,c={per_page:r?100:0,hide_empty:!0,search:t,orderby:"name",order:"asc"},s=[(0,a.addQueryArgs)("/wc/store/products/categories",{...c,...o})];return r&&e.length&&s.push((0,a.addQueryArgs)("/wc/store/products/categories",{hide_empty:!0,include:e,per_page:0})),s})({selected:e,search:t,queryArgs:o});return Promise.all(r.map((e=>h()({path:e})))).then((e=>(0,p.uniqBy)((0,p.flatten)(e),"id").map((e=>({...e,parent:0}))))).catch((e=>{throw e}))},E=({label:e,className:o,onChange:c,selected:n})=>{const a=bkBlocks.categoryCount>100,l=`inspector-yith-wcbk-categories-control-${(0,d.useInstanceId)(E)}`;return(0,r.createElement)(s.BaseControl,{label:e,id:l,className:`yith-wcbk-categories-control__wrap ${o}`},(0,r.createElement)(w,{messages:{clear:(0,t.__)("Clear all","yith-booking-for-woocommerce"),noItems:(0,t.__)("No categories found.","yith-booking-for-woocommerce"),noResults:(0,t.__)("No results for %s","yith-booking-for-woocommerce"),search:(0,t.__)("Search categories...","yith-booking-for-woocommerce"),selected:e=>(0,t.sprintf)(// translators: Number of products selected from list.
(0,t._n)("%d category selected","%d categories selected",e,"yith-booking-for-woocommerce"),e)},onChange:c,selected:n,loadItems:v,asyncSearch:a}))};E.propTypes={onChange:i().func.isRequired,selected:i().array},E.defaultProps={selected:[],label:"",className:""};var P=E,B={type:{type:"string",default:"newest"},columns:{type:"number",default:parseInt(bkBlocks.defaultProductsPerRow,10)},rows:{type:"number",default:1},product_ids:{type:"array",default:[]},categories:{type:"array",default:[]}};(0,e.registerBlockType)("yith/wcbk-booking-products",{title:(0,t._x)("Bookable Products","Block name","yith-booking-for-woocommerce"),icon:c,category:"yith-blocks",example:{},attributes:B,edit:function({attributes:e,className:o,setAttributes:c}){const{type:l,columns:i,rows:d,product_ids:u,categories:m}=e,h=(0,a.addQueryArgs)(bkBlocks.siteURL,{"yith-wcbk-block-preview":1,"yith-wcbk-block-preview-nonce":bkBlocks.nonces.previewBookingProducts,attributes:e,block:"booking-products"});return(0,r.createElement)(r.Fragment,null,(0,r.createElement)(n.InspectorControls,null,(0,r.createElement)(s.PanelBody,null,(0,r.createElement)(s.SelectControl,{label:(0,t._x)("Type","Bookable Products block","yith-booking-for-woocommerce"),value:l,onChange:e=>c({type:e}),options:[{label:(0,t._x)("Newest","Bookable Products block types","yith-booking-for-woocommerce"),value:"newest"},{label:(0,t._x)("Hand-picked products","Bookable Products block types","yith-booking-for-woocommerce"),value:"hand-picked"},{label:(0,t._x)("Product categories","Bookable Products block types","yith-booking-for-woocommerce"),value:"categories"},{label:(0,t._x)("Top rated","Bookable Products block types","yith-booking-for-woocommerce"),value:"top-rated"}]})),"hand-picked"===l&&(0,r.createElement)(s.PanelBody,{title:(0,t._x)("Products","Bookable Products block panel title","yith-booking-for-woocommerce")},(0,r.createElement)(C,{selected:u,onChange:e=>{c({product_ids:e})}})),"categories"===l&&(0,r.createElement)(s.PanelBody,{title:(0,t._x)("Categories","Bookable Products block panel title","yith-booking-for-woocommerce")},(0,r.createElement)(P,{selected:m,onChange:e=>{c({categories:e})}})),(0,r.createElement)(s.PanelBody,{title:(0,t._x)("Layout","Bookable Products block panel title","yith-booking-for-woocommerce")},(0,r.createElement)(s.RangeControl,{label:(0,t._x)("Columns","Bookable Products block","yith-booking-for-woocommerce"),value:i,onChange:e=>c({columns:e}),min:1,max:6}),(0,r.createElement)(s.RangeControl,{label:(0,t._x)("Rows","Bookable Products block","yith-booking-for-woocommerce"),value:d,onChange:e=>c({rows:e}),min:1,max:6}))),(0,r.createElement)("div",{className:o},(0,r.createElement)(s.Disabled,null,(0,r.createElement)("iframe",{className:"yith-wcbk-booking-products-block__edit-preview-iframe",title:(0,t._x)("Bookable Products","Block name","yith-booking-for-woocommerce"),src:h,onLoad:e=>{const t=e.target;t.contentDocument.body.style.overflow="hidden";const o=Math.max(t.contentDocument.documentElement.offsetHeight,t.contentDocument.body.offsetHeight);t.style.height=`${o}px`},height:100}))))},save:()=>null})}()}();