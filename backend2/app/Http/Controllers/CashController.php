<?php

namespace App\Http\Controllers;

use App\Models\CashUnit;
use Illuminate\Http\Request;

class CashController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return CashUnit::orderByDesc('denomination')->get();
    }

    public function store(Request $request)
    {
        $request->validate([
            'denomination' => 'required|integer|min:1',
            'quantity'     => 'required|integer|min:0',
        ]);

        return CashUnit::create($request->only(['denomination', 'quantity']));
    }

    public function update(Request $request, CashUnit $cashUnit)
    {
        $request->validate([
            'quantity' => 'required|integer|min:0',
        ]);

        $cashUnit->update($request->only('quantity'));
        return $cashUnit;
    }

    public function destroy(CashUnit $cashUnit)
    {
        $cashUnit->delete();
        return response()->noContent();
    }
}
