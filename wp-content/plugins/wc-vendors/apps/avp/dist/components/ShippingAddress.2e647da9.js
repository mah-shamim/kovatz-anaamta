import{d as o,L as u,o as _,N as g,c as t,w as i,u as e,F as f}from"../common/vendor.d81eb0ee.js";import{Z as h,c as l}from"../main.93ffca59.js";import{b as m,F as n,c as v}from"../common/VendorEdit.c4dff8e1.js";import{af as d}from"../common/VendorStore.c0428353.js";import"../common/antd.bb16c729.js";import"../common/index.033703c1.js";import"../common/initDefaultProps.ddfd9383.js";import"../common/_plugin-vue_export-helper.c27b6911.js";const A=o({__name:"ShippingAddress",setup(b){const r=h(),{vendorSettings:s}=u(r);return(y,a)=>(_(),g(f,null,[t(n,{label:e(l)("shippingCountry")},{default:i(()=>[t(m,{selectedValue:e(s).shipping_flat_rate.shipping_address.country,"onUpdate:selectedValue":a[0]||(a[0]=p=>e(s).shipping_flat_rate.shipping_address.country=p)},null,8,["selectedValue"])]),_:1},8,["label"]),t(n,{label:e(l)("shippingState")},{default:i(()=>[t(v,{selectedValue:e(s).shipping_flat_rate.shipping_address.state,"onUpdate:selectedValue":a[1]||(a[1]=p=>e(s).shipping_flat_rate.shipping_address.state=p),country:e(s).shipping_flat_rate.shipping_address.country},null,8,["selectedValue","country"])]),_:1},8,["label"]),t(n,{label:e(l)("shippingCity")},{default:i(()=>[t(e(d),{value:e(s).shipping_flat_rate.shipping_address.city,"onUpdate:value":a[2]||(a[2]=p=>e(s).shipping_flat_rate.shipping_address.city=p)},null,8,["value"])]),_:1},8,["label"]),t(n,{label:e(l)("shippingPostcode")},{default:i(()=>[t(e(d),{value:e(s).shipping_flat_rate.shipping_address.postcode,"onUpdate:value":a[3]||(a[3]=p=>e(s).shipping_flat_rate.shipping_address.postcode=p)},null,8,["value"])]),_:1},8,["label"]),t(n,{label:e(l)("shippingAddress1")},{default:i(()=>[t(e(d),{value:e(s).shipping_flat_rate.shipping_address.address1,"onUpdate:value":a[4]||(a[4]=p=>e(s).shipping_flat_rate.shipping_address.address1=p)},null,8,["value"])]),_:1},8,["label"]),t(n,{label:e(l)("shippingAddress2")},{default:i(()=>[t(e(d),{value:e(s).shipping_flat_rate.shipping_address.address2,"onUpdate:value":a[5]||(a[5]=p=>e(s).shipping_flat_rate.shipping_address.address2=p)},null,8,["value"])]),_:1},8,["label"])],64))}});export{A as default};
