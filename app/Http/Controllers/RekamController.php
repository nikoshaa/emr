<?php

namespace App\Http\Controllers;

use App\Events\StatusRekamUpdate;
use App\Models\Dokter;
use App\Models\KondisiGigi;
use App\Models\Pasien;
use App\Models\PengeluaranObat;
use App\Models\Poli;
use App\Models\Rekam;
use App\Models\RekamGigi;
use App\Models\Tindakan;
use App\Notifications\RekamUpdateNotification;
use App\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
// use Illuminate\Notifications\Notification;
use Illuminate\Support\Facades\Notification as Notification;
use Illuminate\Support\Facades\Auth; // Import Auth
use Illuminate\Support\Facades\Hash; // Import Hash


class RekamController extends Controller
{
    public function index(Request $request)
    {
        $user = auth()->user();
        $role = $user->role_display();
        
        $rawData = Rekam::with(['pasien', 'dokter'])->latest()->get();
        
        $searchedDatas = $rawData->map(function($rekam) {
            // dd($rekam);
            return [
                'id' => $rekam->id,
                'no_rekam' => $rekam->no_rekam,
                'tgl_rekam' => $rekam->tgl_rekam,
                'pasien_id' => $rekam->pasien_id,
                'pasien_nama' => $rekam->pasien->nama,
                'poli' => $rekam->poli,
                'dokter_nama' => $rekam->dokter->nama,
                'keluhan' => $rekam->keluhan,
                'cara_bayar' => $rekam->cara_bayar,
                'status' => $rekam->status,
                'status_display' => $rekam->status_display(),
                'pasien' => [
                    'nama' => $rekam->pasien->nama,
                    'id' => $rekam->pasien->id,
                ],
                'dokter' => [
                    'nama' => $rekam->dokter->nama,
                    'id' => $rekam->dokter->id,
                ]
            ];
        });

        // Filter by role
        if ($role == "Dokter") {
            $dokterId = Dokter::where('user_id', $user->id)->where('status', 1)->first()->id;
            $searchedDatas = $searchedDatas->filter(function($item) use ($dokterId) {
                
                return $item['dokter']['id'] == $dokterId;
            });
        }

        // Filter by tab
        if ($request->tab) {
            $searchedDatas = $searchedDatas->filter(function($item) use ($request, $role) {
                if ($role == "Dokter" && $request->tab == 5) {
                    return in_array($item['status'], [3, 4, 5]);
                } else {
                    if ($request->tab == 5) {
                        return in_array($item['status'], [4, 5]);
                    } else {
                        return $item['status'] == $request->tab;
                    }
                }
            });
        }

        // Search functionality
        if ($request->keyword) {
            $keyword = strtolower($request->keyword);
            $searchedDatas = $searchedDatas->filter(function($item) use ($keyword) {
                return str_contains(strtolower($item['tgl_rekam']), $keyword) ||
                       str_contains(strtolower($item['cara_bayar']), $keyword) ||
                       str_contains(strtolower($item['pasien_nama']), $keyword) ||
                       str_contains(strtolower($item['no_rekam']), $keyword);
            });
        }

        // Create paginator
        $rekams = new \Illuminate\Pagination\LengthAwarePaginator(
            $searchedDatas->forPage(\Illuminate\Pagination\Paginator::resolveCurrentPage(), 10),
            $searchedDatas->count(),
            10,
            null,
            ['path' => \Illuminate\Pagination\Paginator::resolveCurrentPath()]
        );

        return view('rekam.index', compact('rekams'));
    }

    public function add(Request $request)
    {
        $poli = Poli::all();
        return view('rekam.add',compact('poli'));
    }

    public function edit(Request $request,$id)
    {
        $poli = Poli::all();
        $data = Rekam::find($id);
        return view('rekam.edit',compact('data','poli'));
    }

   
    public function detail(Request $request,$pasien_id)
    {
        $pasien = Pasien::with('files')->find($pasien_id);
        // dd($pasien);
        $rekamLatest = Rekam::latest()
                                ->where('status','!=',5)
                                ->where('pasien_id',$pasien_id)
                                ->first();

        $rekams = Rekam::latest()
                    ->where('pasien_id',$pasien_id)
                    ->when($request->keyword, function ($query) use ($request) {
                        $query->where('tgl_rekam', 'LIKE', "%{$request->keyword}%");
                    })
                    ->when($request->poli, function ($query) use ($request) {
                        $query->where('poli', 'LIKE', "%{$request->poli}%");
                    })
                    ->paginate(5);
                    
        if($rekamLatest){
           auth()->user()->notifications->where('data.no_rekam',$rekamLatest->no_rekam)->markAsRead();
        //   dd($data);
        }
        $poli = Poli::where('status',1)->get();

        return view('rekam.detail-rekam',compact('pasien','rekams','rekamLatest','poli'));
    }

    function store(Request $request){
        $this->validate($request,[
            'tgl_rekam' => 'required',
            'pasien_id' => 'required',
            'pasien_nama' => 'required',
            
            'poli' => 'required',
            'cara_bayar' => 'required',
            'dokter_id' => 'required'
        ]);
        $pasien = Pasien::find($request->pasien_id);
        if(!$pasien){
            return redirect()->back()->withInput($request->input())
                                ->withErrors(['pasien_id' => 'Data Pasien Tidak Ditemukan']);
        }
        $rekam_ada = Rekam::where('pasien_id',$request->pasien_id)
                            ->whereIn('status',[1,2,3,4])
                            ->first();
        // if($rekam_ada){
        //     return redirect()->back()->withInput($request->input())
        //                         ->withErrors(['pasien_id' => 'Pasien ini masih belum selesai periksa,
        //                          harap selesaikan pemeriksaan sebelumnya']);
        // }
        // $dokter = Dokter::where('poli',$request->poli)->first();
        // if($dokter){
        //     $request->merge([
        //         'dokter_id' => $dokter->id
        //     ]);
        // }
        $request->merge([
            'no_rekam' => "REG#".date('Ymd').$request->pasien_id,
            'petugas_id' => auth()->user()->id
        ]);
        // Create Rekam instance WITHOUT sensitive data first
        $rekam = new Rekam();
        $rekam->fill($request->except(['keluhan', 'pemeriksaan', 'diagnosa', 'tindakan'])); // Fill non-sensitive data

        // Assign other required fields explicitly if not fillable
        $rekam->no_rekam = "REG#".date('Ymd').$request->pasien_id;
        $rekam->petugas_id = auth()->user()->id;
        $rekam->pasien_id = $request->pasien_id; // Ensure pasien_id is set for mutators
        $rekam->dokter_id = $request->dokter_id;
        $rekam->tgl_rekam = $request->tgl_rekam;
        $rekam->poli = $request->poli;
        $rekam->cara_bayar = $request->cara_bayar;
        // Set default status if needed, e.g., $rekam->status = 1;

        // Now assign sensitive data to trigger mutators
        $rekam->setRelation('pasien', $pasien); // Manually associate the loaded Pasien model

        // Uncomment these lines if these fields are submitted in the 'add' form
        $rekam->keluhan = $request->input('keluhan');
        $rekam->pemeriksaan = $request->input('pemeriksaan');
        $rekam->diagnosa = $request->input('diagnosa');
        $rekam->tindakan = $request->input('tindakan');
        
        // Save the record
        $rekam->save();
        $message = "Pasien ".$rekam->pasien->nama.", silahkan diproses";
        // find user using dokter id
        // $dokter = Dokter::find($request->dokter_id);
        // $user = User::find($dokter->user_id);
        // Notification::send($user, new RekamUpdateNotification($rekam,$message));
        return redirect()->route('rekam.detail',$request->pasien_id)
                        ->with('sukses','Pendaftaran Berhasil,
                         Silakan lakukan pemeriksaan dan teruskan ke dokter terkait');

    }

    function update(Request $request,$id){
        $this->validate($request,[
            'tgl_rekam' => 'required',
            'pasien_id' => 'required',
            'pasien_nama' => 'required',
            'keluhan' => 'required',
            'poli' => 'required',
            'cara_bayar' => 'required',
            'dokter_id' => 'required'
        ]);
        // Find the existing Rekam record and load its patient relationship
        $rekam = Rekam::with('pasien')->find($id);
        if(!$rekam){
             return redirect()->route('rekam')->with('gagal', 'Data rekam medis tidak ditemukan.');
        }

        // Ensure the update is for the correct patient if pasien_id is part of the request
        if ($rekam->pasien_id != $request->pasien_id) {
             // This scenario might indicate an error or attempt to change patient association, handle appropriately
             return redirect()->back()->withInput($request->input())
                                 ->withErrors(['pasien_id' => 'Tidak dapat mengubah pasien untuk rekam medis ini.']);
        }

        // Log the input before assigning it
        \Illuminate\Support\Facades\Log::debug("Rekam Update ID {$id}: Received pemeriksaan input: " . $request->input('pemeriksaan'));

        // Update non-sensitive fields using fill
        $rekam->fill($request->except(['keluhan', 'pemeriksaan', 'diagnosa', 'tindakan']));

        // Update sensitive fields individually to trigger mutators
        $rekam->keluhan = $request->input('keluhan');
        $rekam->pemeriksaan = $request->input('pemeriksaan'); // Assign here
        $rekam->diagnosa = $request->input('diagnosa');
        $rekam->tindakan = $request->input('tindakan');

        // Save the changes
        $rekam->save();
        return redirect()->route('rekam.detail',$request->pasien_id)
                        ->with('sukses','Berhasil diperbaharui,
                         Silakan lakukan pemeriksaan dan teruskan ke dokter terkait');

    }

    public function rekam_status(Request $request, $id, $status)
    {
        $rekam = Rekam::find($id);
        // dd($rekam);
        $dokter = Dokter::find($rekam->dokter_id);
        $user = User::find($dokter->user_id);
        if($status==2 && $rekam->poli != "Poli Gigi"){
            if($rekam->pemeriksaan==null){
                return redirect()->route('rekam.detail',$rekam->pasien_id)
                ->with('gagal','Pemeriksaan Isi lebih dulu');
            }
        }
        if($status==3){
            if($rekam->poli=="Poli Gigi"){
                if(RekamGigi::where('rekam_id',$id)->count() == 0){
                    return redirect()->route('rekam.detail',$rekam->pasien_id)
                    ->with('gagal','Pemeriksaan, Diagnosa, Tindakan Wajib diisi');
                }

            }else if($rekam->tindakan==null ){
                return redirect()->route('rekam.detail',$rekam->pasien_id)
                ->with('gagal','Tindakan dan Diagnosa Belum diisi');
            }
            Notification::send($user, new RekamUpdateNotification($rekam,"Pasien ".$rekam->pasien->nama.", telah diperiksa, menunggu pemberian obat"));
        }

        $pasien_id = $rekam->pasien_id;
        $all_pasien_rekams_decrypted = Rekam::where('pasien_id',$pasien_id)
                                ->where('status','=',$status-1)
                                ->get();

        // dd($all_pasien_rekams_decrypted);

        // find the record that having is_decrypted = 0 from all_pasien_rekams_decrypted
        $rekam_decrypted = $all_pasien_rekams_decrypted->firstWhere('is_decrypted', 0);

        if($rekam_decrypted){
            return redirect()->route('rekam.detail',$rekam_decrypted->pasien_id)
                    ->with('gagal','Pasien '.$rekam_decrypted->pasien->nama.', masih ada rekam medis yang belum selesai diperiksa');
        }

        DB::beginTransaction();

        try {
            // update all record in all_pasien_rekams_decrypted that having is_decrypted = 0
            $all_pasien_rekams_decrypted->each(function ($rekam) use ($status) {
                $rekam->update([
                    'is_decrypted' => 0,
                    'status' => $status
                ]);
            });
            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            // Log the error for debugging
            \Illuminate\Support\Facades\Log::error("Failed to update rekam status for Pasien ID {$pasien_id}: " . $e->getMessage());

            return redirect()->route('rekam.detail',$pasien_id)
                    ->with('gagal','Terjadi kesalahan saat memperbarui status rekam medis.');
        }


        $waktu = Carbon::parse($rekam->created_at)->format('d/m/Y H:i:s');
        if($status==2){
            $message = "Pasien ".$rekam->pasien->nama.", silahkan diproses";
            Notification::send($user, new RekamUpdateNotification($rekam,$message));
            $link = Route('rekam.detail',$rekam->pasien_id);
            event(new StatusRekamUpdate($user->id,$rekam->no_rekam,$message,$link,$waktu));
        }else  if($status==3){
            $user = User::where('role',4)->get();
            $message = "Obat a\n Pasien ".$rekam->pasien->nama.", silahkan diproses";
            Notification::send($user, new RekamUpdateNotification($rekam,$message));
            foreach ($user as $key => $item) {
                $link = Route('rekam.detail',$rekam->pasien_id);
                // StatusRekamUpdate::dispatch($item->id,$rekam->no_rekam,$message,$link,$waktu);
                event(new StatusRekamUpdate($item->id,$rekam->no_rekam,$message,$link,$waktu));
            }
        }else  if($status==4){
            $user = User::where('role',2)->get();
            $message = "Pembayaran a\n Pasien ".$rekam->pasien->nama.", silahkan diproses";
            Notification::send($user, new RekamUpdateNotification($rekam,$message));
            foreach ($user as $key => $item) {
                $link = Route('rekam.detail',$rekam->pasien_id);
                // StatusRekamUpdate::dispatch($item->id,$rekam->no_rekam,$message,$link,$waktu);
                event(new StatusRekamUpdate($item->id,$rekam->no_rekam,$message,$link,$waktu));
            }
        }

        return redirect()->route('rekam.detail',$rekam->pasien_id)
                ->with('sukses','Status Rekam medis selesai diperbaharui ');
    }

    public function delete(Request $request,$id)
    {
        Rekam::find($id)->delete();
        PengeluaranObat::where('rekam_id',$id)->update([
            'deleted_at'=> Carbon::now()
        ]);
        return redirect()->route('rekam')->with('sukses','Data berhasil dihapus');
    } 

    public function decryptRow(Request $request)
    {
        $this->validate($request, [
            'id' => 'required|numeric',
            'password' => 'required|string'
        ]);
        
        $rekam = Rekam::find($request->id);
        
        if (!$rekam) {
            return response()->json([
                'success' => false,
                'message' => 'Record not found'
            ], 404);
        }
        
        $user = Auth::user(); // Get the currently authenticated user
        
        if (!$user) {
             return response()->json([
                'success' => false,
                'message' => 'User not authenticated'
            ], 401);
        }

        // Verify the submitted password against the user's password hash
        if (!Hash::check($request->password, $user->password)) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid password'
            ], 403);
        }
        
        // Password is correct, mark the record as decrypted
        $rekam->is_decrypted = 1;
        $rekam->save();
        
        return response()->json([
            'success' => true,
            'message' => 'Record decrypted successfully'
        ]);
    }


   
}

