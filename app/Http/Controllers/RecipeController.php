<?php

namespace App\Http\Controllers;

use App\Models\drug;
use App\Models\recipe;
use Illuminate\Http\Request;
use App\Http\Resources\SendError;
use App\Http\Resources\SendResponse;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Validator;

class RecipeController extends Controller
{
    public function index(Request $request)
    {
        // Mulai query dasar untuk recipe
        $query = recipe::with('obat')->where("flag", 1);

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
        //define validation rules
        $validator = Validator::make($request->all(), [
            'no'   => 'required|unique:recipes,no',
            'date'     => 'required|date',
            'nama_dokter'     => 'required',
            'nama_pasien'     => 'required',
            'id_obat'     => 'required|integer|exists:drugs,id',
            'jumlah_obat'     => 'required|numeric|min:1',
        ]);

        //check if validation fails
        if ($validator->fails()) {
            return new SendError(422, "error", $validator->errors());
        }

        $drug = drug::find(id: $request->id_obat);
        $jumlah_obat_tersedia = $drug->jumlah;

        // Memeriksa apakah jumlah_obat yang diminta melebihi jumlah obat yang tersedia
        if ($request->jumlah_obat > $jumlah_obat_tersedia) {
            return new SendError(422, "error", "The number of drugs exceeds the available stock.");
        }

        $expired_date = $drug->expired_date;

        $today = date('Y-m-d');

        if ($expired_date < $today) {
            return new SendError(422, "error", "The medicine has expired.");
        }

        //create recipe
        $recipe = recipe::create([
            'no'     => $request->no,
            'date'     => $request->date,
            'nama_dokter'     => $request->nama_dokter,
            'nama_pasien'     => $request->nama_pasien,
            'id_obat'     => $request->id_obat,
            'jumlah_obat'     => $request->jumlah_obat,
            'flag' => 1,
        ]);

        $recipe['updated_at'] = null; // Atur updated_at ke null saat insert

        // Mengurangi jumlah obat yang tersedia di tabel obat
        $drug->update(['jumlah' => $jumlah_obat_tersedia - $request->jumlah_obat]);

        //return response
        return new SendResponse('Data Added Successfully!', $recipe);
    }

    public function show($id)
    {
        //find recipe by ID
        $recipe = recipe::with('obat')->find($id);

        //check if recipe exists
        if (!$recipe) {
            // return response if recipe is not found
            return new SendError(404, 'recipe not found', null); // You can customize this based on your SendError class
        }

        //return single recipe as a resource
        return new SendResponse('Detail data recipe', $recipe);
    }

    /**
     * update
     *
     * @param  mixed $request
     * @param  mixed $id
     * @return void
     */
    public function update(Request $request, string $id): SendResponse|SendError
    {
        //define validation rules
        $validator = Validator::make($request->all(), [
            'no'   => 'required|unique:recipes,no',
            'date'     => 'required|date',
            'nama_dokter'     => 'required',
            'nama_pasien'     => 'required',
            'id_obat'     => 'required|integer|exists:drugs,id',
            'jumlah_obat'     => 'required|numeric|min:1',
        ]);

        //check if validation fails
        if ($validator->fails()) {
            // return new SendError(422, "error", $validator->errors());
            return new SendError(422, "error", $validator->errors());
        }
        $recipes = recipe::findOrFail($id);

        // Dapatkan jumlah obat sebelum diupdate
        $jumlah_obat_sebelumnya = $recipes->jumlah_obat;

        // Hitung selisih jumlah obat baru dengan jumlah obat sebelumnya
        $selisih_jumlah_obat = $request->jumlah_obat - $jumlah_obat_sebelumnya;

        // Mengambil jumlah obat yang tersedia dari tabel obat
        $drug = Drug::find($request->id_obat);
        $jumlah_obat_tersedia = $drug->jumlah;

        // Memeriksa apakah jumlah_obat yang diminta melebihi jumlah obat yang tersedia
        if ($selisih_jumlah_obat > $jumlah_obat_tersedia) {
            return new SendError(422, "error", "The number of drugs exceeds the available stock.");
        }

        $expired_date = $drug->expired_date;
        $today = date('Y-m-d');

        if ($expired_date < $today) {
            return new SendError(422, "error", "The medicine has expired.");
        }

        //find recipe by ID
        $recipe = recipe::find($id);
        if (!$recipe) {
            // return response if recipe is not found
            return new SendError(404, 'recipe not found', null); // You can customize this based on your SendError class
        }
        //update recipe without image
        $recipe->update([
            'no'     => $request->no,
            'date'     => $request->date,
            'nama_dokter'     => $request->nama_dokter,
            'nama_pasien'     => $request->nama_pasien,
            'id_obat'     => $request->id_obat,
            'jumlah_obat'     => $request->jumlah_obat,
        ]);

        $drug->update([
            'jumlah' => $jumlah_obat_tersedia - $selisih_jumlah_obat,
        ]);

        //return response
        return new SendResponse('success', $recipe);
    }

    public function destroy($id)
    {
        // Find recipe by ID or fail
        $recipe = recipe::findOrFail($id);
        if (!$recipe) {
            return new SendError(404, 'recipe not found', null);
        }
        // Update the 'flag' field to 0
        $recipe->update(['flag' => 0]);

        // Return response
        return new SendResponse('success', $recipe);
    }
}