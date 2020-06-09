<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use App\Company;

class CompanyUploadsController extends Controller
{
  public function pds(Request $request)
  {
    $request->validate([
      'company_id'        => 'required'
    ]);

    $imagePath = '';
    if ($request->hasFile('word')) {
      $file = $request->file('word');
      $name = $file->getClientOriginalName();
      $name = $name . $file->getClientOriginalExtension();;
      $imagePath = 'companies/' . $request->company_id . '/' . $name;
      Storage::disk('local')->put('documentation/' . $imagePath, file_get_contents($file), 'public');

      $company = Company::where('id', '=', request()->company_id)->first();
      $company->pds_word_path = $imagePath;
      $company->update();
    }
    if ($request->hasFile('pdf')) {
      $file = $request->file('pdf');
      $name = 'pds.';
      $name = $name . $file->getClientOriginalExtension();;
      $imagePath = 'companies/' . $request->company_id . '/' . $name;
      Storage::disk('local')->put('documentation/' . $imagePath, file_get_contents($file), 'public');

      $company = Company::where('id', '=', request()->company_id)->first();
      $company->pds_pdf_path = $imagePath;
      $company->update();
    }

    return response()->json([
      'data'  => [
        'image_path'  =>  $imagePath
      ],
      'success' =>  true
    ]);
  }

  public function form2(Request $request)
  {
    $request->validate([
      'company_id'        => 'required'
    ]);

    $imagePath = '';
    if ($request->hasFile('word')) {
      $file = $request->file('word');
      $name = 'form_2.';
      $name = $name . $file->getClientOriginalExtension();;
      $imagePath = 'companies/' . $request->company_id . '/' . $name;
      Storage::disk('local')->put('documentation/' . $imagePath, file_get_contents($file), 'public');

      $company = Company::where('id', '=', request()->company_id)->first();
      $company->form_2_word_path = $imagePath;
      $company->update();
    }
    if ($request->hasFile('pdf')) {
      $file = $request->file('pdf');
      $name = 'form_2.';
      $name = $name . $file->getClientOriginalExtension();;
      $imagePath = 'companies/' . $request->company_id . '/' . $name;
      Storage::disk('local')->put('documentation/' . $imagePath, file_get_contents($file), 'public');

      $company = Company::where('id', '=', request()->company_id)->first();
      $company->form_2_pdf_path = $imagePath;
      $company->update();
    }

    return response()->json([
      'data'  => [
        'image_path'  =>  $imagePath
      ],
      'success' =>  true
    ]);
  }

  public function form11(Request $request)
  {
    $request->validate([
      'company_id'        => 'required'
    ]);

    $imagePath = '';
    if ($request->hasFile('word')) {
      $file = $request->file('word');
      $name = 'form_11.';
      $name = $name . $file->getClientOriginalExtension();;
      $imagePath = 'companies/' . $request->company_id . '/' . $name;
      Storage::disk('local')->put('documentation/' . $imagePath, file_get_contents($file), 'public');

      $company = Company::where('id', '=', request()->company_id)->first();
      $company->form_11_word_path = $imagePath;
      $company->update();
    }
    if ($request->hasFile('pdf')) {
      $file = $request->file('pdf');
      $name = 'form_11.';
      $name = $name . $file->getClientOriginalExtension();;
      $imagePath = 'companies/' . $request->company_id . '/' . $name;
      Storage::disk('local')->put('documentation/' . $imagePath, file_get_contents($file), 'public');

      $company = Company::where('id', '=', request()->company_id)->first();
      $company->form_11_pdf_path = $imagePath;
      $company->update();
    }

    return response()->json([
      'data'  => [
        'image_path'  =>  $imagePath
      ],
      'success' =>  true
    ]);
  }

  public function pf(Request $request)
  {
    $request->validate([
      'company_id'        => 'required'
    ]);

    $imagePath = '';
    if ($request->hasFile('word')) {
      $file = $request->file('word');
      $name = 'pf.';
      $name = $name . $file->getClientOriginalExtension();;
      $imagePath = 'companies/' . $request->company_id . '/' . $name;
      Storage::disk('local')->put('documentation/' . $imagePath, file_get_contents($file), 'public');

      $company = Company::where('id', '=', request()->company_id)->first();
      $company->pf_word_path = $imagePath;
      $company->update();
    }
    if ($request->hasFile('pdf')) {
      $file = $request->file('pdf');
      $name = 'pf.';
      $name = $name . $file->getClientOriginalExtension();;
      $imagePath = 'companies/' . $request->company_id . '/' . $name;
      Storage::disk('local')->put('documentation/' . $imagePath, file_get_contents($file), 'public');

      $company = Company::where('id', '=', request()->company_id)->first();
      $company->pf_pdf_path = $imagePath;
      $company->update();
    }

    return response()->json([
      'data'  => [
        'image_path'  =>  $imagePath
      ],
      'success' =>  true
    ]);
  }

  public function esic(Request $request)
  {
    $request->validate([
      'company_id'        => 'required'
    ]);

    $imagePath = '';
    if ($request->hasFile('word')) {
      $file = $request->file('word');
      $name = 'esic_benefit.';
      $name = $name . $file->getClientOriginalExtension();;
      $imagePath = 'companies/' . $request->company_id . '/' . $name;
      Storage::disk('local')->put('documentation/' . $imagePath, file_get_contents($file), 'public');

      $company = Company::where('id', '=', request()->company_id)->first();
      $company->esic_benefit_word_path = $imagePath;
      $company->update();
    }
    if ($request->hasFile('pdf')) {
      $file = $request->file('pdf');
      $name = 'esic_benefit.';
      $name = $name . $file->getClientOriginalExtension();;
      $imagePath = 'companies/' . $request->company_id . '/' . $name;
      Storage::disk('local')->put('documentation/' . $imagePath, file_get_contents($file), 'public');

      $company = Company::where('id', '=', request()->company_id)->first();
      $company->esic_benefit_pdf_path = $imagePath;
      $company->update();
    }

    return response()->json([
      'data'  => [
        'image_path'  =>  $imagePath
      ],
      'success' =>  true
    ]);
  }

  public function insurance_claim(Request $request)
  {
    $request->validate([
      'company_id'        => 'required'
    ]);

    $imagePath = '';
    if ($request->hasFile('word')) {
      $file = $request->file('word');
      $name = 'insurance_claim.';
      $name = $name . $file->getClientOriginalExtension();;
      $imagePath = 'companies/' . $request->company_id . '/' . $name;
      Storage::disk('local')->put('documentation/' . $imagePath, file_get_contents($file), 'public');

      $company = Company::where('id', '=', request()->company_id)->first();
      $company->insurance_claim_word_path = $imagePath;
      $company->update();
    }
    if ($request->hasFile('pdf')) {
      $file = $request->file('pdf');
      $name = 'insurance_claim.';
      $name = $name . $file->getClientOriginalExtension();;
      $imagePath = 'companies/' . $request->company_id . '/' . $name;
      Storage::disk('local')->put('documentation/' . $imagePath, file_get_contents($file), 'public');

      $company = Company::where('id', '=', request()->company_id)->first();
      $company->insurance_claim_pdf_path = $imagePath;
      $company->update();
    }

    return response()->json([
      'data'  => [
        'image_path'  =>  $imagePath
      ],
      'success' =>  true
    ]);
  }

  public function salary_slip(Request $request)
  {
    $request->validate([
      'company_id'        => 'required'
    ]);

    $imagePath = '';
    if ($request->hasFile('word')) {
      $file = $request->file('word');
      $name = 'salary_slip.';
      $name = $name . $file->getClientOriginalExtension();;
      $imagePath = 'companies/' . $request->company_id . '/' . $name;
      Storage::disk('local')->put('documentation/' . $imagePath, file_get_contents($file), 'public');

      $company = Company::where('id', '=', request()->company_id)->first();
      $company->salary_slip_word_path = $imagePath;
      $company->update();
    }
    if ($request->hasFile('pdf')) {
      $file = $request->file('pdf');
      $name = 'salary_slip.';
      $name = $name . $file->getClientOriginalExtension();;
      $imagePath = 'companies/' . $request->company_id . '/' . $name;
      Storage::disk('local')->put('documentation/' . $imagePath, file_get_contents($file), 'public');

      $company = Company::where('id', '=', request()->company_id)->first();
      $company->salary_slip_pdf_path = $imagePath;
      $company->update();
    }

    return response()->json([
      'data'  => [
        'image_path'  =>  $imagePath
      ],
      'success' =>  true
    ]);
  }

  public function pms_policies(Request $request)
  {
    $request->validate([
      'company_id'        => 'required'
    ]);

    $imagePath = '';
    if ($request->hasFile('word')) {
      $file = $request->file('word');
      $name = 'pms_policies.';
      $name = $name . $file->getClientOriginalExtension();;
      $imagePath = 'companies/' . $request->company_id . '/' . $name;
      Storage::disk('local')->put('documentation/' . $imagePath, file_get_contents($file), 'public');

      $company = Company::where('id', '=', request()->company_id)->first();
      $company->pms_policies_word_path = $imagePath;
      $company->update();
    }
    if ($request->hasFile('pdf')) {
      $file = $request->file('pdf');
      $name = 'pms_policies.';
      $name = $name . $file->getClientOriginalExtension();;
      $imagePath = 'companies/' . $request->company_id . '/' . $name;
      Storage::disk('local')->put('documentation/' . $imagePath, file_get_contents($file), 'public');

      $company = Company::where('id', '=', request()->company_id)->first();
      $company->pms_policies_pdf_path = $imagePath;
      $company->update();
    }

    return response()->json([
      'data'  => [
        'image_path'  =>  $imagePath
      ],
      'success' =>  true
    ]);
  }

  public function act_of_misconduct(Request $request)
  {
    $request->validate([
      'company_id'        => 'required'
    ]);

    $imagePath = '';
    if ($request->hasFile('word')) {
      $file = $request->file('word');
      $name = 'act_of_misconduct.';
      $name = $name . $file->getClientOriginalExtension();;
      $imagePath = 'companies/' . $request->company_id . '/' . $name;
      Storage::disk('local')->put('documentation/' . $imagePath, file_get_contents($file), 'public');

      $company = Company::where('id', '=', request()->company_id)->first();
      $company->act_of_misconduct_word_path = $imagePath;
      $company->update();
    }
    if ($request->hasFile('pdf')) {
      $file = $request->file('pdf');
      $name = 'act_of_misconduct.';
      $name = $name . $file->getClientOriginalExtension();;
      $imagePath = 'companies/' . $request->company_id . '/' . $name;
      Storage::disk('local')->put('documentation/' . $imagePath, file_get_contents($file), 'public');

      $company = Company::where('id', '=', request()->company_id)->first();
      $company->act_of_misconduct_pdf_path = $imagePath;
      $company->update();
    }

    return response()->json([
      'data'  => [
        'image_path'  =>  $imagePath
      ],
      'success' =>  true
    ]);
  }

  public function uan_activation(Request $request)
  {
    $request->validate([
      'company_id'        => 'required'
    ]);

    $imagePath = '';
    if ($request->hasFile('word')) {
      $file = $request->file('word');
      $name = 'uan_activation.';
      $name = $name . $file->getClientOriginalExtension();;
      $imagePath = 'companies/' . $request->company_id . '/' . $name;
      Storage::disk('local')->put('documentation/' . $imagePath, file_get_contents($file), 'public');

      $company = Company::where('id', '=', request()->company_id)->first();
      $company->uan_activation_word_path = $imagePath;
      $company->update();
    }
    if ($request->hasFile('pdf')) {
      $file = $request->file('pdf');
      $name = 'uan_activation.';
      $name = $name . $file->getClientOriginalExtension();;
      $imagePath = 'companies/' . $request->company_id . '/' . $name;
      Storage::disk('local')->put('documentation/' . $imagePath, file_get_contents($file), 'public');

      $company = Company::where('id', '=', request()->company_id)->first();
      $company->uan_activation_pdf_path = $imagePath;
      $company->update();
    }

    return response()->json([
      'data'  => [
        'image_path'  =>  $imagePath
      ],
      'success' =>  true
    ]);
  }

  public function online_claim(Request $request)
  {
    $request->validate([
      'company_id'        => 'required'
    ]);

    $imagePath = '';
    if ($request->hasFile('word')) {
      $file = $request->file('word');
      $name = 'online_claim.';
      $name = $name . $file->getClientOriginalExtension();;
      $imagePath = 'companies/' . $request->company_id . '/' . $name;
      Storage::disk('local')->put('documentation/' . $imagePath, file_get_contents($file), 'public');

      $company = Company::where('id', '=', request()->company_id)->first();
      $company->online_claim_word_path = $imagePath;
      $company->update();
    }
    if ($request->hasFile('pdf')) {
      $file = $request->file('pdf');
      $name = 'online_claim.';
      $name = $name . $file->getClientOriginalExtension();;
      $imagePath = 'companies/' . $request->company_id . '/' . $name;
      Storage::disk('local')->put('documentation/' . $imagePath, file_get_contents($file), 'public');

      $company = Company::where('id', '=', request()->company_id)->first();
      $company->online_claim_pdf_path = $imagePath;
      $company->update();
    }

    return response()->json([
      'data'  => [
        'image_path'  =>  $imagePath
      ],
      'success' =>  true
    ]);
  }

  public function kyc_update(Request $request)
  {
    $request->validate([
      'company_id'        => 'required'
    ]);

    $imagePath = '';
    if ($request->hasFile('word')) {
      $file = $request->file('word');
      $name = 'kyc_update.';
      $name = $name . $file->getClientOriginalExtension();;
      $imagePath = 'companies/' . $request->company_id . '/' . $name;
      Storage::disk('local')->put('documentation/' . $imagePath, file_get_contents($file), 'public');

      $company = Company::where('id', '=', request()->company_id)->first();
      $company->kyc_update_word_path = $imagePath;
      $company->update();
    }
    if ($request->hasFile('pdf')) {
      $file = $request->file('pdf');
      $name = 'kyc_update.';
      $name = $name . $file->getClientOriginalExtension();;
      $imagePath = 'companies/' . $request->company_id . '/' . $name;
      Storage::disk('local')->put('documentation/' . $imagePath, file_get_contents($file), 'public');

      $company = Company::where('id', '=', request()->company_id)->first();
      $company->kyc_update_pdf_path = $imagePath;
      $company->update();
    }

    return response()->json([
      'data'  => [
        'image_path'  =>  $imagePath
      ],
      'success' =>  true
    ]);
  }

  public function graduity_form_a(Request $request)
  {
    $request->validate([
      'company_id'        => 'required'
    ]);

    $imagePath = '';
    if ($request->hasFile('word')) {
      $file = $request->file('word');
      $name = 'graduity_form.';
      $name = $name . $file->getClientOriginalExtension();;
      $imagePath = 'companies/' . $request->company_id . '/' . $name;
      Storage::disk('local')->put('documentation/' . $imagePath, file_get_contents($file), 'public');

      $company = Company::where('id', '=', request()->company_id)->first();
      $company->graduity_form_word_path = $imagePath;
      $company->update();
    }
    if ($request->hasFile('pdf')) {
      $file = $request->file('pdf');
      $name = 'graduity_form.';
      $name = $name . $file->getClientOriginalExtension();;
      $imagePath = 'companies/' . $request->company_id . '/' . $name;
      Storage::disk('local')->put('documentation/' . $imagePath, file_get_contents($file), 'public');

      $company = Company::where('id', '=', request()->company_id)->first();
      $company->graduity_form_pdf_path = $imagePath;
      $company->update();
    }

    return response()->json([
      'data'  => [
        'image_path'  =>  $imagePath
      ],
      'success' =>  true
    ]);
  }
}
