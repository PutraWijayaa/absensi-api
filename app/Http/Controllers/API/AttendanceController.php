<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;

use App\Models\Attendances;
use App\Models\Offices;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Log;

class AttendanceController extends Controller
{

    // public function attendanceIn(Request $request, $id)
    public function attendanceIn(Request $request)
    {
        $office_id = $request->office_id;
        // $office_id = $request->office_id;

        $office = Offices::where('is_active', 1)->where('id', $office_id)->first();
        // $office = Offices::where('is_active', 1)->where('id', $id)->first();

        //radius
        $lat_from_employee = $request->lat_from_employee;
        $long_from_employee = $request->long_from_employee;

        $lat_from_office = $office->office_lat;
        $long_from_office = $office->office_long;

        $radius = $this->getDistanceBetweenPoints($lat_from_employee, $long_from_employee, $lat_from_office, $long_from_office);

        $meter = round($radius['meters']);
        // $meter = $meter / 1000;

        // jika radius kurang dari 100 meter
        if ($meter >= 100) {
            return response()->json(['message' => 'Employee is in the office']);
        }


        return response()->json(['Meter' => $meter]);
        // $radius - $this->getDistanceBetweenPoints()
        // return $radius;
    }

    public function attendanceOut()
    {

    }

    protected function getDistanceBetweenPoints($lat1, $lon1, $lat2, $lon2)
    {
        $theta = $lon1 - $lon2;
        $miles = (sin(deg2rad($lat1)) * sin(deg2rad($lat2))) + (cos(deg2rad($lat1)) * cos(deg2rad($lat2)) * cos(deg2rad($theta)));
        $miles = acos($miles);
        $miles = rad2deg($miles);
        $miles = $miles * 60 * 1.1515;
        $feet  = $miles * 5280;
        $yards = $feet / 3;
        $kilometers = $miles * 1.609344;
        $meters = $kilometers * 1000;
        return compact('miles', 'feet', 'yards', 'kilometers', 'meters');
    }

    public function index()
    {
        try {
            $Attendances = Attendances::orderBy('id', 'desc')->get();
            return response()->json(['success' => true, 'data' => $Attendances]);
        } catch (\Throwable $th) {
            Log::error('Failed to fetch data Attendances: ' . $th->getMessage());
            return response()->json([
                'success' => false,
                'message' => $th->getMessage(),
            ], 500);
        }
    }

    public function store(Request $request)
    {
        $validate = Validator::make($request->all(), [
            'office_name' => 'required',
            'office_lat' => 'required',
            'office_long' => 'required',
        ]);

        if ($validate->fails()) {
            return response()->json([
                'success' => false,
                'message' => $validate->errors(),
            ], 422);
        }

        try {
            $Attendances = Attendances::create([
                'office_name' => $request->office_name,
                'office_phone' => $request->office_phone,
                'office_address' => $request->office_address,
                'office_lat' => $request->office_lat,
                'office_long' => $request->office_long,
                'is_active' => $request->is_active
            ]);

            return response()->json(['success' => true, 'message' => 'Attendances added success', 'data' => $Attendances], 201);
        } catch (\Throwable $th) {
            Log::error('Failed insert : ' . $th->getMessage());
            return response()->json(['success' => true, 'message' => 'Failed Create Attendances'], 500);
        }
    }

    public function show(String $id)
    {
        try {
            $Attendances = Attendances::findOrFail($id);
            return response()->json(['success' => true, 'message' => 'Show Data Success', 'data' => $Attendances]);
        } catch (\Throwable $th) {
            Log::error('Failed Show : ' . $th->getMessage());
            return response()->json([
                'success' => false,
                'message' => $th->getMessage(),
            ], 500);
        }
    }

    public function update(Request $request, $id)
    {
        $validate = Validator::make($request->all(), [
            'office_name' => 'required',
            'office_lat' => 'required',
            'office_long' => 'required',
        ]);

        if ($validate->fails()) {
            return response()->json([
                'success' => false,
                'message' => $validate->errors(),
            ], 422);
        }

        try {
            $data = [
                'office_name' => $request->office_name,
                'office_phone' => $request->office_phone,
                'office_address' => $request->office_address,
                'office_lat' => $request->office_lat,
                'office_long' => $request->office_long,
                'is_active' => $request->is_active
            ];
            $Attendances = Attendances::findOrFail($id);
            $Attendances->update($data);

            return response()->json(['success' => true, 'message' => 'Attendances Update Success', 'data' => $Attendances]);
        } catch (\Throwable $th) {
            Log::error('Failed Update : ' . $th->getMessage());
            return response()->json(['success' => true, 'message' => 'Failed Create Attendances'], 500);
        }
    }

    public function destroy($id)
    {
        try{
            $Attendances = Attendances::find($id);
            return response()->json(['message' => 'Employe delete success']);
        } catch (\Throwable $th) {
            return response()->json(['message' => $th->getMessage()], 500);
        }
    }
}
