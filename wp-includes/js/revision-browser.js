/**
 * Revision Browser JS
 *
 * JS responsible for frontend revision browsing.
 *
 * @since   0.1.0
 * @package RBR
 */
jQuery(document).ready(function ($) {
    var $previous = $('#wp-admin-bar-revisions-browser-previous a');
    var $next = $('#wp-admin-bar-revisions-browser-next a');
    $previous.hide().css('visibility', 'hidden').attr('aria-hidden', true);
    $next.hide().css('visibility', 'hidden').attr('aria-hidden', true);

    wp.api.loadPromise.done(function () {
        var loaded = false;
        var revision;

        $('.revisions-browser').on('click hover mouseover', function (e) {
            e.preventDefault();
            initRevisions();
        });

        //load revisions and set up system, but only once.
        function initRevisions() {
            var $menu = $('#wp-admin-bar-revisions-browser');
            if (false === loaded) {
                var revisions = new wp.api.collections.PostRevisions({}, {parent: REVBROWSER.post });
                var post = new wp.api.models.Post({id: REVBROWSER.post });
                $.when(revisions.fetch(), post.fetch()).then(function (d1, d2) {
                    if ('object' == typeof  d1 && 0 < d1[0].length) {

                        revisions = d1[0].reverse();
                        var count = d1[0].length;
                        var current = count;

                        if( 
                            d2[0].content.rendered != revisions[count - 1].content.rendered ||
                            d2[0].title.rendered != revisions[count - 1].title.rendered
                        ) {
                            revisions[current] = d2[0];
                        } else {
                            current--;
                        }
                        
                        var $content = $('.' + REVBROWSER.content);
                        var $title = $('.' + REVBROWSER.title);

                        updateNav();

                        $previous.on('click', function (e) {
                            e.preventDefault();
                            if (hasPrevious()) {
                                current = current - 1;
                                revision = revisions[current];
                                placeRevision(revision);
                                updateNav();
                            }

                        });

                        $next.on('click', function (e) {
                            e.preventDefault();
                            if (current + 1 in revisions) {
                                current = current + 1;
                                revision = revisions[current];
                                placeRevision(revision);
                                updateNav();
                            }


                        });

                        function hasPrevious() {
                            if (current - 1 in revisions) {
                                return true;
                            }
                        }

                        function hasNext() {
                            if (current + 1 in revisions) {
                                return true;
                            }
                        }

                        function updateNav() {
                            if (hasNext()) {
                                $next.show().css('visibility', 'visible').attr('aria-hidden', false);
                            } else {
                                $next.hide().css('visibility', 'hidden').attr('aria-hidden', true);
                            }

                            if (hasPrevious()) {
                                $previous.show().css('visibility', 'visible').attr('aria-hidden', false);
                            } else {
                                $previous.hide().css('visibility', 'hidden').attr('aria-hidden', true);
                            }
                        }


                        function placeRevision(revision) {

                            $title.html(revision.title.rendered);
                            $content.html(revision.content.rendered);

                        }

                    } else {
                        $menu.first('a').html( REVBROWSER.none);
                        $menu.find('.ab-sub-wrapper').remove();
                    }

                });

            }

            loaded = true;
        }


    });

});
