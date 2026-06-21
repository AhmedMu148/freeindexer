<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\DB;

class PageMetaServiceProvider extends ServiceProvider
{
  public function boot()
  {
    View::composer('*', function ($view) {

      // Get page meta data and make it available to all views
      $pageMetas = $this->getPageMetas();

      // Get current page name
      $currentPage = $this->getCurrentPageName();

      // Get meta for current page
      $currentPageMeta = $pageMetas->get($currentPage);
      if (!$currentPageMeta) {
        $currentPageMeta = (object) [
          'title'       => 'Free Indexer',
          'description' => 'Free Indexer is a tool to help you get your URLs indexed by search engines.',
          'keywords'    => 'Free Indexer, SEO, indexing',
        ];
      }
      $view->with([
        'PageMeta' => $currentPageMeta,
      ]);
    });
  }

  private function getPageMetas()
  {
    return DB::table('page_metas')->get()->keyBy('page');
  }

  private function getCurrentPageName()
  {
    $requestUri = $_SERVER['REQUEST_URI'] ?? '/';
    $path       = parse_url($requestUri, PHP_URL_PATH);
    // Remove leading slash
    $page       = ltrim($path, '/');

    // If empty, set to index
    if (empty($page)) $page = '/';

    return $page;
  }

  public function register()
  {
    //
  }
}
