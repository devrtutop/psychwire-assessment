( function () {
	var link = document.querySelector( '.back-to-top' );
	var narrow = window.matchMedia( '(max-width: 640px)' );

	if ( ! link ) {
		return;
	}

	document.documentElement.classList.add( 'has-back-to-top' );

	function update() {
		if ( ! narrow.matches ) {
			link.classList.remove( 'is-visible' );
			return;
		}

		var max = document.documentElement.scrollHeight - window.innerHeight;
		var progress = max > 0 ? window.scrollY / max : 0;

		link.classList.toggle( 'is-visible', progress >= 0.5 );
	}

	update();
	window.addEventListener( 'scroll', update, { passive: true } );
	window.addEventListener( 'resize', update );
	narrow.addEventListener( 'change', update );
}() );
