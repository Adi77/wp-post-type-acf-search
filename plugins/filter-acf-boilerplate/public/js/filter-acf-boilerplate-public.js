(function ($) {
  'use strict';

  /**
   * All of the code for your public-facing JavaScript source
   * should reside in this file.
   *
   * Note: It has been assumed you will write jQuery code here, so the
   * $ function reference has been prepared for usage within the scope
   * of this function.
   *
   * This enables you to define handlers, for when the DOM is ready:
   *
   * $(function() {
   *
   * });
   *
   * When the window is loaded:
   *
   * $( window ).load(function() {
   *
   * });
   *
   * ...and/or other possibilities.
   *
   * Ideally, it is not considered best practise to attach more than a
   * single DOM-ready or window-load handler for a particular page.
   * Although scripts in the WordPress core, Plugins and Themes may be
   * practising this, we should strive to set a better example in our own work.
   */

  $(document).ready(function () {
    let urlUpd = '';
    let filterParams = [];
    let filterTypeOptions = [];
    let paged = 1;
    let pagination = false;

    filterParams = getUrlParams();

    loadFilteredItemsList(filterParams, paged);

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

    /*
     * reset all filters
     */
    $('.reset-filter').on('click', function (event) {
      loadFilteredItemsList();
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
      loadFilteredItemsList(filterParams, paged, (pagination = true));
      event.preventDefault();
    });

    /*
     * Get Filter Results Count and disable options
     */
    $('.hotel-list_filter').on('click', function (event) {
      filterParams = prepareFilterQuery($(this).parent().parent());
      loadFilteredItemsData(filterParams);

      //event.preventDefault();
    });

    /*
     * Get activated Filter on Form Submit and update url
     */
    $('#hotelfiltersForm').submit(function (event) {
      filterParams = prepareFilterQuery($(this));

      loadFilteredItemsList(filterParams);

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

  function loadFilteredItemsData(filterParams) {
    ajaxRequest('filter_hotels_data', filterParams, '.hotel-item-count');
  }

  function loadFilteredItemsList(filterParams, paged, pagination) {
    ajaxRequest(
      'filter_hotels_list',
      filterParams,
      '.hotel-item-tiles',
      paged,
      pagination
    );
  }

  function ajaxRequest(action, filterParams, divElement, paged, pagination) {
    $.ajax({
      type: 'POST',
      url: '/wp-admin/admin-ajax.php',
      dataType: 'html',
      data: {
        action: action,
        filterParams: filterParams,
        paged: paged,
      },
      beforeSend: function () {
        $('#hotelfiltersForm').find('.spinner-border').show();
      },
      success: function (res) {
        $('#hotelfiltersForm').find('.spinner-border').hide('slow');
        if (divElement == '.hotel-item-tiles') {
          if (pagination) {
            $('.loadmore button').parent().remove();
            $(divElement).append(res);
          } else {
            $(divElement).html(res);
          }
          $('.itemcount').prepend($('.hotel-item').length + ' von ');
        } else {
          $(divElement).html(res);
        }
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
})(jQuery);
