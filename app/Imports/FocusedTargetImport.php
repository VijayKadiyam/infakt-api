<?php

namespace App\Imports;

use App\CrudeFocusedTarget;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Imports\HeadingRowFormatter;

HeadingRowFormatter::default('none');


class FocusedTargetImport implements ToModel, WithHeadingRow
{
    public function model(array $row)
    {
        if ($row['Target'] != '' || $row['Achieved'] != '') {
            $data = [
                'company_id'  =>  request()->company->id,
                'region'      =>  $row['Region'],
                'channel'     =>  $row['Channel'],
                'chain_name'        =>  $row['Chain Name'],
                'billing_code'       => (array_key_exists('Billing Code', $row) && $row['Billing Code']) ? $row['Billing Code'] : null,
                'store_code'       =>  $row['Store Code'],
                'store_name'        =>  $row['Store Name'],
                'location'        =>  $row['Location'],
                'city'       =>  $row['City'],
                'state'       =>  $row['State'],
                'rsm'        =>  $row['RSM'],
                'asm'       =>  $row['ASM'],
                'supervisor_code'       =>  $row['Supervisor Code'],
                'supervisor_name'       =>  $row['Supervisor Name'],
                'brand'       => (array_key_exists('BRAND', $row) && $row['BRAND']) ? $row['BRAND'] : NULL,
                'ba_status'       => (array_key_exists('BA status', $row) && $row['BA status'])  ? $row['BA status'] : NULL,
                'store_status'       => (array_key_exists('Store Status', $row) && $row['Store Status'])  ? $row['Store Status'] : NULL,
                'target'      =>  $row['Target'],
                'achieved'      =>  $row['Achieved'],
                'month'  =>  request()->month,
                'year'  =>  request()->year,
                'baby' => $row['Baby'],
                'baby_Kit' => $row['Baby Kit'],
                'baby_Oil' => $row['Baby Oil'],
                'bath_salt' => $row['Bath Salt'],
                'bathing' => $row['Bathing'],
                'body' => $row['Body'],
                'body_butter' => $row['Body Butter'],
                'body_cream' => $row['Body Cream'],
                'body_lotion' => $row['Body Lotion'],
                'body_scrub' => $row['Body Scrub'],
                'body_wash' => $row['Body Wash'],
                'capsules' => $row['Capsules'],
                'cleanser' => $row['Cleanser'],
                'cleansing' => $row['Cleansing'],
                'combo_kit' => $row['Combo kit'],
                'conditioner' => $row['Conditioner'],
                'cream' => $row['Cream'],
                'diaper' => $row['Diaper'],
                'dusting_powder' => $row['Dusting Powder'],
                'face_cream' => $row['Face Cream'],
                'face_free' => $row['Face Free'],
                'face_Mask' => $row['Face Mask'],
                'face_Milk' => $row['Face Milk'],
                'face_scrub' => $row['Face scrub'],
                'face_Serum' => $row['Face Serum'],
                'face_Spot' => $row['Face Spot'],
                'face_toner' => $row['Face Toner'],
                'facewash' => $row['Facewash'],
                'freebies' => $row['Freebies'],
                'gel' => $row['Gel'],
                'gift_pack' => $row['Gift Pack'],
                'hair_care' => $row['Hair Care'],
                'hair_Mask' => $row['Hair Mask'],
                'hair_Oil' => $row['Hair Oil'],
                'hair_Serum' => $row['Hair Serum'],
                'hand_cream' => $row['Hand Cream'],
                'hygine' => $row['Hygine'],
                'kajal' => $row['Kajal'],
                'kids_body' => $row['Kids Body'],
                'lip_balm' => $row['Lip Balm'],
                'lotion' => $row['Lotion'],
                'mask' => $row['Mask'],
                'moisturizer' => $row['Moisturizer'],
                'mosquito_protection' => $row['Mosquito Protection'],
                'oil' => $row['Oil'],
                'oral' => $row['Oral'],
                'otc' => $row['OTC'],
                'peeling' => $row['Peeling'],
                'serum' => $row['Serum'],
                'shampoo' => $row['Shampoo'],
                'sheet_mask' => $row['Sheet Mask'],
                'sub_cat' => $row['Sub-Cat'],
                'sun_cat' => $row['Sun-Cat'],
                'sun_care' => $row['Sun Care'],
                'sunscreen' => $row['Sunscreen'],
                'tablets' => $row['Tablets'],
                'toner' => $row['Toner'],
                'yogurt_for' => $row['Yogurt For'],
            ];
            return new CrudeFocusedTarget($data);
        }
    }

    public function headingRow(): int
    {
        return 1;
    }

    public function batchSize(): int
    {
        return 1000;
    }
}
