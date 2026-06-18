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
    private const VISIBLE_SLOTS = 7;
    private const TARGET_START_INDEX = 34;

    public function index(Request $request)
    {
        $selectedCount = (int) $request->query('prizes', 1);

        if (!in_array($selectedCount, self::PRIZE_COUNTS, true)) {
            $selectedCount = 1;
        }

        $products = Product::where('quantity', '>', 0)->orderBy('name')->get();
        $availableUnique = $products->count();
        $totalQuantity = $products->sum('quantity');
        $totalValue = $products->sum(fn (Product $product) => $product->quantity * $product->value);
        $averageValue = $totalQuantity > 0 ? $totalValue / $totalQuantity : 0;
        $canSpin = $availableUnique >= $selectedCount;
        $prizes = $canSpin ? $this->pickUniquePrizes($products, $selectedCount) : collect();
        $reelItems = $canSpin ? $this->buildReelItems($products, $prizes, $selectedCount) : collect();
        $availableCounts = collect(self::PRIZE_COUNTS)
            ->filter(fn (int $count) => $availableUnique >= $count)
            ->values();

        return view('fortune-two', [
            'allowedCounts' => self::PRIZE_COUNTS,
            'availableCounts' => $availableCounts,
            'availableUnique' => $availableUnique,
            'averageValue' => $averageValue,
            'canSpin' => $canSpin,
            'selectedCount' => $selectedCount,
            'prizes' => $prizes->map(fn (Product $product) => $this->presentProduct($product))->values(),
            'reelItems' => $reelItems,
            'targetStartIndex' => self::TARGET_START_INDEX,
            'visibleSlots' => self::VISIBLE_SLOTS,
            'arrowSlotIndexes' => $this->arrowSlotIndexes($selectedCount),
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

    private function buildReelItems(Collection $products, Collection $prizes, int $selectedCount): Collection
    {
        $items = collect();
        $beforeCount = self::TARGET_START_INDEX;
        $afterCount = 42;

        for ($i = 0; $i < $beforeCount; $i++) {
            $items->push($this->presentProduct($this->randomProduct($products)));
        }

        foreach ($prizes as $product) {
            $items->push($this->presentProduct($product));
        }

        for ($i = 0; $i < $afterCount; $i++) {
            $items->push($this->presentProduct($this->randomProduct($products)));
        }

        return $items->values();
    }

    private function randomProduct(Collection $products): Product
    {
        return $products->values()->get(random_int(0, $products->count() - 1));
    }

    private function arrowSlotIndexes(int $selectedCount): array
    {
        $center = intdiv(self::VISIBLE_SLOTS, 2);
        $start = $center - intdiv($selectedCount, 2);

        return range($start, $start + $selectedCount - 1);
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
