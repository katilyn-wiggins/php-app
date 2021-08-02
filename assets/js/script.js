$(document).ready(function () {
  $('.result').on('click', function () {
    var url = $(this).attr('href'); //get href attribute/value of the link that was clicked
    var id = $(this).attr('data-linkId'); //get href attribute/value of the link that was clicked

    // if (!id) {
    //   alert('data-linkId attribute not found');
    // }

    increaseLinkClicks(id, url);
    return false;
  });
});

function increaseLinkClicks(linkId, url) {
  $.post('ajax/updateLinkCount.php', { linkId: linkId }).done(function (
    result
  ) {
    if (result != '') {
      alert(result);
      return;
    }

    window.location.href = url;
  });
}
