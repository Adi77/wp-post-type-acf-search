import './../scss/style.scss';

import './navigation';

import $ from 'jquery';

$(document).ready(function () {
  let urlUpd = '';
  let filterParams = getUrlParams();

  loadFilterNav(filterParams);
  loadHotelList(filterParams);

  /*   $('.hotel-list_filter').on('click', function () {
    loadFilterNav(filterParams);
    console.log('huhu');
  }); */

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

    loadFilterNav(filterParams);
    loadHotelList(filterParams);

    urlUpd = '/hotels-uebersicht/?' + $.param(filterParams);
    window.history.pushState(null, '', urlUpd);

    event.preventDefault();
  });
});

function loadFilterNav(filterParams) {
  ajaxRequest('filter_nav', filterParams, '.hotel-filter-nav');
}

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
  var url = document.location.href;
  var qs = url.substring(url.indexOf('?') + 1).split('&');
  for (var i = 0, result = {}; i < qs.length; i++) {
    qs[i] = qs[i].split('=');
    result[qs[i][0]] = decodeURIComponent(qs[i][1]);
  }
  return result;
}
