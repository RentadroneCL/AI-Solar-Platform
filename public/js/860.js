"use strict";(self.webpackChunk=self.webpackChunk||[]).push([[860],{3857:(e,t,r)=>{function n(e,t){let r=e.length-t,n=0;do{for(let r=t;r>0;r--)e[n+t]+=e[n],n++;r-=t}while(r>0)}function o(e,t,r){let n=0,o=e.length;const i=o/r;for(;o>t;){for(let r=t;r>0;--r)e[n+t]+=e[n],++n;o-=t}const l=e.slice();for(let t=0;t<i;++t)for(let n=0;n<r;++n)e[r*t+n]=l[(r-n-1)*i+t]}r.d(t,{Z:()=>i});class i{async decode(e,t){const r=await this.decodeBlock(t),i=e.Predictor||1;if(1!==i){const t=!e.StripOffsets;return function(e,t,r,i,l,s){if(!t||1===t)return e;for(let e=0;e<l.length;++e){if(l[e]%8!=0)throw new Error("When decoding with predictor, only multiple of 8 bits are supported.");if(l[e]!==l[0])throw new Error("When decoding with predictor, all samples must have the same size.")}const a=l[0]/8,c=2===s?1:l.length;for(let s=0;s<i&&!(s*c*r*a>=e.byteLength);++s){let i;if(2===t){switch(l[0]){case 8:i=new Uint8Array(e,s*c*r*a,c*r*a);break;case 16:i=new Uint16Array(e,s*c*r*a,c*r*a/2);break;case 32:i=new Uint32Array(e,s*c*r*a,c*r*a/4);break;default:throw new Error(`Predictor 2 not allowed with ${l[0]} bits per sample.`)}n(i,c)}else 3===t&&(i=new Uint8Array(e,s*c*r*a,c*r*a),o(i,c,a))}return e}(r,i,t?e.TileWidth:e.ImageWidth,t?e.TileLength:e.RowsPerStrip||e.ImageLength,e.BitsPerSample,e.PlanarConfiguration)}return r}}},1860:(e,t,r)=>{r.r(t),r.d(t,{default:()=>o});var n=r(3857);class o extends n.Z{decodeBlock(e){const t=new DataView(e),r=[];for(let n=0;n<e.byteLength;++n){let e=t.getInt8(n);if(e<0){const o=t.getUint8(n+1);e=-e;for(let t=0;t<=e;++t)r.push(o);n+=1}else{for(let o=0;o<=e;++o)r.push(t.getUint8(n+o+1));n+=e+1}}return new Uint8Array(r).buffer}}}}]);