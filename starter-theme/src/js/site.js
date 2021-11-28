import './../scss/style.scss';

import './navigation';

import $ from 'jquery';

$(document).ready(function () {
  let urlUpd = '';
  let filterParams = getUrlParams();

  loadHotelList(filterParams);

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

  $('.reset-filter').on('click', function (event) {
    loadHotelList();
    window.history.pushState(null, '', '?');

    $('input[name="hotels-filter-checkbox"]').each(function () {
      this.checked = false;
    });

    event.preventDefault();
  });

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

    let filterParamsString = Object.keys(filterParams)
      .map(function (key) {
        return key + '=' + filterParams[key];
      })
      .join('&');

    urlUpd = '/hotels-uebersicht/?' + filterParamsString;
    window.history.pushState(null, '', urlUpd);

    event.preventDefault();
  });
});

function loadHotelList(filterParams) {
  ajaxRequest('filter_hotels', filterParams, '.hotel-item-tiles');
}

function ajaxRequest(action, filterParams, divElement) {
  $.ajax({
    type: 'POST',
    url: '/wp-admin/admin-ajax.php',
    dataType: 'html',
    data: {
      action: action,
      filterParams: filterParams,
    },
    success: function (res) {
      $(divElement).html(res);
    },
  });
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
