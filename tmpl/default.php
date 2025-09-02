<?php

use Joomla\CMS\Factory;
use Joomla\CMS\Helper\ModuleHelper;
use Joomla\CMS\Language\Text;

defined('_JEXEC') or die;

$app = Factory::getApplication();
$template = $app->getTemplate();

$additionalMenuClass = '';

if (str_starts_with($template, 'yoo_')) {
    $additionalMenuClass .= ' menu-sidebar';
}

?>
<ul class="menu<?= htmlspecialchars($additionalMenuClass . $moduleclass_sfx, ENT_QUOTES, 'UTF-8') ?>">
    <?php require ModuleHelper::getLayoutPath('mod_digi_artcats', $params->get('layout', 'default') . '_items'); ?>
</ul>


