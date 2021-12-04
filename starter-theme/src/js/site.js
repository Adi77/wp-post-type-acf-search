import './../scss/style.scss';

import './navigation';

import $ from 'jquery';

$(document).ready(function () {
  let urlUpd = '';
  let filterParams = getUrlParams();
  let paged = 1;

  loadHotelList(filterParams, paged);

  //
  // Set active filter from url params
  //
  $.each(filterParams, function (key, value) {
    let filterTypeOptions = value.split(',');
    $.each(filterTypeOptions, function (optionsKey, optionsValue) {
      $('#' + key + '')
        .find($('#' + optionsValue.replace(/%/g, '') + ''))
        .attr('checked', true);
    });
  });

  /*   $('.hotel-list_filter').on('click', function () {

    console.log('huhu');
  }); */

  //
  // reset all filters
  //
  $('.reset-filter').on('click', function (event) {
    loadHotelList();
    window.history.pushState(null, '', '?');

    $('input[name="hotels-filter-checkbox"]').each(function () {
      this.checked = false;
    });

    event.preventDefault();
  });

  //
  // Pagination
  //
  $(document).on('click', '.pagination', function (event) {
    filterParams = $.parseJSON($(this).attr('data-filter-params'));

    paged = $(this).attr('data-paged');

    console.log(paged);

    loadHotelList(filterParams, paged);

    event.preventDefault();
  });

  //
  // Generate Array from checked filters on form submit
  //
  $('#hotelfiltersForm').submit(function (event) {
    let filterType = [];
    let filterParams = {};
    let filterParamsValuesString = '';

    $(this)
      .children('div')
      .each(function (index) {
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

    loadHotelList(filterParams);

    //
    // generate string for url params
    //
    let filterParamsString = Object.keys(filterParams)
      .map(function (key) {
        return key + '=' + filterParams[key];
      })
      .join('&');

    //
    // Update url params
    //
    urlUpd = '/hotels-uebersicht/?' + filterParamsString;
    window.history.pushState(null, '', urlUpd);

    event.preventDefault();
  });
});

function loadHotelList(filterParams, paged) {
  ajaxRequest('filter_hotels', filterParams, '.hotel-item-tiles', paged);
}

function ajaxRequest(action, filterParams, divElement, paged) {
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
      $(divElement).html(res);
    },
  });
  return false;
}

function getUrlParams() {
  let url = document.location.href;
  let qs = url.substring(url.indexOf('?') + 1).split('&');
  if (url == qs) {
    qs = 0;
  }
  for (var i = 0, result = {}; i < qs.length; i++) {
    qs[i] = qs[i].split('=');
    result[qs[i][0]] = qs[i][1];
  }
  return result;
}
