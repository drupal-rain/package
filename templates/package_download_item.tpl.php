<?php
/**
 * @variables
 *   - $title
 *   - $archive
 *   - $files_table
 */
?>

<div class="<?php print $classes; ?>">
  <div class="title vertical-align space-between"<?php print $title_attributes; ?>>
    <h4><?php print $title; ?></h4>
    <span class=""><?php print $archive; ?></span>
  </div>
  <?php if ($files_table): ?>
    <div class="files"<?php print $content_attributes; ?>>
      <?php print $files_table; ?>
    </div>
  <?php endif; ?>
</div>
