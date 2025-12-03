async function reloadWithTinymce() {
	const currentUrl = new URL( window.location.href );
	currentUrl.searchParams.set( 'requiresTinymce', '1' );
	window.location.href = currentUrl;
}

window.tinymce = new Proxy(
	{},
	{
		get: reloadWithTinymce,
		set: reloadWithTinymce,
		apply: reloadWithTinymce,
	}
);
