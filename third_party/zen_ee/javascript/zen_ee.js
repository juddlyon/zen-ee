$(function() {

  function fireModal(selector, width) {
    $(selector).click(function() {
      var modal_id = '#' + $(this).attr('data-modal');
      $(modal_id).dialog({ width: width });      
    });
  }

  // modal forms
  fireModal('.trig');
  fireModal('.view-details', 600);
  
  // table styles
  $('.zen_ee_table tr:odd').addClass('odd');

  $('.z25 td:first-child').addClass('w25');

  $('.z40 td:first-child').addClass('w40');

  $('.z50 td:first-child').addClass('w50');

  $('.zen_ee_table tbody .format span').each(function() {
    $(this).addClass($(this).text());
  });

  // pagination
  $('.pageContents').pajinate({
    items_per_page: 20,
    item_container_id: '.pajinate',
    nav_panel_id: '.pagination',
    show_first_last: false
  });

  // hide pagination nav < 20
  if (! $('.page_link').length < 20) {
    $('.pagination').remove();
  }

  // get value from alt_vid_url
  var $alt_vid = $('#alt_vid_url').val();

  // set alternate_video_url hidden field value to alt_vid_url value
  $('input[name="alternate_video_url"]').val($alt_vid);

});