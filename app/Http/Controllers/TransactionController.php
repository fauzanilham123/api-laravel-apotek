<?php

namespace App\Http\Controllers;

use App\Models\transaction;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Resources\SendResponse;
use Illuminate\Support\Facades\Schema;

class TransactionController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        // Mulai query dasar untuk transaction
        $query = transaction::where("flag", 1);

        // Ambil semua kolom dari tabel transaction
        $columns = Schema::getColumnListing('transactions');

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

        $transaction = $query->latest()->paginate($limit, ['*'], 'page', $page);
        //return collection of transaction as a resource
        return new SendResponse('List Data Transaction', $transaction);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(transaction $transaction)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(transaction $transaction)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, transaction $transaction)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(transaction $transaction)
    {
        //
    }
}