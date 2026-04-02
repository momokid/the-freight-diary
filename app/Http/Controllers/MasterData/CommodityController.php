<?php

namespace App\Http\Controllers\MasterData;

use App\Http\Controllers\Controller;
use App\Models\CommodityCategory;
use App\Models\CommodityType;
use Illuminate\Http\Request;

class CommodityController extends Controller
{
    public function index()
    {
        $categories = CommodityCategory::with('types')
            ->orderBy('CategoryName')
            ->get();

        return view('master-data.commodities', compact('categories'));
    }

    // ── Category ──
    public function storeCategory(Request $request)
    {
        $request->validate([
            'CategoryName' => ['required', 'string', 'max:50', 'unique:commodity_category,CategoryName'],
        ]);

        $category = CommodityCategory::create([
            'CategoryName' => trim($request->CategoryName),
        ]);

        return response()->json([
            'success'      => true,
            'message'      => 'Category added successfully.',
            'ID'           => $category->ID,
            'CategoryName' => $category->CategoryName,
        ]);
    }

    // ── Type ──
    public function storeType(Request $request)
    {
        $request->validate([
            'CategoryID' => ['required', 'integer', 'exists:commodity_category,ID'],
            'TypeName'   => ['required', 'string', 'max:500'],
        ]);

        $type = CommodityType::create([
            'CategoryID' => $request->CategoryID,
            'TypeName'   => trim($request->TypeName),
        ]);

        return response()->json([
            'success'  => true,
            'message'  => 'Commodity type added successfully.',
            'TypeID'   => $type->TypeID,
            'TypeName' => $type->TypeName,
        ]);
    }

    public function destroyType(int $id)
    {
        CommodityType::findOrFail($id)->delete();

        return response()->json(['success' => true, 'message' => 'Commodity type removed successfully.']);
    }
}
