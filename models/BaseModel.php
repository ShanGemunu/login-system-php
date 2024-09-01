<?php
namespace app\models;

use app\core\Application;
use app\logs\Log;
use Exception;
use mysqli_stmt;
use app\exceptions\ParameterBindFailedException;
use app\exceptions\PrepareQueryFailedException;
use app\exceptions\QueryExecuteFailedException;
use app\exceptions\LoadInFileFailedException;

class BaseModel
{
    protected $conn;
    protected $table;
    protected $where = [];
    protected $orderBy;
    protected $limit;
    protected $innerJoin;

    protected function __construct()
    {
        $this->conn = Application::$app->db->getDbConnection();
    }

    /** 
     *    set table for current model
     *    @param string $table
     *    @return void
     */
    protected function table(string $table): void
    {
        $this->table = $table;
    }

    /** 
     *    set WHERE clause with AND for current query
     *    @param string $column
     *    @param string $operator
     *    @param int|string $value
     *    @return BaseModel
     */
    protected function whereAnd(string $column, string $operator, int|string $value): BaseModel
    {
        Log::logInfo("executing whereAnd with parameters - $column, $operator, $value at BaseModel");
        $this->where[] = count($this->where) > 0 ? "and $column $operator '$value'" : "$column $operator '$value'";

        return $this;
    }

    /** 
     *    set WHERE clause with OR for current query
     *    @param string $column
     *    @param string $operator
     *    @param int|string $value
     *    @return BaseModel
     */
    protected function whereOr(string $column, string $operator, int|string $value): BaseModel
    {
        Log::logInfo("executing whereOr with parameters - $column, $operator, $value at BaseModel");
        $this->where[] = count($this->where) > 0 ? "or $column $operator '$value'" : "$column $operator '$value'";

        return $this;
    }

    /** 
     *    set ORDER BY clause for current query
     *    @param string $column
     *    @param string $order
     *    @return BaseModel
     */
    protected function orderBy(string $column, string $order = "ASC"): BaseModel
    {
        $this->orderBy = " order by $column $order";

        return $this;
    }

    /** 
     *    set LIMIT clause for current query
     *    @param int $limit
     *    @param int $offset
     *    @return BaseModel
     */
    protected function limit(int $limit = 1000, int $offset = 1): BaseModel
    {
        $this->limit = " limit $limit offset $offset";

        return $this;
    }

    /** 
     *    prepare query
     *    returns a statement object or false if an error occurred.
     *    @param string
     *    @return mysqli_stmt|bool
     */
    private function prepareQuery(string $query): mysqli_stmt|bool
    {
        Log::logInfo("executing prepareQuery with parameters - $query at BaseModel");

        return $this->conn->prepare($query);
    }

    /** 
     *    bind parameters to query
     *    Returns true on success or false on failure.
     *    @param mysqli_stmt $statement 
     *    @param array $parameters 
     *    @return bool  
     */
    private function bindParameters(mysqli_stmt $statement, array $parameters): bool
    {
        // assoiative array
        $types = array_map(function ($param) {
            return $param[1];
        }, $parameters);

        $concatTypes = implode('', $types);

        // assoiative array
        $values = array_map(function ($param) {
            return $param[0];
        }, $parameters);

        // array 
        $values = array_values($values);
        Log::logInfo("executing bindParameters with parameters -  ,  at BaseModel");

        return $statement->bind_param($concatTypes, ...$values);
    }

    /** 
     *    insert records to a table 
     *    @param array $data 
     *    @throws PrepareQueryFailedException
     *    @throws ParameterBindFailedException
     *    @throws QueryExecuteFailedException
     *    @return bool  
     */
    protected function insert(array $data): int
    {
        $paramSymbols = "?" . str_repeat(",?", count($data) - 1);
        $columns = implode(",", array_keys($data));

        $logAndExceptionData = "";
        foreach ($data as $key => $value) {
            $logAndExceptionData .= $key . "-" . implode(',', $value) . " ";
        }

        $query = "INSERT INTO {$this->table} ($columns)
            VALUES ($paramSymbols)";

        $statement = $this->prepareQuery($query);

        if ($statement === false)
            throw new PrepareQueryFailedException("data - $logAndExceptionData", BaseModel::class, "insert");
        Log::logInfo("query prepared successfully at insert of BaseModel, data - $logAndExceptionData");

        $isParametersBind = $this->bindParameters($statement, $data);

        if (!$isParametersBind) {
            throw new ParameterBindFailedException("data - $logAndExceptionData", BaseModel::class, "insert");
        }
        Log::logInfo("parameters bound successfully at insert of BaseModel, data - $logAndExceptionData");

        if ($statement->execute() === false) {
            throw new QueryExecuteFailedException("data - $logAndExceptionData", BaseModel::class, "insert");
        }
        Log::logInfo("query executed successfully at insert of BaseModel, data - $logAndExceptionData");

        return $statement->affected_rows;
    }

    /** 
     *    select records of a table 
     *    @param array $columns, $table 
     *    @throws PrepareQueryFailedException
     *    @throws QueryExecuteFailedException
     *    @return bool
     */
    protected function select(array $columns = ["*"]): array
    {
        $columnsString = implode(",", $columns);
        $query = "SELECT $columnsString FROM {$this->table}";
        $logAndExceptiondData = "{$columnsString}{$this->table}";

        if (!empty($this->where)) {
            $query .= " where " . implode(' ', $this->where);
        }
        if ($this->orderBy) {
            $query .= $this->orderBy;
        }
        if ($this->limit) {
            $query .= $this->limit;
        }
        $statement = $this->prepareQuery($query);

        if ($statement === false) {
            throw new PrepareQueryFailedException("data - $logAndExceptiondData", BaseModel::class, 'select');
        }
        Log::logInfo("query prepared successfully at select of BaseModel, data - $logAndExceptiondData");

        if ($statement->execute() === false) {
            throw new QueryExecuteFailedException("data - logAndExceptiondData", BaseModel::class, 'select');
        }
        Log::logInfo("query executed successfully at select of BaseModel - $logAndExceptiondData");
        $result = $statement->get_result();

        return $result->fetch_all(MYSQLI_ASSOC);
    }

    /** 
     *    update records of a table 
     *    @param array $data
     *    @throws PrepareQueryFailedException
     *    @throws ParameterBindFailedException
     *    @throws QueryExecuteFailedException
     *    @return int
     */
    protected function update(array $data): int
    {
        $set = "";
        $logAndExceptionData = "";
        foreach ($data as $column => $value) {
            $set .= "$column = ? ,";
            $logAndExceptionData .= $column . "-" . implode(", ", $value) . " ";
        }
        $lastPos = strrpos($set, ",");
        $set = substr_replace($set, '', $lastPos, 1);

        $query = "UPDATE {$this->table} SET $set";
        if (!empty($this->where)) {
            $query .= " where " . implode(' ', $this->where);
        }

        $statement = $this->prepareQuery($query);

        if ($statement === false) {
            throw new PrepareQueryFailedException("data - $logAndExceptionData", BaseModel::class, 'update');
        }
        Log::logInfo("query prepared successfully at update of BaseModel, data - $logAndExceptionData");

        $isParametersBind = $this->bindParameters($statement, $data);

        if (!$isParametersBind) {
            throw new ParameterBindFailedException("data - $logAndExceptionData", BaseModel::class, 'update');
        }
        Log::logInfo("parameters bound successfully at update of BaseModel, data - $logAndExceptionData");

        if ($statement->execute() === false) {
            throw new QueryExecuteFailedException("data - $logAndExceptionData", BaseModel::class, 'update');
        }
        Log::logInfo("query executed successfully at update of BaseModel - $logAndExceptionData");

        return $statement->affected_rows;
    }

    /** 
     *    delete records of a table 
     *    @param array 
     *    @throws PrepareQueryFailedException
     *    @throws QueryExecuteFailedException
     *    @return bool
     */
    protected function delete(): int
    {
        $query = "DELETE FROM {$this->table}";
        if (!empty($this->where)) {
            $query .= " where " . implode(' ', $this->where);
        }
        $statement = $this->prepareQuery($query);

        if ($statement === false)
            throw new PrepareQueryFailedException("table - {$this->table}", BaseModel::class, 'delete');
        Log::logInfo("query prepared successfully at delete of BaseModel, table - {$this->table}");

        if ($statement->execute() === false) {
            throw new QueryExecuteFailedException("table - {$this->table}", BaseModel::class, "delete");
        }
        Log::logInfo("query executed successfully at delete of BaseModel, table- {$this->table}");

        return $statement->affected_rows;
    }

    /** 
     *    insert dataset as a infile(csv file) to db 
     *    @param string $table 
     *    @param array $columns 
     *    @param string $fileName
     *    @throws LoadInFileFailedException
     *    @return bool
     */
    function insertInFile(array $columns = [], string $fileName): bool
    {
        $columnsString = implode(",", $columns);

        $query = "
        LOAD DATA INFILE '../../htdocs/login-system-php/files/$fileName' 
        IGNORE INTO TABLE {$_ENV['DB_NAME']}.{$this->table} 
        CHARACTER SET UTF8 
        FIELDS TERMINATED BY ',' 
        LINES TERMINATED BY '\r\n' 
        IGNORE 1 LINES 
        ($columnsString)
        ";

        if (!($this->conn->query($query) === TRUE)) {
            throw new LoadInFileFailedException("table - {$this->table}, columns - $columnsString, file - $fileName", BaseModel::class, "insertInFile");
        }
        Log::logInfo("query executed successfully at insertInFile of BaseModel, columns - $columnsString, file - $fileName");

        return true;
    }

    /** 
     *    select records of table by using AS closure for columns 
     *    @param array $columns  ex: ['column01'=>["column01","asColumn01"],'column02'=>["column02","asColumn02"],...]
     *    @throws PrepareQueryFailedException
     *    @throws QueryExecuteFailedException
     *    @return array  ex:  [[order-detail-01,order-detail-01->product-01],[order-detail-01,order-detail-01->product-02],...]
     */
    function selectAs(array $columns) : array
    {
        $columnString = "";
        foreach ($columns as $value) {
            $columnString .= implode(" as ", $value) . ",";
        }
        $lastPos = strrpos($columnString, ",");
        $columnString = substr_replace($columnString, "", $lastPos, 1);

        $query = "SELECT $columnString FROM {$this->table}";

        if ($this->innerJoin) {
            $query .= $this->innerJoin;
        }
        if (!empty($this->where)) {
            $query .= " where " . implode(' ', $this->where);
        }
        if ($this->orderBy) {
            $query .= $this->orderBy;
        }
        $statement = $this->prepareQuery($query);
        if ($statement === false) {
            throw new PrepareQueryFailedException("data - $query", "BaseModel", 'selectAs');
        }
        Log::logInfo("query prepared successfully at selectAs of BaseModel, query - $query");

        if ($statement->execute() === false) {
            throw new QueryExecuteFailedException("data - logAndExceptiondData", "BaseModel", 'selectAs');
        }
        Log::logInfo("query executed successfully at selectAs of BaseModel, query - $query");
        $result = $statement->get_result();

        return $result->fetch_all(MYSQLI_ASSOC);
    }

    /** 
     *    set inner for innerJoin property
     *    @param array $tablesAndColumns  ex: ['mainTable'=>["mainTableName","mainTableColumnName"],'subTable'=>["subTableName","subTableColumnName"]]
     *    @return void
     */
    function innerJoin(array $tablesAndColumns): void
    {
        Log::logInfo("executing innerJoin with parameters - main table: {$tablesAndColumns['mainTable'][0]} / main table column: {$tablesAndColumns['mainTable'][1]}   ,   sub table: {$tablesAndColumns['subTable'][0]} / sub table column: {$tablesAndColumns['subTable'][1]}  at BaseModel");
        $this->innerJoin .= " INNER JOIN {$tablesAndColumns['mainTable'][0]} ON {$tablesAndColumns['mainTable'][0]}.{$tablesAndColumns['mainTable'][1]} = {$tablesAndColumns['subTable'][0]}.{$tablesAndColumns['subTable'][1]} ";
    }

    

}