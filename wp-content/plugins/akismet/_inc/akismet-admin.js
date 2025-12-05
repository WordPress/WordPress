document.addEventListener( 'DOMContentLoaded', function() {
	// Prevent aggressive iframe caching in Firefox
	var statsIframe = document.getElementById( 'stats-iframe' );
	if ( statsIframe ) {
		statsIframe.contentWindow.location.href = statsIframe.src;
	}

	initCompatiblePluginsShowMoreToggle();
} );

function initCompatiblePluginsShowMoreToggle() {
  const section = document.querySelector( '.akismet-compatible-plugins' );
  const list = document.querySelector( '.akismet-compatible-plugins__list' );
  const button = document.querySelector( '.akismet-compatible-plugins__show-more' );

  if ( ! section || ! list || ! button ) {
  	return;
  }

  function isElementInViewport( element ) {
    const rect = element.getBoundingClientRect();
    return rect.top >= 0 && rect.bottom <= window.innerHeight;
  }

  function toggleCards() {
    list.classList.toggle( 'is-expanded' );
    const isExpanded = list.classList.contains( 'is-expanded' );
    button.textContent = isExpanded ? button.dataset.labelOpen : button.dataset.labelClosed;
    button.setAttribute( 'aria-expanded', isExpanded.toString() );
  
    if ( ! isExpanded && ! isElementInViewport( section ) ) {
      section.scrollIntoView( { block: 'start' } );
    }
  }

  button.addEventListener( 'click', toggleCards );
}
