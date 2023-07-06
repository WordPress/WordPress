"use strict";

function woof_qs_after_redraw_grid_2() {
    var zindex = 10;

    jQuery("div.qs_card").on('click', function (e) {
        if (jQuery(event.target).attr('class') != 'woof_qs_link' && jQuery(event.target).attr('class') != 'woof_qs_link_btn') {
            e.preventDefault();
        }

        var isShowing = false;

        if (jQuery(this).hasClass("show")) {
            isShowing = true
        }

        if (jQuery("div.qs_cards").hasClass("showing")) {
            // a card is already in view
            jQuery("div.qs_card.show")
                    .removeClass("show");

            if (isShowing) {
                // this card was showing - reset the grid
                jQuery("div.qs_cards")
                        .removeClass("showing");
            } else {
                // this card isn't showing - get in with it
                jQuery(this)
                        .css({zIndex: zindex})
                        .addClass("show");

            }

            zindex++;

        } else {
            // no cards in view
            jQuery("div.qs_cards")
                    .addClass("showing");
            jQuery(this)
                    .css({zIndex: zindex})
                    .addClass("show");

            zindex++;
        }

    });
}

