<?php

namespace App\Http\Controllers\Shop;

use App\Http\Controllers\Controller;
use Illuminate\View\View;

class PageController extends Controller
{
    public const PAGES = ['impressum', 'terms', 'privacy', 'shipping-returns'];

    public function show(string $page): View
    {
        abort_unless(in_array($page, self::PAGES, true), 404);

        $view = 'pages.'.app()->getLocale().'.'.$page;

        return view(view()->exists($view) ? $view : 'pages.en.'.$page, ['page' => $page]);
    }
}
