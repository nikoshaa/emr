<?php

namespace App\Http\Controllers;

use App\Events\StatusRekamUpdate;
use App\Models\Obat;
use App\Models\Pasien;
use App\Models\PengeluaranObat;
use App\Models\Rekam;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Notifications\RekamUpdateNotification;
use Carbon\Carbon;
use Illuminate\Support\Facades\Notification as Notification;
use Mpdf\Mpdf; // Add this use statement for mPDF
use Mpdf\Config\ConfigVariables;
use Mpdf\Config\FontVariables;
use Mpdf\Output\Destination; // <-- Add for direct output handling if needed
use Illuminate\Support\Facades\Auth as Auth;

use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class PengeluaranObatController extends Controller
{
    public function resep(Request $request)
    {
        $datas = Rekam::latest()
                    ->where('status',3)
                    ->get();

        return view('obat.resep',compact('datas'));
    }

    public function pengeluaran(Request $request,$rekam_id)
    {
        $rekam = Rekam::find($rekam_id);
        $pasien = Pasien::find($rekam->pasien_id);
        $pengeluaran = PengeluaranObat::where('rekam_id',$rekam_id)->whereNull('deleted_at')->get();
        if($rekam){
            auth()->user()->notifications->where('data.no_rekam',$rekam->no_rekam)->markAsRead();
        }
        return view('obat.pengeluaran',compact('rekam','pasien','pengeluaran'));
    }

    public function riwayat(Request $request)
    {
        $datas = PengeluaranObat::latest()
                            ->when($request->keyword, function ($query) use ($request) {
                                $query->where('created_at', 'LIKE', "%{$request->keyword}%")
                                    ->orWhere('kd_obat', 'LIKE', "%{$request->keyword}%");
                            })
                            ->whereNull('deleted_at')
                            ->paginate(10);
        return view('obat.riwayat',compact('datas'));
    }

    public function store(Request $request)
    {
        // dd($request->all());
        try {
            DB::beginTransaction();
            if ($request->obat_id) {
                foreach ($request->obat_id as $i => $obatId) {
                    PengeluaranObat::create([
                        'rekam_id' => $request->rekam_id,
                        'pasien_id' => $request->pasien_id,
                        'obat_id'  => $obatId,
                        'jumlah' => $request->jumlah[$i],
                        'harga' => $request->harga[$i],
                        'subtotal' => $request->subtotal[$i],
                        'keterangan' =>  $request->keterangan[$i] != "" ? $request->keterangan[$i] : ""
                    ]);

                    $obat = Obat::find($obatId);
                    $obat->update(
                        [
                        'stok' => $obat->stok - $request->jumlah[$i]
                    ]);
                }
            }
            DB::commit();
            $rekam = Rekam::find($request->rekam_id);
            $status = 5;
            $users = User::where('role',2)->get();
            $message = "Pasien ".$rekam->pasien->nama.", sudah selesai berobat";
            $pasien = Pasien::find($rekam->pasien_id);
            // dd($pasien);
            $user = User::find($pasien->user_id);
            // dd($user);
            Notification::send($user, new RekamUpdateNotification($rekam,$message));
            foreach ($users as $key => $item) {
                $link = Route('rekam.detail',$rekam->pasien_id);
                $waktu = Carbon::parse($rekam->created_at)->format('d/m/Y H:i:s');
                event(new StatusRekamUpdate($item->id,$rekam->no_rekam,$message,$link,$waktu));
            }

            $apoteker = auth()->user();
            // }
            $rekam->update([
                'status' => $status,
                'apoteker_id' => $apoteker->id,
                'is_decrypted' => 0,
            ]);
            
            return redirect()->route('obat.pengeluaran',$request->rekam_id)->with('sukses','Obat Berhasil diberikan');

        } catch (\PDOException $e) {
            DB::rollback();
            return redirect()->route('obat.pengeluaran',$request->rekam_id)->with('gagal','Data Gagal ditambahkan '.$e);

        }   
    }
    /**
     * Verify user password and trigger protected PDF export.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response|\Illuminate\Http\JsonResponse
     */
    public function verifyPasswordAndExportPdf(Request $request)
    {
        $request->validate([
            'password' => 'required|string',
            'rekam_id' => 'required|integer',
        ]);

        // is_decrypt_pasien = 0
        $isDecryptedPasien = $request->input('is_decrypted_pasien', 0) == 1;


        $user = Auth::user();
        $rekam_id = $request->input('rekam_id');
        $enteredPassword = $request->input('password');

        // hash password
        $hashedPassword = Hash::make($enteredPassword);
        // dd($hashedPassword,$user->password);

        // Verify the entered password against the user's stored hash
        if (Hash::check($enteredPassword, $user->password)) {
            // Password is correct, generate and return the protected PDF
            try {
                if ($isDecryptedPasien) {
                    return response()->json(['success' => true,'message' => 'PDF berhasil dibuat.']); // Return success response without password protectio
                }
                // Pass the plain text password to the generator
                return $this->generatePengeluaranPdf($rekam_id, $enteredPassword);
            } catch (\Exception $e) {
                 // Log the error details for debugging
                 \Log::error("PDF Generation Failed after Password Check: " . $e->getMessage(), ['rekam_id' => $rekam_id]);
                 // Return a JSON error response that the AJAX can understand
                 return response()->json(['success' => false, 'message' => 'Gagal membuat PDF: ' . $e->getMessage()], 500);
            }
        } else {
            // Password incorrect
            return response()->json(['success' => false, 'message' => 'Password yang Anda masukkan salah.'], 401); // 401 Unauthorized
        }
    }

    /**
     * Public method for potentially exporting unprotected PDF (if needed).
     * Consider removing if only protected export is desired.
     *
     * @param  int  $rekam_id
     * @return \Illuminate\Http\Response
     */
    public function exportPdf($rekam_id)
    {
         try {
            // Generate PDF without password protection
            return $this->generatePengeluaranPdf($rekam_id, null);
        } catch (\Exception $e) {
             \Log::error("Unprotected PDF Generation Failed: " . $e->getMessage(), ['rekam_id' => $rekam_id]);
             return redirect()->route('obat.pengeluaran', $rekam_id)->with('gagal', 'Terjadi kesalahan saat membuat PDF: ' . $e->getMessage());
        }
    }

    /**
     * Private helper method to generate the PDF content.
     *
     * @param int $rekam_id
     * @param string|null $password Password for protection, null for none.
     * @return \Illuminate\Http\Response
     * @throws \Mpdf\MpdfException|\Exception
     */
    private function generatePengeluaranPdf($rekam_id, $password = null)
    {
        // Fetch data (same as before)
        $rekam = Rekam::with(['pasien', 'dokter'])->find($rekam_id); // Eager load patient & doctor
        if (!$rekam) {
            abort(404, 'Rekam Medis tidak ditemukan.');
        }
        $apoteker = User::find($rekam->apoteker_id);
        $pasien = $rekam->pasien;
        $pengeluaranRaw = PengeluaranObat::with(['obat','rekam.dokter'])
                                      ->where('rekam_id', $rekam_id)
                                      ->whereNull('deleted_at')
                                      ->get();
        // make the $obat['nama'] => $obat->nama
        
        $pengeluaran = $pengeluaranRaw->map(function ($item) {
            $item->obat['nama'] = $item->obat->nama;
            $item->obat['kd_obat'] = $item->obat->kd_obat;
            $item->rekam['keluhan'] = $item->rekam->keluhan;
            $item->rekam['pemeriksaan'] = $item->rekam->pemeriksaan;
            $item->rekam['diagnosa'] = $item->rekam->diagnosa;
            $item->rekam->dokter['nama'] = $item->rekam->dokter->nama;

            // dd($item);

            return $item;
        });
        // dd($pengeluaran);

        $data = compact('rekam', 'pasien', 'pengeluaran','apoteker');
        $html = view('obat.pengeluaran_pdf', $data)->render();

        // Setup mPDF (same as before)
        $defaultConfig = (new ConfigVariables())->getDefaults();
        $fontDirs = $defaultConfig['fontDir'];
        $defaultFontConfig = (new FontVariables())->getDefaults();
        $fontData = $defaultFontConfig['fontdata'];

        // Ensure temp directory exists and is writable
        $tempDir = storage_path('app/pdf_temp');
        if (!is_dir($tempDir)) {
            mkdir($tempDir, 0775, true); // Create if not exists
        }

        $mpdf = new Mpdf([
            'mode' => 'utf-8',
            'format' => 'A4',
            'orientation' => 'P',
            'tempDir' => $tempDir,
            'fontDir' => array_merge($fontDirs, [ /* custom font dirs */ ]),
            'fontdata' => $fontData + [ /* custom fonts */ ],
        ]);

        $mpdf->SetTitle('Detail Pengeluaran Obat - ' . $rekam->no_rekam);
        $mpdf->SetAuthor(config('app.name'));
        $mpdf->SetCreator(config('app.name'));

        // *** Apply Password Protection if password is provided ***
        if ($password !== null && $password !== '') {
            // Set user password (restricts opening) and owner password (restricts permissions)
            // Using same password for both for simplicity here.
            // Empty array for permissions means default (usually print allowed, modify restricted)
             $mpdf->SetProtection([], $password, $password);
        }

        $mpdf->WriteHTML($html);

        $filename = 'PengeluaranObat-'.$rekam->no_rekam.'.pdf';

        // Return the PDF content directly as a response for AJAX/Download
        // Use 'I' for inline display attempt, 'D' for forced download
        // When called via AJAX, the browser handles it based on response headers + JS blob handling
        return response($mpdf->Output($filename, Destination::STRING_RETURN), 200, [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => 'inline; filename="'.$filename.'"', // Suggest inline, JS handles download
        ]);
    }
}
