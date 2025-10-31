<?php
$currentPage = 'home';
require_once __DIR__ . '/../includes/header.php';
?>

<section class="bg-white rounded-3xl shadow-lg p-12 w-full mx-auto px-4 shadow-brand">
  <h1 class="text-primary text-4xl mb-2 text-center font-fumofumo"><?php echo htmlspecialchars($siteConfig['site']['subtitle'] ?? ''); ?></h1>
  <p class="text-muted text-xl text-center mb-8"><?php echo htmlspecialchars($siteConfig['site']['description'] ?? ''); ?></p>

  <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 mt-4">
    <?php foreach ($siteConfig['home']['features'] as $feature): ?>
      <div class="bg-gradient-to-br from-gray-50 to-pink-50 rounded-2xl p-6 text-center border border-gray-200 transition-all duration-200 hover:-translate-y-0.5 cursor-pointer hover:shadow-lg">
        <h2 class="text-primary text-xl mb-2 font-fumofumo mt-2"><?php echo htmlspecialchars($feature['title']); ?></h2>
        <p class="text-muted text-base leading-relaxed m-0"><?php echo htmlspecialchars($feature['description']); ?></p>
      </div>
    <?php endforeach; ?>
  </div>
</section>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>