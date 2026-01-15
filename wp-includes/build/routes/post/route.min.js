import{redirect as r}from"@wordpress/route";var o={beforeLoad:({params:t})=>{throw r({throw:!0,to:"/types/$type/list/$slug",params:{type:t.type,slug:"all"}})}};export{o as route};
