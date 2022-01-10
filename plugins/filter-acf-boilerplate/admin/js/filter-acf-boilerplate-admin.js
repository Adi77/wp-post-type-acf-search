(function ($) {
  'use strict';
  /**
   * All of the code for your admin-facing JavaScript source
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
    /*
     * show ACF Fields of Post Type Position
     */
    $('#post-type-selector').on('change', function () {
      loadPostTypeAcfFields(this.value);
    });

    /*
     * Generate shortcodes
     */
    let filterIds = [];
    let postTypeName = '';
    $('#acfFilters').submit(function (event) {
      if ($(this).find('input:checked').length == 0) {
        alert('Bitte wählen Sie mindestens ein Filter aus.');
        return false;
      }

      $(this)
        .find('input:checked')
        .each(function (index) {
          filterIds[index] = $(this).attr('id');
        });

      postTypeName = $(this).find('div.postType').attr('id');

      generateShortcodes(filterIds, postTypeName);

      event.preventDefault();
    });
  });

  function loadPostTypeAcfFields(postType) {
    $.ajax({
      type: 'POST',
      url: '/wp-admin/admin-ajax.php',
      dataType: 'Html',
      data: {
        action: 'acf_filter_list',
        postType: postType,
      },
      beforeSend: function () {
        $('#acfFilters').find('.spinner-border').show();
      },
      success: function (res) {
        $('#acfFilters').find('.spinner-border').hide('slow');

        $('.acf-filter-list').html(res);
      },
    });
    return false;
  }

  function generateShortcodes(filterIds, postTypeName) {
    $.ajax({
      type: 'POST',
      url: '/wp-admin/admin-ajax.php',
      dataType: 'Html',
      data: {
        action: 'generated_shortcodes_list',
        filterIds: filterIds,
        postTypeName: postTypeName,
      },
      beforeSend: function () {
        $('#acfFilters').find('.spinner-border').show();
      },
      success: function (res) {
        $('#acfFilters').find('.spinner-border').hide('slow');

        $('.generated-shortcodes-list').html(res);
      },
    });
    return false;
  }
})(jQuery);
