<?php

/**
 * @package     Joomla.Site
 * @subpackage  mod_digi_artcats
 * @copyright   (C) Open Source Matters, Inc.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

use Joomla\CMS\Factory;
use Joomla\CMS\Helper\ModuleHelper;
use Joomla\Module\DigiArtCats\Site\Helper\DigiArtCatsHelper;
use Joomla\CMS\Category\CategoryFactoryInterface;

defined('_JEXEC') or die;

// Access application and input
$app = Factory::getApplication();
$input = $app->getInput();

// Override menu item if requested
if ($params->get('override_menuitemid')) {
    $app->getMenu('site')->setActive((int) $params->get('override_menuitemid'));
}

// Determine base category ID
$idbase = null;
$list = [];
$mode = $params->get('mode', 'dynamic');
$option = $input->getCmd('option');
$view = $input->getCmd('view');

switch ($mode) {
    case 'dynamic':
        if ($option === 'com_content') {
            switch ($view) {
                case 'category':
                case 'categories':
                    $idbase = $input->getInt('id');
                    break;

                case 'article':
                    if ($params->get('show_on_article_page', 1)) {
                        $articleId = $input->getInt('id');
                        $db = Factory::getDbo();
                        $query = $db->getQuery(true)
                            ->select($db->quoteName('catid'))
                            ->from($db->quoteName('#__content'))
                            ->where($db->quoteName('id') . ' = :id')
                            ->bind(':id', $articleId, \Joomla\Database\ParameterType::INTEGER);
                        $db->setQuery($query);
                        $idbase = $db->loadResult();
                    }
                    break;
            }
        }
        break;

    case 'normal':
    default:
        $idbase = $params->get('parent');
        break;
}

// Set parent category if dynamic
if (!empty($idbase) && $mode === 'dynamic') {
    $params->set('parent', $idbase);
}

// Get category list
if (!empty($idbase)) {
    $list = DigiArtCatsHelper::getList($params, $module);
}

// Render layout if list is available
if (!empty($list)) {
    $moduleclass_sfx = htmlspecialchars($params->get('moduleclass_sfx'), ENT_QUOTES, 'UTF-8');

    $categoryFactory = Factory::getContainer()->get(CategoryFactoryInterface::class);
    $category = $categoryFactory->createCategory([
        'extension' => 'com_content',
        'id' => (int) $params->get('parent', 'root')
    ]);

    $startLevel = $category->level ?? 1;

    require ModuleHelper::getLayoutPath('mod_digi_artcats', $params->get('layout', 'default'));
}
