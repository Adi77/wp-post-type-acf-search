(function ($) {
  'use strict';

  /**
   * Filter ACF Version 1.0.0
   */

  $(document).ready(function () {
    let urlUpd = '',
      filterParams = [],
      filterTypeOptions = [],
      paged = 1,
      pagination = false,
      shortcodeAttrPostType = $('#hotelfiltersForm')
        .find('#postType')
        .attr('value'),
      shortcodeAttrAcfFieldIds = $('#hotelfiltersForm')
        .find('#acfFieldIds')
        .attr('value');

    /*
     * Get url Parameters
     */
    filterParams = getUrlParams();
    /*
     * Check if url Parameters are set
     */
    if (!$.isEmptyObject(filterParams)) {
      /*
       * Set active filter from url params
       */
      $.each(filterParams, function (key, value) {
        filterTypeOptions = value.split(',');

        $.each(filterTypeOptions, function (optionsKey, optionsValue) {
          $('#' + key + '')
            .find($('#' + optionsValue.replace(/%/g, '') + ''))
            .attr('checked', true);
        });
      });
    }

    loadFilteredItemsList(filterParams, paged, shortcodeAttrPostType);

    /*
     * Get Filter Results Count and disable options
     */
    $('.hotel-list_filter').on('click', function (event) {
      filterParams = prepareFilterQuery($(this).parent().parent());
      loadFilteredContentItemsJson(
        filterParams,
        shortcodeAttrPostType,
        shortcodeAttrAcfFieldIds
      );

      //loadFilteredItemsList(filterParams);
    });

    /*
     * reset all filters
     */
    $('.reset-filter').on('click', function (event) {
      filterParams = [];
      loadFilteredItemsList(filterParams, null, shortcodeAttrPostType);

      loadFilteredContentItemsJson(
        filterParams,
        shortcodeAttrPostType,
        shortcodeAttrAcfFieldIds
      );

      previewFilterState();

      window.history.pushState(null, '', '?');
      $('.hotel-item-count').empty();
      $('input[name="hotels-filter-checkbox"]').each(function () {
        this.checked = false;
      });

      event.preventDefault();
    });

    /*
     * Pagination
     */
    $(document).on('click', '.loadmore button', function (event) {
      filterParams = $.parseJSON($(this).attr('data-filter-params'));
      paged = $(this).attr('data-paged');
      loadFilteredItemsList(
        filterParams,
        paged,
        shortcodeAttrPostType,
        (pagination = true)
      );
      event.preventDefault();
    });

    /*
     * Get activated Filter on Form Submit and update url
     */
    $('#hotelfiltersForm').submit(function (event) {
      filterParams = prepareFilterQuery($(this));
      loadFilteredItemsList(filterParams, null, shortcodeAttrPostType);

      /*
       * generate string for url params
       */
      let filterParamsString = Object.keys(filterParams)
        .map(function (key) {
          return key + '=' + filterParams[key];
        })
        .join('&');

      /*
       *Update url params
       */
      urlUpd = '/hotels-uebersicht/?' + filterParamsString;
      window.history.pushState(null, '', urlUpd);

      event.preventDefault();
    });
  });

  /*
   * Generate Array from checked filters
   */
  function prepareFilterQuery(thisObj) {
    let filterType = [];
    let filterParams = {};
    let filterParamsValuesString = '';

    thisObj.children('div').each(function (index) {
      filterType[index] = $(this).attr('id');
    });
    $.each(filterType, function (key, value) {
      let filterInputField = $('#' + value + '');
      if (filterInputField) {
        $(filterInputField)
          .children('input:checked')
          .each(function () {
            filterParamsValuesString += $(this).attr('value') + ',';
          });
        if (filterParamsValuesString) {
          filterParams[value] = filterParamsValuesString.slice(0, -1);
        }
        filterParamsValuesString = '';
      }
    });
    return filterParams;
  }

  function loadFilteredItemsList(
    filterParams,
    paged,
    shortcodeAttrPostType,
    pagination
  ) {
    $.ajax({
      type: 'POST',
      url: '/wp-admin/admin-ajax.php',
      dataType: 'html',
      data: {
        action: 'filtered_content_list',
        filterParams: filterParams,
        paged: paged,
        shortcodeAttrPostType: shortcodeAttrPostType,
      },
      beforeSend: function () {
        $('#hotelfiltersForm').find('.spinner-border').show();
      },
      success: function (res) {
        $('#hotelfiltersForm').find('.spinner-border').hide('slow');

        if (pagination) {
          $('.itemcount, .loadmore').remove();
          $('.hotel-item-tiles').append(res);
        } else {
          $('.hotel-item-tiles')
            .html(res)
            .promise()
            .done(function () {
              if ($('.total-hotels-count').length) {
                $('.hotel-item-count').replaceWith(
                  "<span class='hotel-item-count'>" +
                    $('.total-hotels-count').html() +
                    '</span>'
                );
              } else {
                $('.hotel-item-count').replaceWith(
                  "<span class='hotel-item-count'>" +
                    $('.hotel-item').length +
                    '</span>'
                );
              }
            });
        }
        $('.itemcount').prepend($('.hotel-item').length + ' von ');
      },
    });
    return false;
  }

  function loadFilteredContentItemsJson(
    filterParams,
    shortcodeAttrPostType,
    shortcodeAttrAcfFieldIds
  ) {
    $.ajax({
      type: 'POST',
      url: '/wp-admin/admin-ajax.php',
      dataType: 'JSON',
      data: {
        action: 'filtered_content_json',
        filterParams: filterParams,
        shortcodeAttrPostType: shortcodeAttrPostType,
        shortcodeAttrAcfFieldIds: shortcodeAttrAcfFieldIds,
      },
      beforeSend: function () {
        $('#hotelfiltersForm').find('.spinner-border').show();
      },
      success: function (res) {
        $('#hotelfiltersForm').find('.spinner-border').hide('slow');

        let itemcount = previewFilterState(res);

        $('.hotel-item-count').html(itemcount);
      },
    });
    return false;
  }

  function getUrlParams() {
    let url = document.location.href;
    let qs = url.substring(url.indexOf('?') + 1).split('&');
    if (qs == url || !qs[0]) {
      qs = 0;
    }
    for (var i = 0, result = {}; i < qs.length; i++) {
      qs[i] = qs[i].split('=');
      result[qs[i][0]] = qs[i][1];
    }
    return result;
  }

  function previewFilterState(res = 0) {
    if (res != 0) {
      $.each(
        $('input[name="hotels-filter-checkbox"]'),
        function (index, value) {
          let currentEl = this.value;

          $('input[value="' + this.value + '"]').attr('disabled', true);

          $('label[for="' + currentEl + '"]')
            .find('span')
            .html(0);

          $.each(res, function (key, value2) {
            $.each(Object.values(value2), function (key2, value3) {
              if (currentEl == value3['filterValue']) {
                $('label[for="' + value3['filterValue'] + '"]')
                  .find('span')
                  .html(value3['filterCount']);
                $('input[value="' + value3['filterValue'] + '"]').removeAttr(
                  'disabled'
                );
              }
            });
          });
        }
      );
    } else {
      $.each(
        $('input[name="hotels-filter-checkbox"]'),
        function (index, value) {
          $('input[value="' + this.value + '"]').removeAttr('disabled');
        }
      );
    }

    return res['itemcount'];
  }
})(jQuery);
