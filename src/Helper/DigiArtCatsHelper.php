<?php

namespace Joomla\Module\DigiArtCats\Site\Helper;

use Joomla\CMS\Factory;
use Joomla\CMS\Router\Route;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Component\ComponentHelper;
use Joomla\CMS\Category\CategoryFactoryInterface;
use Joomla\CMS\User\User;
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\Component\Content\Site\Model\ArticlesModel;
use Joomla\Component\Content\Site\Model\CategoriesModel;
use Joomla\Registry\Registry;

/**
 * Helper class for DigiArtCats module
 */
class DigiArtCatsHelper
{
    public static function getList(Registry $params, ?object $module = null): array
    {
        $container = Factory::getContainer();
        $categoryFactory = $container->get(CategoryFactoryInterface::class);
        $user = Factory::getApplication()->getIdentity();
        $authorised = $user->getAuthorisedViewLevels();

        $parentId = (int) $params->get('parent', 1);
        $category = $categoryFactory->createCategory([
            'extension' => 'com_content',
            'id' => $parentId,
            'access' => true,
            'published' => true,
            'countItems' => $params->get('show_empty_categories', 1) || $params->get('show_category_count', 0),
        ]);

        if ($module && $params->get('title_mode', 1)) {
            $link = Route::_(\Joomla\Component\Content\Site\Helper\RouteHelper::getCategoryRoute($category->id));
            $module->title = '<a href="' . $link . '">' . htmlspecialchars($category->title) . '</a>';
        }

        $items = $category->getChildren();
        $isLeaf = empty($items);

        if ($isLeaf && !$params->get('show_children', 0)) {
            $items = $category->getParent()->getChildren();
        }

        // Ordering
        usort($items, fn($a, $b) => strcmp($a->title, $b->title));
        $count = (int) $params->get('count', 0);
        if ($count > 0 && count($items) > $count) {
            $items = array_slice($items, 0, $count);
        }

        $listMode = $params->get('list_mode', 'combined');
        if ($listMode === 'combined' || $listMode === 'articles') {
            $articles = self::getArticleList($params);

            if ($module && $isLeaf && count($articles) === 0 && $params->get('title_mode', 1)) {
                $parent = $category->getParent();
                $link = Route::_(\Joomla\Component\Content\Site\Helper\RouteHelper::getCategoryRoute($parent->id));
                $title = $parent->id === 'root' ? 'Home' : $parent->title;
                $module->title = '<a href="' . $link . '">' . htmlspecialchars($title) . '</a>';
            }

            // Format articles
            $showDate = $params->get('show_date', 0);
            $dateField = $params->get('show_date_field', 'created');
            $dateFormat = $params->get('show_date_format', 'Y-m-d H:i:s');

            foreach ($articles as &$article) {
                $article->level = $category->level + 1;
                $article->slug = $article->id . ':' . $article->alias;
                $article->catslug = $article->catid . ':' . $article->category_alias;

                if (in_array($article->access, $authorised)) {
                    $article->link = Route::_(\Joomla\Component\Content\Site\Helper\RouteHelper::getArticleRoute($article->slug, $article->catslug));
                } else {
                    $article->link = Route::_('index.php?option=com_users&view=login');
                }

                $article->displayDate = $showDate ? HTMLHelper::_('date', $article->$dateField, $dateFormat) : '';
            }

            // Merge logic
            if (($isLeaf && count($articles)) || $listMode === 'articles') {
                $items = $articles;
            } else {
                $order = $params->get('article_category_ordering', 'Art');
                $items = $order === 'Cat' ? array_merge($items, $articles) : array_merge($articles, $items);
            }
        }

        return $items;
    }

    public static function getArticleList(Registry $params): array
    {
        $model = new ArticlesModel(['ignore_request' => true]);
        $app = Factory::getApplication();
        $model->setState('params', $app->getParams());
        $model->setState('list.start', 0);
        $model->setState('list.limit', (int) $params->get('max_articles', 0));
        $model->setState('filter.published', 1);
        $model->setState('filter.access', !$params->get('show_noauth'));
        $model->setState('filter.category_id.include', true);
        $model->setState('filter.category_id', $params->get('parent', 1));
        $model->setState('list.ordering', $params->get('article_ordering', 'a.ordering'));
        $model->setState('list.direction', $params->get('article_ordering_direction', 'ASC'));
        $model->setState('filter.featured', $params->get('featured_only', 'show'));
        $model->setState('filter.language', $app->getLanguageFilter());

        // Child categories
        if ($params->get('show_child_category_articles', 0) && (int) $params->get('maxlevel', 0) > 0) {
            $catModel = new CategoriesModel(['ignore_request' => true]);
            $catModel->setState('params', $app->getParams());
            $catModel->setState('filter.get_children', $params->get('maxlevel', 1));
            $catModel->setState('filter.published', 1);
            $catModel->setState('filter.access', !$params->get('show_noauth'));

            $catids = (array) $params->get('parent', 1);
            $additional = [];

            foreach ($catids as $catid) {
                $catModel->setState('filter.parentId', $catid);
                foreach ($catModel->getItems(true) as $cat) {
                    $additional[] = $cat->id;
                }
            }

            $model->setState('filter.category_id', array_unique(array_merge($catids, $additional)));
        }

        return $model->getItems() ?? [];
    }
}
