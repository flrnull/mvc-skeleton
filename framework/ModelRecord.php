<?php

/**
 * @author Evgeniy Udodov <flr.null@gmail.com>
 */

class ModelRecord {

    /**
     * Should be set in child!
     *
     * @var string
     */
    protected $table;

    /**
     * Could be set in child
     *
     * @var string
     */
    protected $dbName;

    /**
     * IoC container.
     *
     * @var Pimple
     */
    protected $container;

    /**
     * Model properties.
     * Should be set in child!
     *
     * For example: array(
     *     array(
     *         "name" => "id",
     *         "type" => PDO::PARAM_INT,
     *         "primary" => true,
     *     ),
     * )
     *
     * @var array
     */
    protected $properties = array();

    /**
     * Values for model properties.
     * For example: array(
     *     "id" => 1
     * )
     *
     * @var array
     */
    protected $params = array();

    /**
     * Calculated when it needed first time.
     *
     * @var array
     */
    protected $propertiesByNameArray = null;

    /**
     * Constructor.
     *
     * @param Pimple $container IoC container
     */
    public function __construct($container) {
        $this->container = $container;
    }

    /**
     * @return ModelRecord
     */
    public static function instance() {
        $className = get_called_class();
        return new $className();
    }

    /**
     * Init model with params.
     */
    public function setParams(/* Params */) {
        $params = func_get_args();
        if (!is_array($params)) {
            return;
        }
        $paramsCount = count($params);
        $propCount = count($this->properties);
        if ($paramsCount === $propCount) {
            $withPrimary = true;
        } elseif ($paramsCount === ($propCount-1)) {
            $withPrimary = false;
        } else {
            return;
        }
        foreach ($this->properties as $index=>$property) {
            if ($withPrimary) {
                $this->params[$property['name']] = $params[$index];
            } elseif ($index > 0) {
                $this->params[$property['name']] = $params[$index-1];
            }
        }
    }

    /**
     * Params is empty.
     *
     * @return bool
     */
    public function isEmpty() {
        return empty($this->params);
    }

    /**
     * Checks in DB all unique params.
     *
     * @return bool
     */
    public function isExists() {
        $propsByName = $this->propertiesGetByName();
        $tableName = $this->table;
        $query = "SELECT count(*) as count FROM `{$tableName}` WHERE ";
        $loop = 0;
        $preparedTypes = array();
        foreach ($this->params as $param=>$value) {
            if (isset($propsByName[$param]['unique'])
                && $propsByName[$param]['unique'])
            {
                if ($loop) {
                    $query .= " OR ";
                }
                $query .= "`{$param}` = :{$param}";
                $preparedTypes[] = array(":{$param}", $value, $propsByName[$param]['type']);
            }
            $loop++;
        }
        $db = $this->dbConnect()->prepare($query);
        foreach ($preparedTypes as $prepared) {
            $db->bindValue($prepared[0], $prepared[1], $prepared[2]);
        }
        $result = $db->execute()->fetch(PDO::FETCH_ASSOC);
        if ($result['count']) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * Compares all params with properties array.
     *
     * @return bool
     */
    public function isValid() {
        try {
            $result = $this->isValidProcess();
        } catch (Exception $e) {
            return false;
        }
        return $result;
    }

    /**
     * Compares all params with properties array.
     * @TODO implement
     * @throws Exception
     * @return bool
     */
    public function isValidProcess() {
        if ($this->isEmpty()) {
            throw new Exception('Object is empty');
        }
        foreach($this->properties as $property) {
            // First we check that param exists
            if (!isset($property['primary']) || !$property['primary']) {
                if (!isset($this->params[$property['name']])) {
                    throw new Exception("Error on first check. Param {$property['name']} is empty");
                }
            } else {
                if (!isset($this->params[$property['name']])) {
                    continue;
                }
            }
            $paramValue = $this->params[$property['name']];
            // Then we check type
            if ($property['type'] === PDO::PARAM_INT) {
                if (!is_numeric($paramValue)
                    || (isset($property['min']) && $property['min'] > $paramValue)
                    || (isset($property['max']) && $property['max'] < $paramValue)
                ) {
                    throw new Exception("Error on int check. Param {$property['name']} = {$paramValue}");
                }
            } elseif ($property['type'] === PDO::PARAM_STR) {
                if (!is_string($paramValue)
                    || (isset($property['regexp']) && !preg_match($property['regexp'], $paramValue))
                    || (isset($property['min']) && (strlen($paramValue) < $property['min']))
                    || (isset($property['max']) && (strlen($paramValue) > $property['max']))
                ) {
                    throw new Exception("Error on string check. Param {$property['name']} = {$paramValue}");
                }
            }
        }
        return true;
    }

    /**
     * Creates or updates row in DB.
     *
     * @return Mixed InsertedID or true or false
     */
    public function save() {
        if (!$this->isValid()) {
            return false;
        }
        $primary = $this->propertiesGetPrimaryName();
        if (!isset($this->params[$primary])) {
            $insertedId = $this->create();
            if ($insertedId && $primary === "id") {
                $this->params[$primary] = $insertedId;
            }
            return $insertedId;
        } else {
            return $this->update();
        }
    }

    /**
     * Updates row in DB.
     *
     * @return Mixed DB query result
     */
    public function update() {
        $propsByName = $this->propertiesGetByName();
        $tableName = $this->table;
        $loop = 0;
        $queryNames = ""; $queryValues = "";
        $preparedTypes = array();
        foreach ($this->params as $param=>$value) {
            if ($loop) {
                $queryNames .= ", ";
                $queryValues .= ", ";
            }
            $queryNames .= "`{$param}`";
            $queryValues .= ":{$param}";
            $preparedTypes[] = array(":{$param}", $value, $propsByName[$param]['type']);
            $loop++;
        }
        $query = "REPLACE INTO `{$tableName}` ({$queryNames}) VALUES ({$queryValues})";
        $db = $this->dbConnect()->prepare($query);
        foreach ($preparedTypes as $prepared) {
            $db->bindValue($prepared[0], $prepared[1], $prepared[2]);
        }
        $db->execute();
        return $db->lastInsertId();
    }

    /**
     * Removes from DB.
     *
     * @return bool DB query result
     */
    public function delete() {
        if (!$this->isValid()) {
            return false;
        }
        $tableName = $this->table;
        $primary = $this->propertiesGetPrimaryName();
        if (!isset($this->params[$primary])) {
            return false;
        } else {
            $value = $this->params[$primary];
            $query = "DELETE FROM `{$tableName}` WHERE `{$primary}` = :id LIMIT 1";
            $db = $this->dbConnect()->prepare($query);
            $db->bindValue(':id', $value);
            $db->execute();
            return true;
        }
    }

    /**
     * Saves current model into DB.
     *
     * @return int Last insert id
     */
    public function create() {
        $propsByName = $this->propertiesGetByName();
        $tableName = $this->table;
        $loop = 0;
        $queryNames = ""; $queryValues = "";
        $preparedTypes = array();
        foreach ($this->params as $param=>$value) {
            if ($loop) {
                $queryNames .= ", ";
                $queryValues .= ", ";
            }
            $queryNames .= "`{$param}`";
            $queryValues .= ":{$param}";
            $preparedTypes[] = array(":{$param}", $value, $propsByName[$param]['type']);
            $loop++;
        }
        $query = "INSERT INTO `{$tableName}` ({$queryNames}) VALUES ({$queryValues})";
        $db = $this->dbConnect()->prepare($query);
        foreach ($preparedTypes as $prepared) {
            $db->bindValue($prepared[0], $prepared[1], $prepared[2]);
        }
        $db->execute();
        return $db->lastInsertId();
    }

    /**
     * Load one object from DB.
     *
     * @param String $param Param name
     * @param Mixed $value
     * @return ModelRecord
     */
    public function fetchBy($param, $value) {
        $propsByName = $this->propertiesGetByName();
        $table = $this->table;
        $param = $propsByName[$param]['name'];
        $query = "SELECT * FROM `{$table}` WHERE `{$param}` = :{$param} LIMIT 1";
        $db = $this->dbConnect();
        $db ->prepare($query)
            ->bindValue(":{$param}", $value, $propsByName[$param]['type'])
            ->execute();
        $row = $db->fetch(PDO::FETCH_ASSOC);
        if (!$row) {
            return null;
        }
        foreach ($this->properties as $property) {
            $this->params[$property['name']] = $row[$property['name']];
        }
        return $this;
    }

    /**
     *
     * @param int|false $limit
     * @param String|false $order Order by specified param
     * @param bool $desc Is we should use DESC instead ASC
     *
     * @return Array|false
     */
    public function fetchAll($limit = false, $order = false, $desc = false) {
        $propsByName = $this->propertiesGetByName();
        $table = $this->table;
        $query = "SELECT * FROM `{$table}`";
        if ($order) {
            $order = $propsByName[$order]['name'];
            $query .= " ORDER BY {$order} ";
            if ($desc) {
                $query .= "DESC";
            } else {
                $query .= "ASC";
            }
        }
        if ($limit) {
            $query .= " LIMIT :limit";
        }
        return $this->dbConnect()
            ->prepare($query)
            ->bindValue(":limit", $limit, PDO::PARAM_INT)
            ->execute()
            ->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Gets param by name.
     *
     * @param String $name
     * @return Mixed
     */
    public function __get($name) {
        return $this->params[$name];
    }

    /**
     * @param String $name
     * @return bool
     */
    public function __isset($name) {
        return isset($this->params[$name]);
    }

    /**
     * Sets param by name.
     *
     * @param String $name
     * @param int|String $value
     */
    public function __set($name, $value) {
        $this->params[$name] = $value;
    }

    /**
     * Returns array of properties by name.
     *
     * @return Array
     */
    public function propertiesGetByName() {
        if (!$this->propertiesByNameArray) {
            $this->propertiesCalculateByName();
        }
        return $this->propertiesByNameArray;
    }

    /**
     * We use it for example
     */
    protected function propertiesCalculateByName() {
        $this->propertiesByNameArray = array();
        foreach ($this->properties as $property) {
            $this->propertiesByNameArray[$property['name']] = $property;
        }
    }

    /**
     * Returns name of primary property.
     *
     * @return String|false
     */
    protected function propertiesGetPrimaryName() {
        foreach ($this->properties as $property) {
            if (isset($property['primary']) && $property['primary']) {
                return $property['name'];
            }
        }
        return false;
    }

    /**
     * Returns established DB connection.
     *
     * @return PDOChainer\PDOChainer
     */
    protected function dbConnect() {
        $db = $this->container['db'];
        if (!is_null($this->dbName) && $this->dbName !== $this->container['currentDbName']) {
            $db->query("USE {$this->dbName};");
            $this->container['currentDbName'] = $this->dbName;
        } else if (is_null($this->dbName) && $this->container['currentDbName'] !== $this->container['config']['mysql']['dbname']) {
            $db->query("USE {$this->container['config']['mysql']['dbname']};");
            $this->container['currentDbName'] = $this->container['config']['mysql']['dbname'];
        }
        return $db;
    }

}