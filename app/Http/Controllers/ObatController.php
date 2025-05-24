<?php

namespace App\Http\Controllers;

use App\Models\Obat;
use App\Models\PengeluaranObat;
use Carbon\Carbon;
use Illuminate\Http\Request;
use DataTables;


class ObatController extends Controller
{
    public function data(Request $request)
    {
        // Get all obat records first
        $rawObats = Obat::whereNull('deleted_at')->get();
        
        // Create a new collection with plain objects containing decrypted values
        $obats = $rawObats->map(function ($obat) {
            return [
                'id' => $obat->id,
                'kd_obat' => $obat->kd_obat, // This triggers the __get magic method for decryption
                'nama' => $obat->nama,       // This triggers the __get magic method for decryption
                'satuan' => $obat->satuan,
                'stok' => $obat->stok,
                'harga' => $obat->harga,
                'created_at' => $obat->created_at,
                'updated_at' => $obat->updated_at
            ];
        });

        
        if ($request->ajax()) {
            return DataTables::of($obats)
                    ->addColumn('action',function($data){
                        $button = '<a href="javascript:void(0)" 
                            data-id="'.$data['id'].'"
                            data-code="'.$data['kd_obat'].'"
                            data-nama="'.$data['nama'].'"
                            data-stok="'.$data['stok'].'"
                            data-harga="'.$data['harga'].'"
                            data-satuan="'.$data['satuan'].'"
                            class="btn btn-primary shadow btn-xs pilihObat">
                            Pilih</a>';
                        return $button;
                    })->rawColumns(['action'])
                    ->toJson();
        }
        
        // For non-ajax requests, also use the collection
        return DataTables::of($obats)
            ->addColumn('action',function($data){
                $button = '<a href="javascript:void(0)" 
                    data-id="'.$data['id'].'"
                    data-code="'.$data['kd_obat'].'"
                    data-nama="'.$data['nama'].'"
                    data-stok="'.$data['stok'].'"
                    data-harga="'.$data['harga'].'"
                    data-satuan="'.$data['satuan'].'"
                    class="btn btn-primary shadow btn-xs pilihObat">
                    Pilih</a>';
                return $button;
            })->rawColumns(['action'])->toJson();
    }

    public function index(Request $request)
    {
        $datas = Obat::whereNull('deleted_at')->paginate(10);
        return view('obat.index',compact('datas'));
    }

    public function store(Request $request)
    {
        // set the request kd_obat with hash_hmac
        $request->request->add(['kd_obat_hash' => hash_hmac('sha256', $request->kd_obat, env('APP_KEY'))]);
        $request->request->add(['nama_hash' => hash_hmac('sha256', $request->nama, env('APP_KEY'))]);

        $this->validate($request,[
            'kd_obat_hash' => 'required|unique:obat',
            'nama_hash' => 'required|unique:obat',
            'satuan' => 'required',
            'harga' => 'required|numeric',
            'stok' => 'required|numeric',
        ]);
        // dd($request->all());
        $obat = new Obat();
        $obat->kd_obat = $request->kd_obat;
        $obat->nama = $request->nama;
        $obat->satuan = $request->satuan;
        $obat->harga = $request->harga;
        $obat->stok = $request->stok;
        $obat->save();


        return redirect()->route('obat')->with('sukses','Data berhasil ditambahkan');
    }

    public function update(Request $request,$id)
    {
        // set the request kd_obat with hash_hmac
        $request->request->add(['nama_hash' => hash_hmac('sha256', $request->nama, env('APP_KEY'))]);
        $this->validate($request,[
            'nama_hash' =>'required'
        ]);
        $data = Obat::find($id);
        $data->nama_hash = $request->nama_hash;
        $data->nama = $request->nama;
        $data->satuan = $request->satuan;
        $data->harga = $request->harga;
        $data->stok = $request->stok;
        $data->save();

        return redirect()->route('obat')->with('sukses','Data berhasil diperbaharui');
    }

    public function delete(Request $request,$id)
    {
        $dike = PengeluaranObat::where('obat_id',$id)->count();
        if ($dike > 0) {
            Obat::find($id)->update([
                'deleted_at' => Carbon::now()
            ]);
        }else{
            Obat::find($id)->delete();
        }
        return redirect()->route('obat')->with('sukses','Data berhasil dihapus');
    } 
}
