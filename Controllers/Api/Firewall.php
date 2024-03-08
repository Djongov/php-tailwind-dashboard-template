<?php

namespace Controllers\Api;

use Models\Api\Firewall as FirewallModel;
use App\Exceptions\FirewallException;
use Controllers\Api\Output;

class Firewall
{
    // For now only the add method is implemented as the other methods are presented by the DataGrid functionality
    public function get(string $ip) : string
    {
        $firewall = new FirewallModel();
        try {
            $result = $firewall->get($ip);
            return Output::success($result);
        } catch (FirewallException $e) {
            return Output::error($e->getMessage());
        }
    }
    public function add($ip, $comment = '') : void
    {
        $firewall = new FirewallModel();
        try {
            $firewall->save($ip, $comment);
            echo Output::success('ip ' . $ip . ' added to the firewall');
        } catch (FirewallException $e) {
            Output::error($e->getMessage());
        }
    }
    public function update(array $data, int $id) : void
    {
        $firewall = new FirewallModel();
        try {
            $firewall->update($data, $id);
            echo Output::success('ip with id ' . $id . ' updated');
        } catch (FirewallException $e) {
            Output::error($e->getMessage());
        }
    }
    public function delete(int $id) : void
    {
        $firewall = new FirewallModel();
        try {
            $firewall->delete($id);
            echo Output::success('ip with id ' . $id . ' deleted');
        } catch (FirewallException $e) {
            Output::error($e->getMessage());
        }
    }
}
