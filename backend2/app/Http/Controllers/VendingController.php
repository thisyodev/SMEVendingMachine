<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Product;
use App\Models\CashUnit;
use App\Models\Transaction;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Log;
class VendingController extends Controller
{
    // POST /insert-coin
    public function insertCoin(Request $request) {
        $denomination = $request->input('denomination');
        $quantity = $request->input('quantity', 1);

        // รับเฉพาะที่ระบบรองรับ
        $validDenominations = [1, 5, 10, 20, 50, 100, 500, 1000];
        if (!in_array($denomination, $validDenominations)) {
            return response()->json(['message' => 'ชนิดเงินไม่รองรับ'], 400);
        }

        $currentBalance = session('current_balance', 0);
        $currentBalance += $denomination * $quantity;
        session(['current_balance' => $currentBalance]);

        // บันทึกเหรียญเข้า cash_units
        $cashUnit = CashUnit::firstOrCreate(['denomination' => $denomination]);
        $cashUnit->increment('quantity', $quantity);

        return response()->json([
            'message' => "เพิ่มเงิน {$denomination} บาทแล้ว",
            'current_balance' => $currentBalance,
        ]);
    }

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

        $changeAmount = $currentBalance - $product->price;
        $changeBreakdown = $this->calculateChange($changeAmount);
        if ($changeAmount > 0 && $changeBreakdown === false) {
            return response()->json(['message' => 'เครื่องไม่มีเงินทอนเพียงพอ'], 400);
        }

        DB::transaction(function () use ($product, $changeAmount, $changeBreakdown) {
            // ลด stock
            $product->stock -= 1;
            $product->save();

            // เพิ่มเงินเข้าเครื่อง
            foreach (CashUnit::all() as $unit) {
                if (session('current_balance') >= $unit->denomination) {
                    $unit->quantity += intdiv(session('current_balance'), $unit->denomination);
                    $unit->save();
                    session(['current_balance' => session('current_balance') % $unit->denomination]);
                }
            }

            // หักเงินทอนออกจากเครื่อง
            if ($changeBreakdown) {
                foreach ($changeBreakdown as $denomination => $qty) {
                    CashUnit::where('denomination', $denomination)->decrement('quantity', $qty);
                }
            }

            session(['current_balance' => 0]); // reset
        });

        return response()->json([
            'message' => 'ซื้อสินค้าเรียบร้อย',
            'current_balance' => 0,
            'product' => [
                'id' => $product->id,
                'name' => $product->name,
                'total_paid' => $product->price,
            ],
            'change' => $changeAmount,
            'change_breakdown' => $changeBreakdown ?? [],
        ]);
    }

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
        $inserted = session('current_balance', 0); // เปลี่ยนจาก inserted เป็น current_balance
        return response()->json([
            'inserted' => $inserted,
            'available_change' => $this->calculateChange($inserted),
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

    protected function getCurrentBalance()
    {
        return Session::get('current_balance', 0);
    }
}
