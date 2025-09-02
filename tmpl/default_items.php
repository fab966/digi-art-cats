<?php

use Joomla\CMS\Factory;
use Joomla\CMS\Helper\ModuleHelper;
use Joomla\CMS\Router\Route;
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\Component\Content\Site\Helper\RouteHelper;

defined('_JEXEC') or die;

$useHeadings = (bool) $params->get('use_headings', 1);
$showEmptyCategories = (bool) $params->get('show_empty_categories', 1);
$showCategoryCount = (bool) $params->get('show_category_count', 0);
$linkCategories = (bool) $params->get('link_categories', 1);
$itemHeadingBase = (int) $params->get('item_heading', 4);

$app = Factory::getApplication();
$input = $app->getInput();
$currentId = $input->getInt('id');
$option = $input->getCmd('option');
$view = $input->getCmd('view');

foreach ($list as $item) {
    if (!$showEmptyCategories && !isset($item->catid) && $item->getNumItems(true) === 0) {
        continue;
    }

    $levelup = $item->level - $startLevel - 1;
    $currentLevel = max(1, $item->level - $startLevel);
    $parentClass = (!isset($item->catid) && method_exists($item, 'getChildren') && count($item->getChildren())) ? 'parent' : '';

    $isActive = (
        ($option === 'com_content' && $view === 'article' && isset($item->catid) && $item->id === $currentId) ||
        ($idbase === $item->id && !isset($item->catid))
    ) ? 'active current' : '';

    $itemClass = 'item' . $item->id . ' ' . $isActive . ' level' . $currentLevel . ' ' . $parentClass;

    echo '<li class="' . htmlspecialchars($itemClass, ENT_QUOTES, 'UTF-8') . '">';

    if ($useHeadings) {
        echo '<h' . ($itemHeadingBase + $levelup) . '>';
    }

    $link = isset($item->catid)
        ? $item->link
        : Route::_(RouteHelper::getCategoryRoute($item->id));

    if (isset($item->catid) || $linkCategories) {
        echo '<a class="level' . $currentLevel . ' ' . $isActive . '" href="' . htmlspecialchars($link, ENT_QUOTES, 'UTF-8') . '">';
    }

    echo '<span>' . htmlspecialchars($item->title, ENT_QUOTES, 'UTF-8');

    if ($showCategoryCount && !isset($item->catid)) {
        echo ' (' . $item->getNumItems(true) . ')';
    }

    echo '</span>';

    if (isset($item->catid) || $linkCategories) {
        echo '</a>';
    }

    if ($useHeadings) {
        echo '</h' . ($itemHeadingBase + $levelup) . '>';
    }

    if (!empty($item->displayDate)) {
        echo '<span class="mod-digi_artcats-date">' . htmlspecialchars($item->displayDate, ENT_QUOTES, 'UTF-8') . '</span>';
    }

    if (!isset($item->catid)) {
        if ($params->get('show_description', 0)) {
            echo HTMLHelper::_('content.prepare', $item->description, $item->getParams(), 'mod_digi_artcats.content');
        }

        $params->set('parent', $item->id);
        $subList = \Joomla\Module\DigiArtCats\Site\Helper\DigiArtCatsHelper::getList($params);

        $maxLevel = (int) $params->get('maxlevel', 0);
        if (
            $params->get('show_children', 0) &&
            ($maxLevel === 0 || $maxLevel > ($item->level - $startLevel)) &&
            count($subList)
        ) {
            echo '<ul>';
            $temp = $list;
            $list = $subList;
            require ModuleHelper::getLayoutPath('mod_digi_artcats', $params->get('layout', 'default') . '_items');
            $list = $temp;
            echo '</ul>';
        }
    }

    echo '</li>';
}
