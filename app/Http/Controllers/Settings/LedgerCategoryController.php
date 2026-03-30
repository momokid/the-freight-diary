<?php

namespace App\Http\Controllers\Settings;

use App\Http\Controllers\Controller;
use App\Models\LedgerCategory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LedgerCategoryController extends Controller
{
    public function index()
    {
        $activeSubCategories   = LedgerCategory::active()->orderBy('CategoryID')->orderBy('SubCategoryID')->get();
        $inactiveSubCategories = LedgerCategory::inactive()->orderBy('CategoryID')->orderBy('SubCategoryID')->get();
        $categories            = LedgerCategory::getDistinctCategories();

        return view('settings.ledger-category', compact(
            'activeSubCategories',
            'inactiveSubCategories',
            'categories'
        ));
    }

    // Store a new parent category
    public function storeCategory(Request $request)
    {
        $request->validate([
            'CategoryName' => ['required', 'string', 'max:100'],
            'Class'        => ['required', 'in:Dr,Cr'],
            'Type'         => ['required', 'in:GL,INCOME,EXPENDITURE'],
        ]);

        // Check if category name already exists
        $exists = LedgerCategory::where('CategoryName', strtoupper(trim($request->CategoryName)))->exists();

        if ($exists) {
            return response()->json([
                'success' => false,
                'message' => 'A category with this name already exists.',
            ], 409);
        }

        $categoryID   = LedgerCategory::getNextCategoryID();
        $categoryName = strtoupper(trim($request->CategoryName));

        // We create a placeholder subcategory row to anchor the new category
        // This ensures the category appears in the distinct categories list
        LedgerCategory::create([
            'CategoryID'      => $categoryID,
            'CategoryName'    => $categoryName,
            'SubCategoryName' => $categoryName, // placeholder — same as category name
            'Class'           => $request->Class,
            'Type'            => $request->Type,
            'Username'        => Auth::user()->ID,
            'Date'            => now()->toDateString(),
            'Time'            => now()->toDateTimeString(),
            'Status'          => 1,
        ]);

        return response()->json([
            'success'      => true,
            'message'      => 'Category added successfully.',
            'CategoryID'   => $categoryID,
            'CategoryName' => $categoryName,
            'Class'        => $request->Class,
            'Type'         => $request->Type,
        ]);
    }

    // Store a new subcategory
    public function storeSubCategory(Request $request)
    {
        $request->validate([
            'CategoryID'      => ['required', 'integer'],
            'CategoryName'    => ['required', 'string', 'max:100'],
            'SubCategoryName' => ['required', 'string', 'max:100'],
            'Class'           => ['required', 'in:Dr,Cr'],
            'Type'            => ['required', 'in:GL,INCOME,EXPENDITURE'],
        ]);

        // Check for duplicate subcategory name under the same category
        $exists = LedgerCategory::where('CategoryID', $request->CategoryID)
            ->where('SubCategoryName', strtoupper(trim($request->SubCategoryName)))
            ->exists();

        if ($exists) {
            return response()->json([
                'success' => false,
                'message' => 'A subcategory with this name already exists under the selected category.',
            ], 409);
        }

        LedgerCategory::create([
            'CategoryID'      => $request->CategoryID,
            'CategoryName'    => strtoupper(trim($request->CategoryName)),
            'SubCategoryName' => strtoupper(trim($request->SubCategoryName)),
            'Class'           => $request->Class,
            'Type'            => $request->Type,
            'Username'        => Auth::user()->ID,
            'Date'            => now()->toDateString(),
            'Time'            => now()->toDateTimeString(),
            'Status'          => 1,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Subcategory added successfully.',
        ]);
    }

    // Inline edit subcategory name
    public function update(Request $request, int $id)
    {
        $request->validate([
            'SubCategoryName' => [
                'required',
                'string',
                'max:100',
            ],
        ]);

        $subCategory = LedgerCategory::findOrFail($id);

        // Check for duplicate under same category excluding current record
        $exists = LedgerCategory::where('CategoryID', $subCategory->CategoryID)
            ->where('SubCategoryName', strtoupper(trim($request->SubCategoryName)))
            ->where('SubCategoryID', '!=', $id)
            ->exists();

        if ($exists) {
            return response()->json([
                'success' => false,
                'message' => 'A subcategory with this name already exists under the same category.',
            ], 409);
        }

        $subCategory->SubCategoryName = strtoupper(trim($request->SubCategoryName));
        $subCategory->save();

        return response()->json([
            'success'         => true,
            'message'         => 'Subcategory updated successfully.',
            'SubCategoryName' => $subCategory->SubCategoryName,
        ]);
    }

    // Soft delete
    public function deactivate(int $id)
    {
        $subCategory         = LedgerCategory::findOrFail($id);
        $subCategory->Status = 0;
        $subCategory->save();

        return response()->json([
            'success' => true,
            'message' => 'Subcategory deactivated successfully.',
        ]);
    }

    // Restore
    public function restore(int $id)
    {
        $subCategory         = LedgerCategory::findOrFail($id);
        $subCategory->Status = 1;
        $subCategory->save();

        return response()->json([
            'success' => true,
            'message' => 'Subcategory restored successfully.',
        ]);
    }
}
