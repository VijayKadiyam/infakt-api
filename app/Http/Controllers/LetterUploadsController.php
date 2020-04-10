<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use App\UserAppointmentLetter;

class LetterUploadsController extends Controller
{
  public function appointmentLetterFile(Request $request)
  {
    $request->validate([
      'letter_id'  =>  'required'
    ]);
    
    $imagePath = '';
    if ($request->hasFile('attachment')) {
      $file = $request->file('attachment');
      $name = 'al.';
      $name = $name . $file->getClientOriginalExtension();;
      $imagePath = 'appointment_letters/' . $request->letter_id . '/' . $name;
      Storage::disk('local')->put('documentation/' . $imagePath, file_get_contents($file), 'public');

      if($imagePath) {
        $appointmentLetter = UserAppointmentLetter::find($request->letter_id);
        $appointmentLetter->letter_path = $imagePath;
        $appointmentLetter->update();
      }
    }

    return response()->json([
      'data'  => [
        'image_path'  =>  $imagePath
      ],
      'success' =>  true
    ]);
  }
}
