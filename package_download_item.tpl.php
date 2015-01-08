
<div class="row">
  <div class="title row vertical-align">
    <h4 class="medium-8 columns"><?php print $title; ?></h4>
    <span class="medium-4 columns">.zip (23.42 KB)</span>
  </div>
  <?php if ($files_table): ?>
    <div class="files">
      <?php print $files_table; ?>
    </div>
  <?php endif; ?>
</div>
