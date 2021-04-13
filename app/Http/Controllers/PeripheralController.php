<?php

namespace App\Http\Controllers;

use App\Models\Peripheral;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;

class PeripheralController extends Controller
{

    public function index(Request $request)
    {
        if (!isset($request->_id))
            return json_encode([
                'success' => false,
                'message' => 'No item ID found.'
            ]);

        return json_encode([
            'success' => true,
            'payload' => Peripheral::where('id_gateway', $request->_id)->get(),
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'vendor' => 'required',
            'status' => 'required|boolean',
            'uid' => 'required|integer',
            'id_gateway' => 'required',
        ]);

        $check1 = Peripheral::where('uid', $request->uid)->first();
        if (!empty($check1))
            return json_encode([
                'success' => false,
                'message' => 'UID field is already taken.',
            ]);
        $check2 = Peripheral::where('id_gateway', $request->id_gateway)->count();
        if ($check2 >= 10)
            return json_encode([
                'success' => false,
                'message' => 'No more than 10 peripherals per gateway allowed.',
            ]);


        $item = new Peripheral([
            'vendor' => $request->get('vendor'),
            'status' => $request->get('status'),
            'uid' => $request->get('uid'),
            'id_gateway' => $request->get('id_gateway'),
            'created' => Carbon::now()->toDateTimeString(),
        ]);

        $item->save();
        return json_encode([
            'success' => true
        ]);
    }

    public function update(Request $request)
    {
        $request->validate([
            '_id' => 'required',
            'vendor' => 'required',
            'status' => 'required|boolean',
            'uid' => 'required',
            'id_gateway' => 'required',
        ]);

        $check1 = Peripheral::where('uid', $request->uid)->where('_id', '!=', $request->_id)->first();
        if (!empty($check1))
            return json_encode([
                'success' => false,
                'message' => 'UID field is already taken.',
            ]);

        $item = Peripheral::find($request->_id);
        if (empty($item))
            return json_encode([
                'success' => false,
                'message' => 'No item found with ID: '.$request->_id,
            ]);

        $item->status = $request->get('status');
        $item->vendor = $request->get('vendor');
        $item->uid = $request->get('uid');

        $item->update();

        return json_encode([
            'success' => true
        ]);
    }

    public function destroy(Request $request)
    {
        $request->validate([
            '_id' => 'required'
        ]);

        $item = Peripheral::find($request->_id);
        if (empty($item))
            return json_encode([
                'success' => false,
                'message' => 'No item found with ID: '.$request->_id,
            ]);

        $item->delete();
        return json_encode([
            'success' => true,
        ]);
    }

}
