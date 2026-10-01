<?php

namespace App\Http\Controllers\Shop;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\DeviceModel;
use App\Models\Product;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\View\View;

class CatalogController extends Controller
{
    public function home(): View
    {
        return view('shop.home', [
            'products' => Product::visible()->with('images', 'variants')->latest()->take(8)->get(),
        ]);
    }

    /**
     * Product list, optionally narrowed to a category, a phone model and a search term.
     */
    public function index(Request $request, ?Category $category = null): View
    {
        $device = DeviceModel::where('slug', $request->query('device'))->first();
        $search = trim((string) $request->query('q'));

        $products = Product::visible()
            ->with('images', 'variants')
            ->when($category, fn (Builder $query) => $query->where('category_id', $category->id))
            ->when($device, fn (Builder $query) => $query->whereHas('deviceModels', fn (Builder $models) => $models->whereKey($device->id)))
            ->when($search !== '', function (Builder $query) use ($search) {
                $like = '%'.str_replace(['%', '_'], ['\%', '\_'], mb_strtolower($search)).'%';

                $query->where(fn (Builder $query) => $query
                    ->whereRaw('lower(name) like ?', [$like])
                    ->orWhereRaw('lower(description) like ?', [$like]));
            })
            ->latest()
            ->paginate(24)
            ->withQueryString();

        return view('shop.products', [
            'products' => $products,
            'category' => $category,
            'device' => $device,
            'search' => $search,
            'devices' => DeviceModel::orderBy('brand')->orderBy('position')->orderBy('name')->get()->groupBy('brand'),
        ]);
    }

    public function show(Product $product): View
    {
        abort_unless($product->is_active, 404);

        $product->load(['images', 'deviceModels', 'category', 'variants' => fn ($query) => $query->where('is_active', true)->orderBy('price')]);

        abort_if($product->variants->isEmpty(), 404);

        return view('shop.product', ['product' => $product]);
    }
}
