<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Schema;
use App\Models\Drug;
use Illuminate\Http\Request;
use App\Http\Resources\SendError;
use App\Http\Resources\SendResponse;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class DrugController extends Controller
{
    public function index(Request $request)
    {
        // Mulai query dasar untuk drugs
        $query = Drug::where("flag", 1);

        // Ambil semua kolom dari tabel drugs
        $columns = Schema::getColumnListing('drugs');

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

        $drugs = $query->latest()->paginate($limit, ['*'], 'page', $page);
        //return collection of drugs as a resource
        return new SendResponse('List Data Drugs', $drugs);
    }

    public function store(Request $request)
    {
        //define validation rules
        $validator = Validator::make($request->all(), [
            'nama'   => 'required',
            'kode'     => 'required|unique:drugs,kode_obat',
            'expired_date'     => 'required|date',
            'jumlah'     => 'required|integer',
            'harga'     => 'required|integer',
            'image'     => 'required|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
        ]);

        //check if validation fails
        if ($validator->fails()) {
            return new SendError(422, "error", $validator->errors());
        }

        //upload image
        $image = $request->file('image');
        $image->storeAs('public/posts', $image->hashName());

        //create drug
        $drugs = drug::create([
            'image'     => $image->hashName(),
            'kode_obat'     => $request->kode,
            'nama_obat'     => $request->nama,
            'expired_date'     => $request->expired_date,
            'jumlah'     => $request->jumlah,
            'harga'     => $request->harga,
            'flag' => 1,
        ]);

        $drugs['updated_at'] = null; // Atur updated_at ke null saat insert

        //return response
        return new SendResponse('Data Added Successfully!', $drugs);
    }

    public function show($id)
    {
        //find drug by ID
        $drug = drug::find($id);

        //check if drug exists
        if (!$drug) {
            // return response if drug is not found
            return new SendError(404, 'Drug not found', null); // You can customize this based on your SendError class
        }

        //return single drug as a resource
        return new SendResponse('Detail data drug', $drug);
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
            'nama'   => 'required',
            'kode'     => 'required|unique:drugs,kode_obat,{$id}',
            'expired_date'     => 'required|date',
            'jumlah'     => 'required|integer',
            'harga'     => 'required|integer',
            'image'     => 'image|mimes:jpeg,png,jpg,gif,svg|max:2048',
        ]);

        //check if validation fails
        if ($validator->fails()) {
            // return new SendError(422, "error", $validator->errors());
            return new SendError(422, "error", $validator->errors());
        }

        //find drug by ID
        $drug = drug::find($id);
        if (!$drug) {
            // return response if drug is not found
            return new SendError(404, 'Drug not found', null); // You can customize this based on your SendError class
        }

        //check if image is not empty
        if ($request->hasFile('image')) {

            //upload image
            $image = $request->file('image');
            $image->storeAs('public/posts', $image->hashName());

            //delete old image
            Storage::delete('public/posts/' . basename($drug->image));

            //update drug with new image
            $drug->update([
                'image'     => $image->hashName(),
                'kode_obat'     => $request->kode,
                'nama_obat'     => $request->nama,
                'expired_date'     => $request->expired_date,
                'jumlah'     => $request->jumlah,
                'harga'     => $request->harga,
            ]);
        } else {

            //update drug without image
            $drug->update([
                'kode_obat'     => $request->kode,
                'nama_obat'     => $request->nama,
                'expired_date'     => $request->expired_date,
                'jumlah'     => $request->jumlah,
                'harga'     => $request->harga,
            ]);
        }

        //return response
        return new SendResponse('success', $drug);
    }

    public function destroy($id)
    {
        // Find drug by ID or fail
        $drug = drug::find($id);
        if (!$drug) {
            return new SendError(404, 'recipe not found', null);
        }
        // Update the 'flag' field to 0
        $drug->update(['flag' => 0]);

        // Return response
        return new SendResponse('success', $drug);
    }
}