<?php

namespace App\Http\Controllers;

use App\Models\Pasien;
use Carbon\Carbon;
use Illuminate\Http\Request;
use DataTables;
// use Image;
use App\Models\Rekam;
use App\Models\RekamGigi;
use App\Models\PengeluaranObat;
use App\Models\PasienFile; // Make sure this is at the top
use App\User;


use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;



class PasienController extends Controller
{
    public function json(Request $request)
    {
        $pasiens = Pasien::all()->map(function($pasien) {
            return [
                'id' => $pasien->id,
                'nama' => $pasien->nama, // decrypted via __get
                'no_rm' => $pasien->no_rm,
                'cara_bayar' => $pasien->cara_bayar, // decrypted via __get
                'tgl_lahir' => $pasien->tgl_lahir,
                'no_hp' => $pasien->no_hp, // decrypted via __get
                'no_bpjs' => $pasien->no_bpjs
            ];
        });

        return DataTables::of($pasiens)
            ->addColumn('action', function($data){
                $button = '<a href="javascript:void(0)" 
                    data-id="'.$data['id'].'"
                    data-nama="'.$data['nama'].'"
                    data-no="'.$data['no_rm'].'"
                    data-metode="'.$data['cara_bayar'].'"
                    class="btn btn-primary shadow btn-xs pilihPasien">
                    Pilih</a>';
                return $button;
            })
            ->rawColumns(['action'])
            ->toJson();
    }

    public function index(Request $request)
    {
        $rawData = Pasien::all();
        $searchedDatas = $rawData->map(function($pasien) {
            return [
                'id' => $pasien->id,
                'nama' => $pasien->nama,
                'no_rm' => $pasien->no_rm,
                'cara_bayar' => $pasien->cara_bayar,
                'tgl_lahir' => $pasien->tgl_lahir,
                'jk' => $pasien->jk,
                'tmp_lahir' => $pasien->tmp_lahir,
                'no_hp' => $pasien->no_hp,
                'no_bpjs' => $pasien->no_bpjs,
                'alamat_lengkap' => $pasien->alamat_lengkap,
                'status_pasien' => $pasien->statusPasien()
            ];
        });
        // dd($searchedDatas);

        if($request->keyword) {
            $keyword = strtolower($request->keyword);
            $searchedDatas = $searchedDatas->filter(function($item) use ($keyword) {
                return str_contains(strtolower($item['nama']), $keyword) ||
                       str_contains(strtolower($item['no_rm']), $keyword) ||
                       str_contains(strtolower($item['no_bpjs'] ?? ''), $keyword) ||
                       str_contains(strtolower($item['no_hp']), $keyword) ||
                       str_contains(strtolower($item['alamat_lengkap'] ?? ''), $keyword) ||
                       str_contains(strtolower($item['tgl_lahir']?? ''), $keyword) ||
                       str_contains(strtolower($item['jk']?? ''), $keyword);

            });
        }

        $datas = new \Illuminate\Pagination\LengthAwarePaginator(
            $searchedDatas->forPage(\Illuminate\Pagination\Paginator::resolveCurrentPage(), 10),
            $searchedDatas->count(),
            10,
            null,
            ['path' => \Illuminate\Pagination\Paginator::resolveCurrentPath()]
        );

        return view('pasien.index', compact('datas'));
    }

    function add(Request $request){
        return view('pasien.add');
    }

    function edit(Request $request,$id){
        $data = Pasien::find($id);
        return view('pasien.edit',compact('data'));
    }

    function file(Request $request,$id){
        $data = Pasien::find($id);
        return view('pasien.file',compact('data'));
    }

    function store(Request $request){
        // make email by name
        $email = strtolower(str_replace(' ', '.', $request->nama));
        $email = preg_replace('/[^a-zA-Z0-9.]/', '', $email);
        $email .= '@gmail.com';

        // assign email to request
        $request->merge(['email' => $email]);
        $this->validate($request,[
            'nama' => 'required',
            'no_hp' => 'required|unique:users,phone',
            'cara_bayar' => 'required',
            'jk' => 'required',
            'no_rm' => 'required|unique:pasien',
            // 'no_bpjs' => 'unique:pasien', // Assuming no_bpjs might not always be unique or present for all users initially
            'no_bpjs' => 'nullable|unique:pasien,no_bpjs', // Allow null and ensure unique if provided
            'email' => 'required|email|unique:users,email', // Add email validation for user creation
            'files.*' => 'nullable|mimes:jpg,png,jpeg,pdf,xls,xlsx,csv|max:2048'
        ]);

        // Start a database transaction
        DB::beginTransaction();

        try {
            $createdUser = null;
            // Create User first only if the authenticated user has role = 2 (Pendaftaran)
            if (Auth::check() && Auth::user()->role == 2) {
                $user = new User();
                $user->name = $request->nama;
                $user->email = $request->email; // Use validated email from request
                $user->phone = $request->no_hp;
                $user->password = Hash::make('qwe'); // Default password
                $user->role = 5; // Pasien role
                $user->status = 1; // Active status
                $user->email_verified_at = Carbon::now();
                $user->save();
                $createdUser = $user;
            }

            $pasien = new Pasien();

            // Fill no_rm and no_bpjs directly
            $pasien->fill([
                'no_rm' => $request->no_rm,
                'no_bpjs' => $request->no_bpjs
            ]);

            // Assign user_id if user was created
            if ($createdUser) {
                $pasien->user_id = $createdUser->id; // Assign the new user's ID
            }

            // Handle encrypted attributes
            $encryptedAttributes = $pasien->getEncryptedAttributes();
            foreach ($encryptedAttributes as $attribute) {
                // Skip user_id if it's in encryptedAttributes, as it's already set
                if ($attribute === 'user_id') continue;
                // Ensure other attributes like 'nama', 'no_hp' are set on Pasien model if they are part of its encrypted fields
                // and not just User model.
                if ($request->has($attribute)) {
                     $pasien->{$attribute} = $request->input($attribute);
                }
            }
            // If 'nama' and 'no_hp' are also fields in Pasien model and need to be encrypted for Pasien
            // (and not just for User), ensure they are set here. Assuming they are from request.
            // This depends on your Pasien model's $encryptableFields definition.
            // For example, if 'nama' is an encrypted field in Pasien:
            // $pasien->nama = $request->nama; 

            $pasien->save();

            // Handle file uploads using PasienFile model
            if ($request->hasFile('files')) {
                foreach ($request->file('files') as $file) {
                    $originalName = $file->getClientOriginalName();
                    $fileName = time() . '_' . $originalName;
                    $filePath = 'pasien_files/' . $pasien->id . '/' . $fileName;
                    $file->move(public_path('pasien_files/' . $pasien->id), $fileName);
                    PasienFile::create([
                        'pasien_id' => $pasien->id,
                        'file_path' => $filePath,
                        'original_name' => $originalName,
                    ]);
                }
            }

            DB::commit();
            return redirect()->route('pasien')->with('sukses','Data berhasil ditambahkan');

        } catch (\Illuminate\Validation\ValidationException $e) {
            dd($e);
            DB::rollBack();
            return redirect()->back()->withInput()->withErrors($e->errors());
        } catch (\Exception $e) {
            dd($e);
            DB::rollBack();
            
            // Log the error for debugging
            \Log::error("Error creating Pasien and/or User: " . $e->getMessage() . "\nStack Trace:\n" . $e->getTraceAsString());

            return redirect()->back()->withInput()->withErrors('Gagal menambahkan data. Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    function update(Request $request,$id){
        $this->validate($request,[
            'nama' => 'required',
            'no_hp' => 'required',
            'jk' => 'required',
            'cara_bayar' => 'required',
            'files.*' => 'nullable|mimes:jpg,png,jpeg,pdf,xls,xlsx,csv|max:2048' // Added Excel file types
        ]);
        // dd($request->all());

        // Start a database transaction for update
        DB::beginTransaction();

        try {
            $data = Pasien::find($id);

            if (!$data) {
                 DB::rollBack();
                 return redirect()->back()->withErrors('Pasien not found.');
            }

            $encryptedAttributes = $data->getEncryptedAttributes();
            // dd($encryptedAttributes,$request->all());
            foreach ($encryptedAttributes as $attribute) {
                $data->{$attribute} = $request->input($attribute);
            }

             // Fill other non-encrypted attributes
            // $data->fill($request->only([
            //     'tmp_lahir', 'tgl_lahir', 'jk', 'status_menikah', 'agama',
            //     'pendidikan', 'pekerjaan', 'alamat_lengkap', 'kelurahan',
            //     'kecamatan', 'kabupaten', 'kodepos', 'kewarganegaraan',
            //     'no_bpjs', 'no_rm'
            // ]));

            $data->save();
            
            // Handle new file uploads using PasienFile model
            if ($request->hasFile('files')) {
                foreach ($request->file('files') as $file) {
                    $originalName = $file->getClientOriginalName();
                    // Generate a unique filename
                    $fileName = time() . '_' . $originalName;
                    // Define the storage path
                    $filePath = 'pasien_files/' . $data->id . '/' . $fileName;

                    // Create directory if it doesn't exist
                    if (!file_exists(public_path('pasien_files/' . $data->id))) {
                        mkdir(public_path('pasien_files/' . $data->id), 0777, true);
                    }

                    // Store the file
                    $file->move(public_path('pasien_files/' . $data->id), $fileName);

                    // Create a record in the pasien_files table
                    PasienFile::create([
                        'pasien_id' => $data->id,
                        'file_path' => $filePath,
                        'original_name' => $originalName,
                    ]);
                }
            }

            // Commit the transaction
            DB::commit();

            return redirect()->route('pasien')->with('sukses','Data berhasil diperbaharui');

        } catch (\Exception $e) {
            dd($e);
            // If any error occurs, rollback the transaction
            DB::rollBack();

            // Log the error
            \Log::error("Error updating Pasien: " . $e->getMessage());

            // Redirect back with an error message
            return redirect()->back()->withInput()->withErrors('Failed to update Pasien: ' . $e->getMessage());
        }
    }
    

    function delete(Request $request,$id)
    {
        // Pasien::find($id)->update(['deleted_at'=>Carbon::now()]);
       $suk = Pasien::find($id)->delete();
       if($suk){
            Rekam::where('pasien_id',$id)->delete();
            RekamGigi::where('pasien_id',$id)->delete();
            PengeluaranObat::where('pasien_id',$id)->delete();
       }
        return redirect()->route('pasien')->with('sukses','Data berhasil dihapus');
    } 

    function getLastRM(Request $request)
    {
        if ($code = $request->get('code')){
            $data = Pasien::orderBy('no_rm','desc')
                        ->where('no_rm','LIKE',"%{$code}%")
                        ->first();
            if ($data) {
                $last_no = substr($data->no_rm,2,3);
                $noLast = (int)$last_no;
                $newNo = $noLast+1;
                $nomorBaru = $newNo;
                if($newNo < 10){
                    $nomorBaru = "00".$newNo;
                }else if($newNo < 100){
                    $nomorBaru = "0".$newNo;
                }
                $no_rm_baru = $code.$nomorBaru;
                return response()->json([
                    'success' => true,
                    'data' => $no_rm_baru
                ],200);
            }else{
                return response()->json([
                    'success' => false,
                ],400);
            }
        }
            
        return response()->json([ 'success' => false],400);
    }

    public function deleteFile($pasienId, $fileId)
    {
        $file = PasienFile::where('pasien_id', $pasienId)->where('id', $fileId)->firstOrFail();

        // Delete the file from storage
        if (file_exists(public_path($file->file_path))) {
            unlink(public_path($file->file_path));
        }

        // Delete the database record
        $file->delete();

        return back()->with('success', 'File deleted successfully.');
    }
}
