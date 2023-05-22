<?php

namespace Und3fined\Module\AdminApi\Classes;

use Db;

class Key extends \ObjectModel
{
    public $key;
    public $description;

    public $active;

    /**
     * @see ObjectModel::$definition
     */
    public static $definition = array(
        'table' => 'ps_adminapi_key',
        'primary' => 'id_key',
        'fields' => array(
            'key' => array('type' => self::TYPE_STRING, 'validate' => 'isString'),
            'description' => array('type' => self::TYPE_STRING, 'validate' => 'isString'),
            'active' => array('type' => self::TYPE_BOOL, 'validate' => 'isBool'),
        ),
    );

    public function add($autodate = true, $nullValues = false)
    {
        if (Key::keyExists($this->key)) {
            return false;
        }

        return parent::add($autodate = true, $nullValues = false);
    }

    public function delete()
    {
        return parent::delete() && ($this->deleteAssociations() !== false);
    }

    public function deleteAssociations()
    {
        return Db::getInstance()->delete('ps_adminapi_key_permission', 'id_key = ' . (int) $this->id);
    }

    public static function getAll()
    {
        $db = Db::getInstance();

        $sql = 'SELECT * FROM ' . _DB_PREFIX_ . 'ps_adminapi_key ORDER BY id_key DESC';

        return $db->ExecuteS($sql);
    }


    /**
     * Retrieve Key object by key
     *
     * @param string $key
     *
     * @return Key|bool
     */
    public static function getByKey($key)
    {
        $sql = 'SELECT * FROM `' . _DB_PREFIX_ . 'ps_adminapi_key` WHERE `key` = \'' . pSQL($key) . '\'';

        $result = Db::getInstance()->getRow($sql);

        if (!$result) {
            return false;
        }

        $adminapi_key = new Key();

        $adminapi_key->id = $result['id_key'];
        $adminapi_key->key = $result['key'];
        $adminapi_key->description = $result['description'];
        $adminapi_key->active = $result['active'];

        return $adminapi_key;
    }

    public static function getPermissionForAccount($auth_key)
    {
        $result = Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS('
			SELECT p.*
			FROM `' . _DB_PREFIX_ . 'ps_adminapi_key_permission` p
			LEFT JOIN `' . _DB_PREFIX_ . 'ps_adminapi_key` a ON (a.id_key = p.id_key)
			WHERE a.key = \'' . pSQL($auth_key) . '\'
		');
        
        $permissions = array();
        if ($result) {
            foreach ($result as $row) {
                $permissions[$row['resource']][] = $row['method'];
            }
        }

        return $permissions;
    }

    public static function setPermissionForAccount($id_key, $permissions_to_set)
    {
        $sql = 'DELETE FROM `' . _DB_PREFIX_ . 'ps_adminapi_key_permission` WHERE `id_key` = ' . (int) $id_key;

        if (!Db::getInstance()->execute($sql)) {
            return false;
        }

        if (isset($permissions_to_set)) {
            $permissions = array();
            $resources = Request::getResources();
            $methods = array('GET', 'PUT', 'POST', 'DELETE', 'HEAD');

            foreach ($permissions_to_set as $resource_method => $resource_names) {
                if (in_array($resource_method, $methods)) {
                    foreach ($resource_names as $resource_name) {
                        if (in_array($resource_name, array_keys($resources))) {
                            $permissions[] = array($resource_method, $resource_name);
                        }
                    }
                }
            }

            $key = new Key($id_key);
            if ($key->deleteAssociations() && $permissions) {
                $sql = 'INSERT INTO `' . _DB_PREFIX_ . 'ps_adminapi_key_permission` (`id_key_permission` ,`resource` ,`method` ,`id_key`) VALUES ';
                foreach ($permissions as $permission) {
                    $sql .= '(NULL , \'' . pSQL($permission[1]) . '\', \'' . pSQL($permission[0]) . '\', ' . (int) $id_key . '), ';
                }
                $sql = rtrim($sql, ', ');
                if (!Db::getInstance()->execute($sql)) {
                    return false;
                }
            }
        }

        return true;
    }
    public static function keyExists($key)
    {
        return Db::getInstance(_PS_USE_SQL_SLAVE_)->getValue('
		SELECT `key`
		FROM ' . _DB_PREFIX_ . 'ps_adminapi_key
		WHERE `key` = "' . pSQL($key) . '"');
    }
    
    public static function isKeyActive($key)
    {
        return Db::getInstance(_PS_USE_SQL_SLAVE_)->getValue('
		SELECT active
		FROM `' . _DB_PREFIX_ . 'ps_adminapi_key`
		WHERE `key` = "' . pSQL($key) . '"');
    }

    public function can($resource, $method) {
        return Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS('
			SELECT p.*
			FROM `' . _DB_PREFIX_ . 'ps_adminapi_key_permission` p
			LEFT JOIN `' . _DB_PREFIX_ . 'ps_adminapi_key` a ON (a.id_key = p.id_key)
			WHERE a.key = \'' . pSQL($this->key) . '\'
            AND p.resource = \'' . pSQL($resource) . '\'
            AND p.method = \'' . pSQL($method) . '\'
		');
    }
}
