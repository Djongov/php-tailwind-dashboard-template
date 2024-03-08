<?php

// Path: Controllers/Api/Firewall.php

// Called in /api/firewall in /Views/api/firewall.php

// Responsible for handling the CRUD api calls for the `firewall` table in the database and returning the appropriate api json response

namespace Controllers\Api;

use Models\Api\Firewall as FirewallModel;
use App\Exceptions\FirewallException;
use Controllers\Api\Output;

class Firewall
{
    /**
     * Get an IP or all IPs from the firewall table and return them as a json response
     * @category   Controller - Firewall
     * @author     Original Author <djongov@gamerz-bg.com>
     * @param      string $ip the ip in normal or CIDR notation. If empty, returns all IPs
     * @return     string json api response
     * @throws     FirewallException
     */
    public function get(string $ip) : string
    {
        $firewall = new FirewallModel();
        try {
            $result = $firewall->get($ip);
            return Output::success($result);
        } catch (FirewallException $e) {
            Output::error($e->getMessage());
        } catch (\Exception $e) {
            Output::error($e->getMessage());
        }
    }
    /**
     * Saves an IP to the firewall table. If the IP already exists or is malformed, throws an exception, otherwise saves the IP and returns a json response
     * @category   Controller - Firewall
     * @author     Original Author <djongov@gamerz-bg.com>
     * @param      string $ip the ip in normal or CIDR notation
     * @param      string $createdBy the user who creats the IP, not only for logging purposes, but also for the firewall to know who added the IP
     * @param      string $comment the comment for the IP
     * @return     string json api response
     * @throws     FirewallException
     */
    public function add($ip, $createdBy, $comment = '') : string
    {
        $firewall = new FirewallModel();
        try {
            $firewall->save($ip, $createdBy, $comment);
            return Output::success('ip ' . $ip . ' added to the firewall');
        } catch (FirewallException $e) {
            Output::error($e->getMessage());
        } catch (\Exception $e) {
            Output::error($e->getMessage());
        }
    }
    /**
     * Updates an IP to the firewall table. If the IP does not exist or has unknown columns, throws an exception, otherwise updates the IP and returns a json response
     * @category   Controller - Firewall
     * @author     Original Author <djongov@gamerz-bg.com>
     * @param      array $data the data to update, must be an associative array with the column name as key and the new value as value
     * @param      int $id the id of the IP
     * @param      string $updatedBy the user who updates the IP, for logging purposes
     * @return     string json api response
     * @throws     FirewallException
     */
    public function update(array $data, int $id, string $updatedBy) : string
    {
        $firewall = new FirewallModel();
        try {
            $firewall->update($data, $id, $updatedBy);
            return Output::success('ip with id ' . $id . ' updated');
        } catch (FirewallException $e) {
            Output::error($e->getMessage());
        } catch (\Exception $e) {
            Output::error($e->getMessage());
        }
    }
    /**
     * Deletes an IP from the firewall table. If the IP does not exist, throws an exception, otherwise deletes the IP and returns a json response
     * @category   Controller - Firewall
     * @author     Original Author <djongov@gamerz-bg.com>
     * @param      int $id the id of the IP
     * @param      string $deletedBy the user who deletes the IP, for logging purposes
     * @return     string json api response
     * @throws     FirewallException
     */
    public function delete(int $id, string $deletedBy) : string
    {
        $firewall = new FirewallModel();
        try {
            $firewall->delete($id, $deletedBy);
            return Output::success('ip with id ' . $id . ' deleted');
        } catch (FirewallException $e) {
            Output::error($e->getMessage());
        } catch (\Exception $e) {
            Output::error($e->getMessage());
        }
    }
}
