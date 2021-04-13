<?php

namespace App\Http\Controllers;

use App\Models\Gateway;
use App\Models\Peripheral;
use Illuminate\Auth\Access\Gate;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Log;
use function PHPUnit\Framework\isEmpty;

class GatewayController extends Controller
{

    public function index()
    {
        return json_encode([
            'success' => true,
//            'payload' => Gateway::all(),
            'payload' => Gateway::with('peripherals')->get(),
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'ipv4' => 'required|ipv4',
            'serial' => 'required',
            'name' => 'required'
        ]);

        $check1 = Gateway::where('ipv4', $request->ipv4)->first();
        $check2 = Gateway::where('serial', $request->serial)->first();
        if (!empty($check1))
            return json_encode([
                'success' => false,
                'message' => 'IPv4 field is already taken.',
            ]);
        if (!empty($check2))
            return json_encode([
                'success' => false,
                'message' => 'Serial field is already taken.',
            ]);

        $item = new Gateway([
            'ipv4' => $request->get('ipv4'),
            'serial'=> $request->get('serial'),
            'name'=> $request->get('name')
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
            'ipv4' => 'required|ipv4',
            'serial' => 'required',
            'name' => 'required'
        ]);

        $check1 = Gateway::where('ipv4', $request->ipv4)->where('_id', '!=', $request->_id)->first();
        $check2 = Gateway::where('serial', $request->serial)->where('_id', '!=', $request->_id)->first();

        if (!empty($check1))
            return json_encode([
                'success' => false,
                'message' => 'IPv4 field is already taken.',
            ]);
        if (!empty($check2))
            return json_encode([
                'success' => false,
                'message' => 'Serial field is already taken.',
            ]);

        $item = Gateway::find($request->_id);
        if (empty($item))
            return json_encode([
                'success' => false,
                'message' => 'No item found with ID: '.$request->_id,
            ]);

        $item->name = $request->get('name');
        $item->serial = $request->get('serial');
        $item->ipv4 = $request->get('ipv4');

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

        $item = Gateway::find($request->_id);
        if (empty($item))
            return json_encode([
                'success' => false,
                'message' => 'No item found with ID: '.$request->_id,
            ]);

        Peripheral::where('id_gateway', $item->_id)->delete();
        $item->delete();
        return json_encode([
            'success' => true,
            ]);
    }

}
