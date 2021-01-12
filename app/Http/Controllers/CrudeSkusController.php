<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Imports\SkuImport;
use App\CrudeSku;
use Maatwebsite\Excel\Facades\Excel;
use App\User;
use App\Sku;
use App\Product;
use App\SkuType;
use App\Unit;
use App\OfferType;
use App\Offer;
use App\Stock;
use App\CompanyDesignation;

class CrudeSkusController extends Controller
{
  public function __construct()
  {
    $this->middleware(['auth:api', 'company'])
      ->except(['index']);
  }

  public function index()
  {
    return response()->json([
      'data'  =>  CrudeSku::all()
    ]);
  }

  public function uploadSku(Request $request)
  {
    set_time_limit(0);
    
    if ($request->hasFile('skus')) {
      $file = $request->file('skus');

      Excel::import(new SkuImport, $file);
      
      return response()->json([
        'data'    =>  CrudeSku::all(),
        'success' =>  true
      ]);
    }
  }

  public function processSku()
  {
    set_time_limit(0);
    
    $crude_skus = CrudeSku::all();

    foreach($crude_skus as $master) {
      if($master->sku_name) {

        // Save DISTRIBUTOR Designation
        $companyDesignation = CompanyDesignation::where('name', '=', 'DISTRIBUTOR')
          ->where('company_id', '=', request()->company->id)
          ->first();
        if(!$companyDesignation) {
          $data = [
            'name'  =>  'DISTRIBUTOR'
          ];
          $companyDesignation = new CompanyDesignation($data);
          request()->company->company_designations()->save($companyDesignation);
        }

        // Save Distributor Name
        $distributor = User::where('name', '=', $master->distributor_name)
          ->whereHas('roles',  function($q) {
            $q->where('name', '=', 'DISTRIBUTOR');
          })
          ->whereHas('companies',  function($q) {
            $q->where('name', '=', request()->company->name);
          })
          ->first();
        if(!$distributor) {
          $data = [
            'name'            =>  $master->distributor_name,
            'email'           =>  str_replace(' ', '', $master->distributor_name) . '@gmail.com',
            'phone'           =>  0,
            'employee_code'   =>  '',
            'password'        =>  bcrypt('123456'),
            'password_backup' =>  bcrypt('123456'),
            'active'          =>  1
          ];
          $distributor = new User($data);
          $distributor->save();
          $distributor->assignRole(10);
          $distributor->assignCompany(request()->company->id);
        }

        // Save SKU
        $sku = Sku::where('name', '=', $master->sku_name)
          ->where('company_id', '=', request()->company->id)
          ->first();
        if(!$sku) {
          $data = [
            'name'        =>  $master->sku_name,
            'company_id'  =>  request()->company->id,
          ];
          $sku = new Sku($data);
          $product = Product::find(1);
          $product->skus()->save($sku);
          $sku->save();
        }

        // Save SKU Types 
        $skuType = SkuType::where('name', '=', $master->sku_type)
          ->where('company_id', '=', request()->company->id)
          ->first();
        if(!$skuType) {
          $data = [
            'name'        =>  $master->sku_type,
          ];
          $skuType = new SkuType($data);
          request()->company->sku_types()->save($skuType);
        }

        // Save Units
        $unit = Unit::where('name', '=', $master->unit)
          ->where('company_id', '=', request()->company->id)
          ->first();
        if(!$unit) {
          $data = [
            'name'        =>  $master->unit,
          ];
          $unit = new Unit($data);
          request()->company->units()->save($unit);
        }

        if($master->offer_type != null) {
          // Save Offer Type
          $offerType = OfferType::where('name', '=', $master->offer_type)
            ->where('company_id', '=', request()->company->id)
            ->first();
          if(!$offerType) {
            $data = [
              'name'        =>  $master->offer_type,
            ];
            $offerType = new OfferType($data);
            request()->company->offer_types()->save($offerType);
          }

          // Save Offer
          $offer = Offer::where('offer', '=', $master->offer)
            ->where('offer_type_id', '=', $offerType->id)
            ->where('company_id', '=', request()->company->id)
            ->first();
          if(!$offer) {
            $data = [
              'offer'         =>  $master->offer,
              'offer_type_id' =>  $offerType->id,
            ];
            $offer = new Offer($data);
            request()->company->offers()->save($offer);
          }
        }

        // Save Stock
        $stock = Stock::where('invoice_no', '=', $master->invoice_no)
          ->first();
        if(!$stock) {
          $data = [
            'distributor_id'=>  $distributor->id,
            'sku_id'        =>  $sku->id,
            'sku_type_id'   =>  $skuType->id,
            'invoice_no'    =>  $master->invoice_no,
            'date'          =>  $master->date,
            'qty'           =>  $master->qty,
            'unit_id'       =>  $unit->id,
            'offer_id'      =>  isset($offer) ? $offer->id : null,
            'price'         =>  $master->price_per_unit,
            'total'         =>  $master->total_price
          ];
          $stock = new Stock($data);
          $sku->stocks()->save($stock);
        }
      }
    }

    return $crude_skus;
  }

  public function truncate()
  {
    CrudeSku::truncate();
  }
}
