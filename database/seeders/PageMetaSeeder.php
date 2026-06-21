<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class PageMetaSeeder extends Seeder
{
  public function run()
  {


    $pageMetas = [
      [
        'page' => '/',
        'title' => 'About US | Free Indexer',
        'keywords' => 'About US, FreeIndexer, backlinks, indexed on Google',
        'description' => 'FreeIndexer About US.'
      ],
      [
        'page' => 'buy-app.php',
        'title' => 'Desktop App | Free Indexer',
        'keywords' => 'powerful indexer, link indexer, backlink indexer, indexing backlinks, FreeIndexer, index software',
        'description' => 'FreeIndexer | Simple to use, yet powerful and flexible link indexer software!'
      ],
      [
        'page' => 'contact.php',
        'title' => 'Contact US | Free Indexer',
        'keywords' => 'Contact US, Free Indexer ',
        'description' => 'FreeIndexer Contact US.'
      ],
      [
        'page' => 'download-app.php',
        'title' => 'Download App | Free Indexer',
        'keywords' => 'Download indexer app, free download, app download, link indexing, FreeIndexer',
        'description' => 'Free Indexer windows App page, Download windows app!'
      ],
      [
        'page' => 'feedback-app.php',
        'title' => 'Feedback | Free Indexer',
        'keywords' => 'Indexer app, free indexer app, feedback, send feedback, FreeIndexer',
        'description' => 'FreeIndexer App Feedback page.'
      ],
      [
        'page' => 'index.php',
        'title' => 'Free Indexer | Rapid Indexer Online | Totally Free',
        'keywords' => 'Free Indexer, Get Indexed, Indexed in 24 hours, FreeIndexer, Free Backlinks, Backlinks, Backlink ',
        'description' => 'Free Indexer submits each one of your URLs backlinks to over 300 statistic sites. This gives you too many backlinks or link pyramids. And 100% get your URLs backlinks indexed by Google!'
      ],
      [
        'page' => 'index',
        'title' => 'Free Indexer | Rapid Indexer Online | Totally Free',
        'keywords' => 'Free Indexer, Get Indexed, Indexed in 24 hours, FreeIndexer, Free Backlinks, Backlinks, Backlink ',
        'description' => 'Free Indexer submits each one of your URLs backlinks to over 300 statistic sites. This gives you too many backlinks or link pyramids. And 100% get your URLs backlinks indexed by Google!'
      ]
    ];

    DB::table('page_metas')->insert($pageMetas);

    // foreach ($pageMetas as $meta) {
    //     PageMeta::create($meta);
    // }

  }
}
