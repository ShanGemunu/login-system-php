<?php
namespace app\models;

use app\core\Application;
use app\core\Log;
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
        Log::logInfo("BaseModel","table","set value for table","success",$table);
        $this->table = $table;;
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
        Log::logInfo("BaseModel","whereAnd","set WHERE clause with AND","success","column - $column; operator - $operator; value - $value");
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
        Log::logInfo("BaseModel","whereOr","set WHERE clause with OR","success","column - $column; operator - $operator; value - $value");
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
        Log::logInfo("BaseModel","orderby","set ORDER BY clause","success","column - $column; operator - $order");

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
        Log::logInfo("BaseModel","limit","limit clause with offset","success","limit - $limit; offset - $offset");

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
        Log::logInfo("BaseModel","prepareQuery","prepare query provided","success","query - $query");

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
        $columns = array_keys($parameters);
        $log_data = "";
        array_map(function ($column, $value, $type) use (&$log_data){
            $log_data .= "column - $column; value - $value; type - $type/ ";
        }, $columns, $values, $types);
        Log::logInfo("BaseModel","bindParameters","binding parameters for prepared query","success",$log_data);

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

        Log::logInfo("BaseModel","insert","starting function","success","query - $query");

        $statement = $this->prepareQuery($query);

        if ($statement === false)
            throw new PrepareQueryFailedException("data - $logAndExceptionData", BaseModel::class, "insert");
        Log::logInfo("BaseModel","insert","query prepared successfully","success","query - $query");

        $isParametersBind = $this->bindParameters($statement, $data);

        if (!$isParametersBind) 
            throw new ParameterBindFailedException("data - $logAndExceptionData", BaseModel::class, "insert");
        Log::logInfo("BaseModel","insert","parameters bound successfully","success","query - $query");

        if ($statement->execute() === false) {
            throw new QueryExecuteFailedException("data - $logAndExceptionData", BaseModel::class, "insert");
        }
        Log::logInfo("BaseModel","insert","query executed successfully","success","query - $query");

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
        Log::logInfo("BaseModel","select","starting function","success","query - $query");
        $statement = $this->prepareQuery($query);

        if ($statement === false) {
            throw new PrepareQueryFailedException("data - $logAndExceptiondData", BaseModel::class, 'select');
        }
        Log::logInfo("BaseModel","select","query prepared successfully","success","query - $query");

        if ($statement->execute() === false) {
            throw new QueryExecuteFailedException("data - logAndExceptiondData", BaseModel::class, 'select');
        }
        Log::logInfo("BaseModel","select","query executed successfully","success","query - $query");
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

        Log::logInfo("BaseModel","update","starting function","success","query - $query");

        $statement = $this->prepareQuery($query);

        if ($statement === false) {
            throw new PrepareQueryFailedException("data - $logAndExceptionData", BaseModel::class, 'update');
        }
        Log::logInfo("BaseModel","update","query prepared successfully","success","query - $query");

        $isParametersBind = $this->bindParameters($statement, $data);

        if (!$isParametersBind) {
            throw new ParameterBindFailedException("data - $logAndExceptionData", BaseModel::class, 'update');
        }
        Log::logInfo("BaseModel","update","parameters bound successfully","success","query - $query");

        if ($statement->execute() === false) {
            throw new QueryExecuteFailedException("data - $logAndExceptionData", BaseModel::class, 'update');
        }
        Log::logInfo("BaseModel","update","query ececuted successfully","success","query - $query");

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
        Log::logInfo("BaseModel","delete","starting fucntion","success","query - $query");

        $statement = $this->prepareQuery($query);

        if ($statement === false)
            throw new PrepareQueryFailedException("table - {$this->table}", BaseModel::class, 'delete');
        Log::logInfo("BaseModel","delete","prepare query successfully","success","query - $query");

        if ($statement->execute() === false) {
            throw new QueryExecuteFailedException("table - {$this->table}", BaseModel::class, "delete");
        }
        Log::logInfo("BaseModel","delete","query executed successfully","success","query - $query");

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
        Log::logInfo("BaseModel","insertInFile","starting function","success","query - $query");

        if (!($this->conn->query($query) === TRUE)) {
            throw new LoadInFileFailedException("table - {$this->table}, columns - $columnsString, file - $fileName", BaseModel::class, "insertInFile");
        }
        Log::logInfo("BaseModel","insertInFile","query executed successfully","success","query - $query");

        return true;
    }

    /** 
     *    select records of table by using AS closure for columns 
     *    @param array $columns  ex: ['column01'=>["column01","asColumn01"],'column02'=>["column02","asColumn02"],...]
     *    @throws PrepareQueryFailedException
     *    @throws QueryExecuteFailedException
     *    @return array  ex:  [[order-detail-01,order-detail-01->product-01],[order-detail-01,order-detail-01->product-02],...]
     */
    function selectAs(array $columns): array
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
        Log::logInfo("BaseModel","selectAs","starting function","success","query - $query");
        $statement = $this->prepareQuery($query);
        if ($statement === false) {
            throw new PrepareQueryFailedException("data - $query", "BaseModel", 'selectAs');
        }
        Log::logInfo("BaseModel","selectAs","prepare query successfully","success","query - $query");

        if ($statement->execute() === false) {
            throw new QueryExecuteFailedException("data - logAndExceptiondData", "BaseModel", 'selectAs');
        }
        Log::logInfo("BaseModel","selectAs","execute query successfully","success","query - $query");
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
        Log::logInfo("BaseModel","innerJoin","set INNER JOIN clases", "success", "main table: {$tablesAndColumns['mainTable'][0]} ; main table column: {$tablesAndColumns['mainTable'][1]};      sub table: {$tablesAndColumns['subTable'][0]} ; sub table column: {$tablesAndColumns['subTable'][1]}");
        $this->innerJoin .= " INNER JOIN {$tablesAndColumns['mainTable'][0]} ON {$tablesAndColumns['mainTable'][0]}.{$tablesAndColumns['mainTable'][1]} = {$tablesAndColumns['subTable'][0]}.{$tablesAndColumns['subTable'][1]} ";
    }



}