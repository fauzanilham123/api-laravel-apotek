<?php

namespace App\Http\Controllers;

use App\Models\recipe;
use App\Models\transaction;
use Illuminate\Http\Request;
use App\Http\Resources\SendError;
use App\Http\Controllers\Controller;
use App\Http\Resources\SendResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Validator;

class CashierController extends Controller
{
    //
    public function index(Request $request)
    {
        // Mulai query dasar untuk recipe
        $query = recipe::with('obat')->where("flag", 1)->where("transaksi", 0);

        // Ambil semua kolom dari tabel recipe
        $columns = Schema::getColumnListing('recipes');

        // Loop melalui setiap kolom dan tambahkan kondisi jika ada input yang sesuai di request
        foreach ($columns as $column) {
            if ($request->has($column) && !is_null($request->input($column))) {
                $query->where($column, 'LIKE', "%" . $request->input($column) . "%");
            }
        }

        // Dapatkan nilai `page` dan `limit` dari request, gunakan default jika tidak ada
        $page = $request->input('page', 1);
        $limit = $request->input('limit', 10);
        $sortBy = $request->input('sort_by', 'created_at'); // Default sort by 'created_at'
        $orderBy = $request->input('order_by', 'desc'); // Default order by 'desc'

        // Validasi `sort_by` agar hanya bisa menggunakan kolom yang ada di tabel
        if (in_array($sortBy, $columns)) {
            $query->orderBy($sortBy, $orderBy);
        }

        $recipes = $query->latest()->paginate($limit, ['*'], 'page', $page);
        //return collection of recipe as a resource
        return new SendResponse('List Data recipes', $recipes);
    }

    public function store(Request $request)
    {
        // Validate the request to ensure id_recipe is provided
        $validator = Validator::make($request->all(), [
            'id_recipe' => 'required|exists:recipes,id',
        ]);

        if ($validator->fails()) {
            return new SendError(
                422,
                "error",
                $validator->errors()
            );
        }

        // Retrieve the recipe and its related drugs
        $recipe = Recipe::with('obat')->find($request->id_recipe);

        // Initialize total price
        $totalPrice = $recipe->jumlah_obat * $recipe->obat->harga;

        $randomTransactionNumber = str_pad(mt_rand(1, 99999999999), 11, '0', STR_PAD_LEFT);

        // Prepare transaction data
        $transactionData = [
            'id_recipe' => $recipe->id,
            'id_user' => Auth::id(),
            'id_drug' => $recipe->obat->id,
            'no' => $randomTransactionNumber,
            'total_bayar' => $totalPrice,
            'flag' => 1,
        ];

        // Create the transaction record
        $transaction = transaction::create($transactionData);
        if ($transaction->id_recipe) {
            recipe::where('id', $transaction->id_recipe)->update(['transaksi' => true]);
        };
        return new SendResponse("success", $transaction);
    }
}