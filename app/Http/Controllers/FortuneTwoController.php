<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class FortuneTwoController extends Controller
{
    private const PRIZE_COUNTS = [1, 3, 5];

    public function index(Request $request)
    {
        $selectedCount = (int) $request->query('prizes', 1);

        if (!in_array($selectedCount, self::PRIZE_COUNTS, true)) {
            $selectedCount = 1;
        }

        $products = Product::where('quantity', '>', 0)->orderBy('name')->get();
        $availableUnique = $products->count();
        $canSpin = $availableUnique >= $selectedCount;
        $prizes = $canSpin ? $this->pickUniquePrizes($products, $selectedCount) : collect();
        $availableCounts = collect(self::PRIZE_COUNTS)
            ->filter(fn (int $count) => $availableUnique >= $count)
            ->values();

        return view('fortune-two', [
            'allowedCounts' => self::PRIZE_COUNTS,
            'availableCounts' => $availableCounts,
            'availableUnique' => $availableUnique,
            'canSpin' => $canSpin,
            'selectedCount' => $selectedCount,
            'prizes' => $prizes->map(fn (Product $product) => $this->presentProduct($product))->values(),
            'wheelItems' => $this->buildWheelItems($products),
        ]);
    }

    public function catch(Request $request)
    {
        $validated = $request->validate([
            'product_ids' => 'required|array|min:1|max:5',
            'product_ids.*' => 'required|integer|distinct|exists:products,id',
        ]);

        $productIds = array_map('intval', $validated['product_ids']);

        if (!in_array(count($productIds), self::PRIZE_COUNTS, true)) {
            throw ValidationException::withMessages([
                'product_ids' => 'Можна забрати тільки 1, 3 або 5 призів.',
            ]);
        }

        DB::transaction(function () use ($productIds) {
            $products = Product::whereIn('id', $productIds)
                ->lockForUpdate()
                ->get()
                ->keyBy('id');

            foreach ($productIds as $productId) {
                $product = $products->get($productId);

                if (!$product || $product->quantity <= 0) {
                    throw ValidationException::withMessages([
                        'product_ids' => 'Один із призів вже недоступний. Запустіть колесо ще раз.',
                    ]);
                }
            }

            foreach ($productIds as $productId) {
                $product = $products->get($productId);
                $product->decrement('quantity');
            }
        });

        return redirect()
            ->route('fortune.two', ['prizes' => count($productIds)])
            ->with('success', 'Призи списано з бази.');
    }

    private function pickUniquePrizes(Collection $products, int $count): Collection
    {
        return $products
            ->map(function (Product $product) {
                $random = random_int(1, PHP_INT_MAX) / PHP_INT_MAX;

                return [
                    'product' => $product,
                    'rank' => log($random) / max(1, (int) $product->quantity),
                ];
            })
            ->sortByDesc('rank')
            ->pluck('product')
            ->take($count)
            ->values();
    }

    private function buildWheelItems(Collection $products): Collection
    {
        $items = $products
            ->shuffle()
            ->take(18)
            ->map(fn (Product $product) => $this->presentProduct($product))
            ->values();

        if ($items->isEmpty()) {
            return $items;
        }

        while ($items->count() < 12) {
            $items = $items->merge($items->shuffle())->take(12)->values();
        }

        return $items;
    }

    private function presentProduct(Product $product): array
    {
        return [
            'id' => $product->id,
            'name' => $product->name,
            'image_url' => $product->image_url,
            'quantity' => $product->quantity,
            'value' => $product->value,
        ];
    }
}
