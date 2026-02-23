<div class="app-pagehead">
  <div class="app-pagehead__title-wrap">
    <h1 class="app-pagehead__title"><?= esc($pageTitle ?? 'Dashboard') ?></h1>
    <?php if (!empty($pageSubtitle ?? '')): ?>
      <p class="app-pagehead__subtitle mb-0"><?= esc($pageSubtitle) ?></p>
    <?php endif; ?>
  </div>
  <?php if (!empty($breadcrumbs ?? [])): ?>
    <ol class="app-breadcrumb mb-0">
      <?php foreach ($breadcrumbs as $idx => $crumb): ?>
        <li class="<?= $idx === array_key_last($breadcrumbs) ? 'active' : '' ?>">
          <?= esc($crumb) ?>
        </li>
      <?php endforeach; ?>
    </ol>
  <?php endif; ?>
</div>

