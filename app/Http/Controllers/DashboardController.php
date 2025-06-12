<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\RekamMedis; // Add this import
use App\Models\Pasien; // Add this import
use App\Models\Obat; // Add this import
use App\Models\Rekam; 

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        if(auth()->user()->role_display()=="Admin"){
            return view('dashboard.admin');
        }else if(auth()->user()->role_display()=="Pendaftaran"){
            return view('dashboard.registrasi');
        }else if(auth()->user()->role_display()=="Dokter"){
            return view('dashboard.dokter');
        }else if(auth()->user()->role_display()=="Apotek"){
            return view('dashboard.obat');
        }else if(auth()->user()->role_display()=="Pasien"){
            // Get the patient's medical records
            // find pasien with user id = user()->id
            $pasien = Pasien::where('user_id', auth()->user()->id)->first();
            $rekamMedis = [];

            if ($pasien) {
                // Get all medical records for this patient
                // Eager load the 'dokter' relationship
                $rekamMedis = Rekam::where('pasien_id', $pasien->id)
                    ->with('dokter') // {{-- Add this line to eager load the dokter relationship --}}
                    ->orderBy('created_at', 'desc')
                    ->get();
            }

            // dd($rekamMedis);

            // Return the patient dashboard view with medical records
            return view('dashboard.pasien', compact('rekamMedis', 'pasien'));
        }else{
            // Return the new pending view
            return view('dashboard.pending');
        }
    }

    public function chat()
    {
        // Only staff can access this page
        if (auth()->user()->role < 1 || auth()->user()->role > 4) {
            return redirect()->route('dashboard');
        }
        
        return view('chat.staff');
    }
}
