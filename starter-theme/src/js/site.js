import './../scss/style.scss';

import './navigation';

import $ from 'jquery';

$(document).ready(function () {
  let urlUpd = '';
  let filterParams = getUrlParams();

  loadHotelList(filterParams);

  $('.hotel-list_filter').on('click', function () {
    let filterParams = [];
    $('input[name="hotels-filter-checkbox"]:checked').each(function (index) {
      filterParams[$(this).parent().attr('id') + '_' + index] = this.checked
        ? $(this).val()
        : '';
    });
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

    urlUpd = '/hotels-uebersicht/?' + $.param(filterParams);
    window.history.pushState(null, '', urlUpd);

    event.preventDefault();
  });
});

function loadHotelList(filterParams) {
  $.ajax({
    type: 'POST',
    url: '/wp-admin/admin-ajax.php',
    dataType: 'html',
    data: {
      action: 'filter_hotels',
      filterParams: filterParams,
    },
    success: function (res) {
      $('.hotel-item-tiles').html(res);
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
