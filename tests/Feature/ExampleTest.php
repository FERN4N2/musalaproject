<?php
declare(strict_types=1);
namespace Tests\Feature;

use App\Http\Controllers\GatewayController;
use App\Http\Controllers\PeripheralController;
use App\Models\Gateway;
use App\Models\Peripheral;
use Illuminate\Http\Request;
use Tests\TestCase;

class ExampleTest extends TestCase

{
    public function testGatewayStore(): array
    {
        $expected = [
            'serial' => 'serial01',
            'name' => 'name01',
            'ipv4' => '1.1.1.1',
        ];
        Gateway::where('ipv4', $expected['ipv4'])->delete();

        $controller = new GatewayController();
        $controller->store(new Request($expected));

        $item = Gateway::where('ipv4', $expected['ipv4'])->first();
        $result = [
            'serial' => $item->serial,
            'name' => $item->name,
            'ipv4' => $item->ipv4,
        ];
        $this->assertSame($expected, $result);
        $return = ['controller' => $controller, 'item' => $item];
        return $return;
    }

    /**
     * @depends testGatewayStore
     * @return array
     */
    public function testGatewayUpdate(array $data): array
    {
        $item = $data['item'];
        $controller = $data['controller'];

        $expected = [
            '_id' => $item->_id,
            'serial' => 'serial02',
            'name' => 'name02',
            'ipv4' => $item->ipv4
        ];
        $controller->update(new Request($expected));

        $item = Gateway::where('ipv4', $expected['ipv4'])->first();
        $result = [
            '_id' => $item->_id,
            'serial' => $item->serial,
            'name' => $item->name,
            'ipv4' => $item->ipv4,
        ];
        $this->assertSame($expected, $result);
        $return = ['controller' => $controller, 'item' => $item];
        return $return;
    }

    /**
     * @depends testGatewayUpdate
     * @return void
     */
    public function testGatewayDestroy(array $data)
    {
        $item = $data['item'];
        $controller = $data['controller'];

        $id = $item->_id;
        $expected = [
            '_id' => $id,
        ];
        $controller->destroy(new Request($expected));
        $item = Gateway::find($id);

        $this->assertNull($item);
    }

    public function testPeripheralStore(): array
    {
        $parent_data = [
            'serial' => 'serial11',
            'name' => 'name11',
            'ipv4' => '5.5.5.5',
        ];

        $gateway = Gateway::where('ipv4', $parent_data['ipv4'])->first();
        if (isset($gateway)) {
            Peripheral::where('id_gateway', $gateway->_id)->delete();
            $gateway->delete();
        }

        $parent_controller = new GatewayController();
        $parent_controller->store(new Request($parent_data));
        $parent = Gateway::where('ipv4', $parent_data['ipv4'])->first();

        $expected = [
            'vendor' => 'vendor01',
            'status' => true,
            'uid' => '12345',
            'id_gateway' => $parent->_id,
        ];

        Peripheral::where('uid', $expected['uid'])->delete();

        $controller = new PeripheralController();
        $controller->store(new Request($expected));

        $item = Peripheral::where('uid', $expected['uid'])->first();
        $result = [
            'vendor' => $item->vendor,
            'status' => $item->status,
            'uid' => $item->uid,
            'id_gateway' => $item->id_gateway,
        ];

        $this->assertSame($expected, $result);

        return ['controller' => $controller, 'item' => $item];
    }

    /**
     * @depends testPeripheralStore
     * @return array
     */
    public function testPeripheralUpdate(array $data): array
    {
        $item = $data['item'];
        $expected = [
            '_id' => $item->_id,
            'vendor' => 'vendor02',
            'status' => false,
            'uid' => '1234567890',
            'id_gateway' => $item->id_gateway,
        ];
        $controller = $data['controller'];
        $controller->update(new Request($expected));

        $item = Peripheral::find($expected['_id']);
        $result = [
            '_id' => $item->_id,
            'vendor' => $item->vendor,
            'status' => $item->status,
            'uid' => $item->uid,
            'id_gateway' => $item->id_gateway,
        ];

        $this->assertSame($expected, $result);
        return ['controller' => $controller, 'item' => $item];
    }

    /**
     * @depends testPeripheralUpdate
     * @return array
     */
    public function testPeripheralMaxAmount(array $data): array
    {
        $item = $data['item'];
        $controller = $data['controller'];
        for ($i = 1; $i < 10; $i++) {
            $response = $controller->store(new Request([
                'vendor' => 'vendor20' . $i,
                'status' => false,
                'uid' => 200 + $i,
                'id_gateway' => $item->id_gateway,
            ]));
//            Log::info($response);
            $this->assertTrue(json_decode($response)->success);
        }

        // Now the Gateway has 10 peripherals, attempting an 11nth item should return SUCCESS == FALSE
        $response = $controller->store(new Request([
            'vendor' => 'vendor50',
            'status' => false,
            'uid' => 50,
            'id_gateway' => $item->id_gateway,
        ]));

        $this->assertFalse(json_decode($response)->success);
        return ['controller' => $controller, 'item' => $item];
    }

    /**
     * @depends testPeripheralMaxAmount
     * @return void
     */
    public function testPeripheralDestroy(array $data)
    {
        $item = $data['item'];
        $controller = $data['controller'];

        $idGateway = $item->id_gateway;
        $peripherals = Peripheral::where('id_gateway', $idGateway)->get();

        foreach ($peripherals as $peripheral)
            $controller->destroy(new Request([
                '_id' => $peripheral->_id
            ]));

        $item = Peripheral::where('id_gateway', $idGateway)->first();
        Gateway::find($idGateway)->delete();
        $this->assertNull($item);
    }
}