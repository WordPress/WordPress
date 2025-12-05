if ((document.cookie.indexOf('$cookieId') !== -1 && window.location.href.indexOf('&$urlParamDisableId') === -1 && window.location.href.indexOf('?$urlParamDisableId') === -1) || window.location.href.indexOf('&$urlParamEnabledId') !== -1 || window.location.href.indexOf('?$urlParamEnabledId') !== -1) {

    var d=document, g=d.createElement('script'), s=d.getElementsByTagName('script')[0];
    g.type='text/javascript'; g.async=false; g.defer=false; g.src='$previewUrl'; s.parentNode.insertBefore(g,s);
    return;
}
