<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use Auth;
use Carbon\Carbon;
use Illuminate\Support\Facades\Hash;

class ProfileController extends Controller
{
    public function __construct() {
        $this->middleware('auth');
        $this->user = Auth::user();
    }

    public function index()
    {
        $data['title'] = 'Hallo ' . Auth::user()->name . ', Selamat Datang';
        $data['user'] = $this->user;

        // dd($this->user);
        return view('user.profile', $data);
    }

    public function update(Request $request)
    {
        // var_dump(($request->has('file') ? true : false));
        // die();
        $user = User::find(Auth::user()->id);
        try {
            if ($request->has('file')) {
                $file = $request->file('file');
                $extension = $file->getClientOriginalExtension();
                $fileName = 'profile-'. Auth::user()->id .'-'. Carbon::now()->format('YmdHis') . '.' . $extension;
                $file->storeAs('profil', $fileName, 'public'); 
                $user->update([
                    'profile' => $fileName,
                ]);
            }
            $user->update([
                'name' => $request->name,
                'email' => $request->email,
            ]);
            if ($request->password != null) {
                $user->update([
                    'password' => Hash::make($request->password),
                ]);
            }

            return response()->json([
                'success' => true,
                'message' => 'Data dii berhasil diperbarui',
            ]);
        } catch (\Throwable $th) {
            return response()->json([
                'success' => false,
                'message' => $th->getMessage(),
            ]);
            //throw $th;
        }
        // var_dump(Auth::user()->id, $user);
        // die();
    }

}
