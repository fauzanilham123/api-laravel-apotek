<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use App\Http\Resources\SendError;
use App\Http\Controllers\Controller;
use App\Http\Resources\SendResponse;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Validator;

class UserController extends Controller
{
    //
    public function index(Request $request)
    {
        // Mulai query dasar untuk users
        $query = User::where("flag", 1);

        // Ambil semua kolom dari tabel users
        $columns = Schema::getColumnListing('users');

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

        $users = $query->latest()->paginate($limit, ['*'], 'page', $page);
        //return collection of users as a resource
        return new SendResponse('List Data Users', $users);
    }

    public function show($id)
    {
        //find user by ID
        $user = User::find($id);

        //check if user exists
        if (!$user) {
            // return response if user is not found
            return new SendError(404, 'User not found', null); // You can customize this based on your SendError class
        }

        //return single user as a resource
        return new SendResponse('Detail data user', $user);
    }
}