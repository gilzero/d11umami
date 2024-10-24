(function ($) {
  $(function () {
    var blockCount = 0;
    $('.region').each(function (index, element) {
      var $region = $(this);
      $region.find('.block-wrapper').each(function (index, element) {
          var $block = $(this);
          $block
            // Even/odd in this region
            .addClass((index % 2 === 0) ? 'odd' : 'even')
            // Even/odd of all blocks
            //.addClass((blockCount % 2 === 0) ? 'blocks-odd' : 'blocks-even')
            // ID of the block in this region.
            .addClass('block_' + (index + 1))
            // ID of the block among all blocks
            //.addClass('blocks_' + (blockCount + 1));

            // Increment the total block count.
            blockCount++;
        });
    });
  });
}(jQuery));
