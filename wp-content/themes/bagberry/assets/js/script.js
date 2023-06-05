"use strict";

(function ($) {
  'use strict';

  $(function () {
    $(document.body).on('click', '.wp-block-woocommerce-filter-wrapper > h1, .wp-block-woocommerce-filter-wrapper > h2, .wp-block-woocommerce-filter-wrapper > h3, .wp-block-woocommerce-filter-wrapper > h4, .wp-block-woocommerce-filter-wrapper > h5, .wp-block-woocommerce-filter-wrapper > h6, .wc-blocks-filter-wrapper h1, .wc-blocks-filter-wrapper h2, .wc-blocks-filter-wrapper h3, .wc-blocks-filter-wrapper h4, .wc-blocks-filter-wrapper h5,  .wc-blocks-filter-wrapper h6', function (e) {
      e.preventDefault();
      var $wrapper = $(this).parent();
      var $parents = $('.wc-blocks-filter-wrapper');
      $parents.removeClass('active');
      $wrapper.hasClass('active') ? $wrapper.removeClass('active') : $wrapper.addClass('active');
    });
    $(document.body).on('click', function (e) {
      var $parent = $('.wc-blocks-filter-wrapper');
      if (!$parent.is(e.target) && $parent.has(e.target).length === 0) {
        $parent.removeClass('active');
      }
    });
    $(window).on('scroll', function () {
      if (window.innerWidth >= 600) {
        var isSubMegamenu = document.querySelector('.has-megamenu > .wp-block-navigation__submenu-container');
        if (isSubMegamenu) {
          var megaMenuChildStyles = window.getComputedStyle(isSubMegamenu);
          var megaMenuChildOffsetTop = megaMenuChildStyles.getPropertyValue('--wp--custom--spacing--menu-offset-top');
          if ($('body').hasClass('admin-bar')) {
            $('.has-megamenu > .wp-block-navigation__submenu-container').css({
              'margin-top': megaMenuChildOffsetTop - $(window).scrollTop() + 'px'
            });
          } else {
            $('.has-megamenu > .wp-block-navigation__submenu-container').css({
              'margin-top': megaMenuChildOffsetTop - 35 - $(window).scrollTop() + 'px'
            });
          }
        }
      }
    });
    $('#customer_login').each(function () {
      var titles = $(this).find('h2').get();
      var tab_list_html = document.createElement('ul');
      $(tab_list_html).addClass('customer_login_toggle');
      titles.forEach(function (title) {
        var $tab_title = document.createElement('li');
        $($tab_title).html('<h6>' + $(title).text() + '</h6>');
        $(tab_list_html).append($tab_title);
      });
      $(this).prepend(tab_list_html);
    });
    $('#customer_login').find('li').each(function (index) {
      var tab_list = $('#customer_login').find('li');
      var columns = $('#customer_login').find('>div');
      $(tab_list[0]).add($(columns[0])).addClass('active');
      $(this).on('click', function () {
        columns.removeClass('active');
        $(columns[index]).addClass('active');
        tab_list.removeClass('active');
        $(this).addClass('active');
      });
    });
    $.fn.insertQtyButtons = function () {
      $(this).each(function () {
        $(this).wrap('<div class="qty-container"></div>');
        $(this).parent('.qty-container').append('<button class="qty-minus">-</button><button class="qty-plus">+</button>');
      });
    };
    $('form .qty').insertQtyButtons();
    $(document.body).on('updated_cart_totals', function () {
      $('.woocommerce-cart-form .qty').insertQtyButtons();
    });
    $(document.body).on('updated_checkout', function () {
      if ($('input[name="coupon_code"]').length == 0) {
        $.ajax({
          type: 'POST',
          url: wc_checkout_params.wc_ajax_url.toString().replace('%%endpoint%%', 'checkout_coupon'),
          data: {},
          success: function success(res) {
            console.log("res", res);
            $('<tr class="agni_checkout_coupon_field"><td>' + res + '</td></tr>').insertBefore('.woocommerce-checkout-review-order-table .order-total');
          },
          dataType: 'html'
        });
      }
    });
    $(document.body).on('click', ".coupon_submit", function (e) {
      e.preventDefault();
      var $coupon = $(this).closest('.agni_checkout_coupon');
      var data = {
        security: wc_checkout_params.apply_coupon_nonce,
        coupon_code: $(this).siblings('input[name="coupon_code"]').val()
      };
      $.ajax({
        type: 'POST',
        url: wc_checkout_params.wc_ajax_url.toString().replace('%%endpoint%%', 'apply_coupon'),
        data: data,
        success: function success(code) {
          if (code) {
            $coupon.before(code);
            $(document.body).trigger('update_checkout', {
              update_shipping_method: false
            });
          }
        },
        dataType: 'html'
      });
    });
    $(document.body).on('click', '.qty-plus, .qty-minus', function (e) {
      e.preventDefault();
      var $qty = $(this).closest('.qty-container').find('.qty'),
        currentVal = parseFloat($qty.val()),
        max = parseFloat($qty.attr('max')),
        min = parseFloat($qty.attr('min')),
        step = $qty.attr('step');
      if (!currentVal || currentVal === '' || currentVal === 'NaN') currentVal = 0;
      if (max === '' || max === 'NaN') max = '';
      if (min === '' || min === 'NaN') min = 0;
      if (step === 'any' || step === '' || step === undefined || parseFloat(step) === 'NaN') step = 1;
      if ($(this).is('.qty-plus')) {
        if (max && (max == currentVal || currentVal > max)) {
          $qty.val(max);
        } else {
          $qty.val(currentVal + parseFloat(step));
        }
      } else {
        if (min && (min == currentVal || currentVal < min)) {
          $qty.val(min);
        } else if (currentVal > 0) {
          $qty.val(currentVal - parseFloat(step));
        }
      }
      $qty.trigger('change');
    });
    $.agni_woocommerce_tabs = {
      hideDefaultTabs: function hideDefaultTabs(tabs) {
        var tabs_ul = tabs.find('>ul');
        tabs_ul.hide();
      },
      activePanel: function activePanel(tabs, tab_id) {
        var tabs_panel = tabs.find('.panel');
        var panel_title = tabs.find('.panel-title');
        $(panel_title[tab_id]).addClass('active');
        $(tabs_panel[tab_id]).addClass('active');
      },
      panelToggle: function panelToggle(tabs) {
        var tabs_panel = tabs.find('.panel');
        var panel_title = tabs.find('.panel-title');
        var tab_id = '0';
        this.activePanel(tabs, tab_id);
        panel_title.find('a').on('click', function (e) {
          e.preventDefault();
          var panel_target_id = $(this).attr('href').substr(1);
          var target_content = tabs.find('#' + panel_target_id);
          if ($(this).parent().hasClass('active')) {
            $(this).parent().removeClass('active');
          } else {
            panel_title.removeClass('active');
            $(this).parent().addClass('active');
          }
          if (target_content.hasClass('active')) {
            target_content.removeClass('active');
            target_content.slideUp(200);
          } else {
            tabs_panel.removeClass('active');
            target_content.addClass('active');
            tabs_panel.slideUp(200);
            target_content.slideDown(200);
          }
        });
      },
      generatePanelTitle: function generatePanelTitle(tabs) {
        var tabs_li = tabs.find('>ul li');
        tabs_li.each(function () {
          var $tab_li = $(this);
          var target = $tab_li.find('a');
          var target_id = target.attr('href').substr(1);
          var target_content = tabs.find('#' + target_id);
          var $tab_li_classList = $tab_li.attr('class').split(/\s+/);
          var tab_heading = '<div class="panel-title ' + $tab_li_classList[0] + '">' + $tab_li.html() + '</div>';
          if (target_content) {
            $(tab_heading).insertBefore(target_content);
          }
        });
      }
    };
    $('.woocommerce-tabs').each(function () {
      var tabs = $(this);
      if (window.innerWidth < 782) {
        $.agni_woocommerce_tabs.hideDefaultTabs(tabs);
        $.agni_woocommerce_tabs.generatePanelTitle(tabs);
        $.agni_woocommerce_tabs.panelToggle(tabs);
      }
    });
    $('.wp-block-video').each(function () {
      $(this).append('<button class="wp-block-video__play-icon"><img src="' + bagberry_script.imgdir + '/video-play.png"></button>');
    });
    $(document.body).on('click', '.wp-block-video__play-icon', function () {
      var $this = $(this);
      var $parent = $this.closest('.wp-block-video');
      $this.siblings('video').get(0).play();
      $parent.addClass('play-initialized');
    });
    $(document.body).on('change', function () {
      console.log("changed document");
    });
    if ($('.wc-block-mini-cart__badge').text() == 0) {
      $('.wc-block-mini-cart__badge').addClass('hide');
    }
    $('.wc-block-mini-cart').on('mouseover', function () {
      if ($('.wc-block-mini-cart__badge').text() == 0) {
        $('.wc-block-mini-cart__badge').addClass('hide');
      }
    });
    $(document.body).on('wc_fragments_refreshed', function () {
      if (window.location.href.indexOf("add-to-cart") > -1) {
        if ($('.wc-block-components-drawer__screen-overlay').length <= 0 || $('.wc-block-components-drawer__screen-overlay--is-hidden').length > 0) {
          $('.wc-block-mini-cart__button').trigger('click');
        }
      }
    });
  });
})(jQuery);