<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rules\Unique;

class HikvisionController extends Controller
{
    public function store(Request $request){
        //xml
        $xml_string = $request->file('anpr_xml')->get();
        $xml = simplexml_load_string($xml_string);
        $license_plate = (string)$xml->ANPR->licensePlate;
        $date = (string)$xml->dateTime;

        if(!$this->isValidLicensePlate($license_plate)){
            return response()->json(['message' => 'Invalid license plate'], 400);
        }

        $filename = uniqid() . "_". $license_plate;

        Storage::disk('local')->put('public/anpr/'.$filename.'.xml', $xml_string);

        $detection_picture = $request->file('detectionPicture_jpg')->get();
        Storage::disk('local')->put('public/anpr/'.$filename.'.jpg', $detection_picture);

        $license_plate_picture = $request->file('licensePlatePicture_jpg')->get();
        Storage::disk('local')->put('public/anpr/'.$filename.'_license_plate.jpg', $license_plate_picture);

        return response()->json(['message' => 'Success'], 200);

    }

    protected function isValidLicensePlate($license_plate): bool
    { 
        $cases = [
            '[a-zA-Z]{1}[0-9]{4}',
            '[a-zA-Z]{3}[0-9]{3}',
            '[a-zA-Z]{2}[0-9]{4}',
            '[a-zA-Z]{1}[0-9]{5}',
            '[a-zA-Z]{2}[0-9]{3}',
            '[a-zA-Z]{3}[0-9]{2}',
            '[a-zA-Z]{3}[0-9]{2}[a-zA-Z]{1}',
            '[0-9]{3}[a-zA-Z]{2}',
            '[0-9]{3}[a-zA-Z]{3}',
            '[a-zA-Z]{2}[0-9]{5}',
            '[a-zA-Z]{3}[0-9]{4}',
            '[a-zA-Z]{2}[0-9]{6}', 
        ];

        return preg_match('/^('.implode('|', $cases).')$/', $license_plate);
    }
}
