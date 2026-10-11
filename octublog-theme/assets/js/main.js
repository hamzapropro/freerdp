/* Octublog theme interactions. */
( function () {
	'use strict';

	var header = document.querySelector( '[data-ob-header]' );

	/* Rubriques dropdown: hover on desktop, click and keyboard everywhere. */
	var dd = document.querySelector( '[data-ob-dropdown]' );
	if ( dd ) {
		var ddBtn = dd.querySelector( 'button' );
		var ddPanel = dd.querySelector( '.ob-dd__panel' );
		var setDd = function ( open ) {
			dd.classList.toggle( 'is-open', open );
			ddBtn.setAttribute( 'aria-expanded', open ? 'true' : 'false' );
			ddPanel.hidden = ! open;
		};
		ddBtn.addEventListener( 'click', function () {
			setDd( ddPanel.hidden );
		} );
		if ( window.matchMedia( '(hover: hover)' ).matches ) {
			dd.addEventListener( 'mouseenter', function () { setDd( true ); } );
			dd.addEventListener( 'mouseleave', function () { setDd( false ); } );
		}
		dd.addEventListener( 'focusout', function ( e ) {
			if ( ! dd.contains( e.relatedTarget ) ) {
				setDd( false );
			}
		} );
		document.addEventListener( 'keydown', function ( e ) {
			if ( 'Escape' === e.key && ! ddPanel.hidden ) {
				setDd( false );
				ddBtn.focus();
			}
		} );
	}

	/* Search panel and mobile drawer (only one open at a time). */
	var search = document.getElementById( 'ob-search' );
	var drawer = document.getElementById( 'ob-drawer' );
	var searchToggles = document.querySelectorAll( '[data-ob-search-toggle]' );
	var menuToggle = document.querySelector( '[data-ob-menu-toggle]' );

	var setSearch = function ( open ) {
		if ( ! search ) {
			return;
		}
		search.hidden = ! open;
		header.classList.toggle( 'search-open', open );
		searchToggles.forEach( function ( b ) { b.setAttribute( 'aria-expanded', open ? 'true' : 'false' ); } );
		if ( open ) {
			setMenu( false );
			var input = search.querySelector( 'input[type="search"]' );
			if ( input ) {
				input.focus();
			}
		}
	};
	var setMenu = function ( open ) {
		if ( ! drawer ) {
			return;
		}
		drawer.hidden = ! open;
		header.classList.toggle( 'menu-open', open );
		document.body.classList.toggle( 'ob-lock', open );
		if ( menuToggle ) {
			menuToggle.setAttribute( 'aria-expanded', open ? 'true' : 'false' );
		}
		if ( open ) {
			setSearch( false );
		}
	};
	searchToggles.forEach( function ( b ) {
		b.addEventListener( 'click', function () { setSearch( search.hidden ); } );
	} );
	if ( menuToggle ) {
		menuToggle.addEventListener( 'click', function () { setMenu( drawer.hidden ); } );
	}
	document.addEventListener( 'keydown', function ( e ) {
		if ( 'Escape' === e.key ) {
			setSearch( false );
			setMenu( false );
		}
	} );
	window.addEventListener( 'resize', function () {
		if ( window.innerWidth >= 1024 && drawer && ! drawer.hidden ) {
			setMenu( false );
		}
	} );

	/* Reading progress bar. */
	var bar = document.querySelector( '[data-ob-progress]' );
	var article = document.querySelector( '.ob-article' );
	if ( bar && article ) {
		var ticking = false;
		var update = function () {
			var rect = article.getBoundingClientRect();
			var total = rect.height - window.innerHeight;
			var done = total > 0 ? Math.min( 1, Math.max( 0, -rect.top / total ) ) : 1;
			bar.style.width = ( done * 100 ).toFixed( 1 ) + '%';
			ticking = false;
		};
		window.addEventListener( 'scroll', function () {
			if ( ! ticking ) {
				ticking = true;
				window.requestAnimationFrame( update );
			}
		}, { passive: true } );
		update();
	}

	/* Table of contents: highlight the section being read. */
	var tocLinks = document.querySelectorAll( '[data-ob-toc-link]' );
	if ( tocLinks.length && 'IntersectionObserver' in window ) {
		var byId = {};
		tocLinks.forEach( function ( a ) { byId[ a.getAttribute( 'href' ).slice( 1 ) ] = a; } );
		var headings = Object.keys( byId ).map( function ( id ) { return document.getElementById( id ); } ).filter( Boolean );
		var setActive = function ( id ) {
			tocLinks.forEach( function ( a ) { a.classList.toggle( 'is-active', a === byId[ id ] ); } );
		};
		var observer = new IntersectionObserver( function () {
			var current = headings[ 0 ];
			headings.forEach( function ( h ) {
				if ( h.getBoundingClientRect().top < window.innerHeight * 0.35 ) {
					current = h;
				}
			} );
			if ( current ) {
				setActive( current.id );
			}
		}, { rootMargin: '0px 0px -60% 0px', threshold: [ 0, 1 ] } );
		headings.forEach( function ( h ) { observer.observe( h ); } );
		if ( headings[ 0 ] ) {
			setActive( headings[ 0 ].id );
		}
	}

	/* Close the mobile table of contents after choosing a section. */
	document.querySelectorAll( '.ob-toc-mobile a' ).forEach( function ( a ) {
		a.addEventListener( 'click', function () {
			var details = a.closest( 'details' );
			if ( details ) {
				details.open = false;
			}
		} );
	} );

	/* Copy link buttons. */
	document.querySelectorAll( '[data-ob-copy]' ).forEach( function ( el ) {
		el.addEventListener( 'click', function ( e ) {
			if ( ! navigator.clipboard ) {
				return;
			}
			e.preventDefault();
			navigator.clipboard.writeText( el.href ).then( function () {
				el.classList.add( 'is-copied' );
				var label = el.querySelector( 'span' );
				var previous = label ? label.textContent : '';
				if ( label ) {
					label.textContent = 'Lien copié';
				}
				setTimeout( function () {
					el.classList.remove( 'is-copied' );
					if ( label ) {
						label.textContent = previous;
					}
				}, 1800 );
			} );
		} );
	} );

	/* Back to top. */
	var toTop = document.querySelector( '[data-ob-totop]' );
	if ( toTop ) {
		toTop.addEventListener( 'click', function ( e ) {
			e.preventDefault();
			window.scrollTo( { top: 0, behavior: 'smooth' } );
		} );
	}

	/* Rubrique description: clamp to three lines, expandable. */
	document.querySelectorAll( '[data-ob-clamp]' ).forEach( function ( box ) {
		var body = box.querySelector( '.ob-clamp__body' );
		var btn = box.querySelector( '.ob-clamp__btn' );
		if ( ! body || ! btn ) {
			return;
		}
		box.classList.add( 'is-js' );
		if ( body.scrollHeight > body.clientHeight + 4 ) {
			box.classList.add( 'is-clamped' );
			btn.hidden = false;
			btn.addEventListener( 'click', function () {
				var open = box.classList.toggle( 'is-open' );
				btn.setAttribute( 'aria-expanded', open ? 'true' : 'false' );
				btn.textContent = open ? 'Réduire' : 'Lire la suite';
			} );
		}
	} );
}() );
