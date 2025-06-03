<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\Product;
use App\Models\CashUnit;

class VendingController extends Controller
{
    // POST /insert-coin
    public function insertCoin(Request $request)
    {
        $denomination = $request->input('denomination');
        $quantity = $request->input('quantity', 1);
        $validDenominations = [1, 5, 10, 20, 50, 100, 500, 1000];

        if (!in_array($denomination, $validDenominations)) {
            return response()->json(['message' => 'ชนิดเงินไม่รองรับ'], 400);
        }

        $currentBalance = session('current_balance', 0);
        $currentBalance += $denomination * $quantity;
        session(['current_balance' => $currentBalance]);

        // บันทึกเหรียญเข้าเครื่อง
        $cashUnit = CashUnit::firstOrCreate(['denomination' => $denomination]);
        $cashUnit->increment('quantity', $quantity);

        return response()->json([
            'message' => "เพิ่มเงิน {$denomination} บาทแล้ว",
            'current_balance' => $currentBalance,
        ]);
    }

    // POST /purchase
    public function purchase(Request $request)
    {
        $productId = $request->input('product_id');
        $currentBalance = session('current_balance', 0);

        $product = Product::find($productId);
        if (!$product) {
            return response()->json(['message' => 'สินค้าไม่พบ'], 404);
        }

        if ($product->stock <= 0) {
            return response()->json(['message' => 'สินค้าหมด'], 400);
        }

        if ($currentBalance < $product->price) {
            return response()->json(['message' => 'ยอดเงินไม่เพียงพอ'], 400);
        }

        $changeDetails = [];
        DB::beginTransaction();
        try {
            $newBalance = $currentBalance - $product->price;
            $change = $this->calculateChange($newBalance);

            if ($change === false) {
                DB::rollBack();
                return response()->json(['message' => 'ไม่สามารถทอนเงินได้'], 400);
            }

            // ทอนเงิน: ลดจำนวนเหรียญที่ใช้ทอน
            foreach ($change as $denom => $qty) {
                $cashUnit = CashUnit::where('denomination', $denom)->first();
                if ($cashUnit) {
                    $cashUnit->decrement('quantity', $qty);
                    $changeDetails[] = [
                        'denomination' => $denom,
                        'quantity' => $qty
                    ];
                }
            }

            session(['current_balance' => 0]); // reset balance หลังทอนเงิน

            // เพิ่มเงินเข้าเครื่องตามราคาสินค้า
            $remaining = $product->price;
            $denominations = CashUnit::orderByDesc('denomination')->get();
            foreach ($denominations as $unit) {
                $count = intdiv($remaining, $unit->denomination);
                if ($count > 0) {
                    $unit->quantity += $count;
                    $unit->save();
                    $remaining %= $unit->denomination;
                }
            }

            $product->decrement('stock');

            $history = session('purchase_history', []);
            $history[] = [
                'product_id' => $product->id,
                'name' => $product->name,
                'price' => $product->price,
                'balance_after' => 0,
                'time' => now()->toDateTimeString()
            ];
            session(['purchase_history' => $history]);

            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['message' => 'เกิดข้อผิดพลาดขณะซื้อสินค้า'], 500);
        }

        return response()->json([
            'message' => 'ซื้อสินค้าเรียบร้อย',
            'current_balance' => session('current_balance'),
            'product' => [
                'id' => $product->id,
                'name' => $product->name,
                'total_paid' => $product->price,
            ],
            'change' => $newBalance,
            'change_details' => $changeDetails,
        ]);
    }

    // GET /status
    public function status()
    {
        $totalCash = CashUnit::sum(DB::raw('denomination * quantity'));
        $totalProducts = Product::sum('stock');

        return response()->json([
            'current_balance' => session('current_balance', 0),
            'total_cash' => $totalCash,
            'total_products' => $totalProducts,
        ]);
    }

    // GET /change-options
    public function changeOptions()
    {
        $balance = session('current_balance', 0);
        $change = $this->calculateChange($balance);

        return response()->json([
            'inserted' => $balance,
            'available_change' => $change !== false ? $change : [],
            'change_possible' => $change !== false,
        ]);
    }

    private function calculateChange($amount)
    {
        $denominations = CashUnit::orderByDesc('denomination')->get();
        $change = [];

        foreach ($denominations as $unit) {
            if ($amount <= 0) break;

            $useQty = min(intval($amount / $unit->denomination), $unit->quantity);

            if ($useQty > 0) {
                $change[$unit->denomination] = $useQty;
                $amount -= $useQty * $unit->denomination;
            }
        }

        return $amount === 0 ? $change : false;
    }

    // GET /history
    public function history()
    {
        return response()->json([
            'history' => session('purchase_history', [])
        ]);
    }
}
