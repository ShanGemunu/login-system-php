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
    protected $subWhere;
    protected $orderBy;
    protected $limit;
    protected $innerJoin;
    protected $leftJoin;
    protected $groupBy;
    protected $subQueryWhere = [];
    protected $subQueryOrderBy;
    protected $subQueryLimit;
    protected $subQueryInnerJoin;
    protected $subQueryLeftJoin;
    protected $subQueryGroupBy;

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
        Log::logInfo("BaseModel", "table", "set value for table", "success", $table);
        $this->table = $table;
        ;
    }

    /** 
     *    set WHERE clause with AND for current query or sub query
     *    @param string $column
     *    @param string $operator
     *    @param int|string $value
     *    @return BaseModel
     */
    protected function whereAnd(string $column, string $operator, int|string $value, bool $isForSubQuery = false): BaseModel
    {
        if ($isForSubQuery) {
            Log::logInfo("BaseModel", "whereAnd", "set WHERE clause with AND for sub query", "success", "column - $column; operator - $operator; value - $value");
            $this->subQueryWhere[] = count($this->subQueryWhere) > 0 ? "AND $column $operator '$value'" : "$column $operator '$value'";
        } else {
            Log::logInfo("BaseModel", "whereAnd", "set WHERE clause with AND", "success", "column - $column; operator - $operator; value - $value");
            $this->where[] = count($this->where) > 0 ? "AND $column $operator '$value'" : "$column $operator '$value'";
        }

        return $this;
    }

    /** 
     *    set WHERE clause with OR for current query or sub query
     *    @param string $column
     *    @param string $operator
     *    @param int|string $value
     *    @return BaseModel
     */
    protected function whereOr(string $column, string $operator, int|string $value, bool $isForSubQuery = false): BaseModel
    {
        if ($isForSubQuery) {
            Log::logInfo("BaseModel", "whereOr", "set WHERE clause with OR for sub query", "success", "column - $column; operator - $operator; value - $value");
            $this->subQueryWhere[] = count($this->subQueryWhere) > 0 ? "OR $column $operator '$value'" : "$column $operator '$value'";
        } else {
            Log::logInfo("BaseModel", "whereOr", "set WHERE clause with OR", "success", "column - $column; operator - $operator; value - $value");
            $this->where[] = count($this->where) > 0 ? "OR $column $operator '$value'" : "$column $operator '$value'";
        }

        return $this;
    }

     /** 
     *    set oparter to subWhere to form sub where statement 
     *    @param string $operator
     *    @return BaseModel
     */
    protected function addSubWhereAnd(string $operator): BaseModel
    {
        $this->subWhere = $operator;

        return $this;
    }

    /** 
     *    set GROUP BY clause for current query or sub query
     *    @param string $column
     *    @param string $operator
     *    @param int|string $value
     *    @return BaseModel
     */
    protected function groupBy(array $columns, bool $isForSubQuery = false): BaseModel
    {
        $groupByString = "GROUP BY " . implode(',', $columns);
        if ($isForSubQuery) {
            Log::logInfo("BaseModel", "groupBy", "set GROUP BY clause for sub query", "success", "group By clause - $groupByString");
            $this->subQueryGroupBy = $groupByString;
        } else {
            Log::logInfo("BaseModel", "groupBy", "set GROUP BY clause for query", "success", "group By clause - $groupByString");
            $this->groupBy = $groupByString;
        }

        return $this;
    }

    /** 
     *    set ORDER BY clause for current query or sub query
     *    @param string $column
     *    @param string $order
     *    @return BaseModel
     */
    protected function orderBy(string $column, string $order = "ASC", bool $isForSubQuery = false): BaseModel
    {
        if ($isForSubQuery) {
            $this->subQueryOrderBy = " order by $column $order";
            Log::logInfo("BaseModel", "orderby", "set ORDER BY clause for sub query", "success", "column - $column; operator - $order");
        } else {
            $this->orderBy = " order by $column $order";
            Log::logInfo("BaseModel", "orderby", "set ORDER BY clause", "success", "column - $column; operator - $order");
        }

        return $this;
    }

    /** 
     *    set LIMIT clause for current query or sub query
     *    @param int $limit
     *    @param int $offset
     *    @return BaseModel
     */
    protected function limit(int $limit = 1000, int $offset = 1, bool $isForSubQuery = false): BaseModel
    {
        if ($isForSubQuery) {
            $this->subQueryLimit = " limit $limit offset $offset";
            Log::logInfo("BaseModel", "limit", "limit clause with offset", "success", "limit - $limit; offset - $offset");
        } else {
            $this->limit = " limit $limit offset $offset";
            Log::logInfo("BaseModel", "limit", "limit clause with offset", "success", "limit - $limit; offset - $offset");
        }

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
        Log::logInfo("BaseModel", "prepareQuery", "preparing query provided", "pending", "query - $query");

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
        array_map(function ($column, $value, $type) use (&$log_data) {
            $log_data .= "column - $column; value - $value; type - $type/ ";
        }, $columns, $values, $types);
        Log::logInfo("BaseModel", "bindParameters", "binding parameters for prepared query", "success", $log_data);

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

        Log::logInfo("BaseModel", "insert", "starting function", "success", "query - $query");

        $statement = $this->prepareQuery($query);

        if ($statement === false)
            throw new PrepareQueryFailedException("data - $logAndExceptionData", BaseModel::class, "insert");
        Log::logInfo("BaseModel", "insert", "query prepared successfully", "success", "query - $query");

        $isParametersBind = $this->bindParameters($statement, $data);

        if (!$isParametersBind)
            throw new ParameterBindFailedException("data - $logAndExceptionData", BaseModel::class, "insert");
        Log::logInfo("BaseModel", "insert", "parameters bound successfully", "success", "query - $query");

        if ($statement->execute() === false) {
            throw new QueryExecuteFailedException("data - $logAndExceptionData", BaseModel::class, "insert");
        }
        Log::logInfo("BaseModel", "insert", "query executed successfully", "success", "query - $query");

        return $statement->affected_rows;
    }

    /** 
     *    select records of a table 
     *    @param array $columns, $case 
     *    @throws PrepareQueryFailedException
     *    @throws QueryExecuteFailedException
     *    @return array
     */
    protected function select(array $columns = [], string $case = "", string $subQuery = ""): array
    {
        $columnsString = "";
        foreach ($columns as $value) {
            if ($value[1] === null) {
                $columnsString .= "{$value[0]} ,";
            } else {
                $columnsString .= implode(" as ", $value) . ",";
            }
        }
        if (count($columns) === 0)
            $columnsString = "* ,";
        if ($case === "") {
            $lastPos = strrpos($columnsString, ",");
            $columnsString = substr_replace($columnsString, "", $lastPos, 1);
        }

        $query = "SELECT $columnsString $case FROM ";
        if ($subQuery !== "") {
            $query .= "($subQuery) AS subquery ";
            $logAndExceptionData = "selected columns - {$columnsString}; table - {$this->table}; sub query - $subQuery";
        } else {
            $query .= "{$this->table}";
            $logAndExceptionData = "selected columns - {$columnsString}; table - {$this->table}";
        }

        if ($this->innerJoin) {
            $query .= $this->innerJoin;
        }
        if ($this->leftJoin) {
            $query .= $this->leftJoin;
        }
        if (!empty($this->where)) {
            $where = " where " . implode(' ', $this->where);
            if($this->subWhere){
                $where = str_replace($this->subWhere, "{$this->subWhere}(", $where).")";
            }
            $query .= $where;
        }
        if ($this->groupBy) {
            $query .= $this->groupBy;
        }
        if ($this->orderBy) {
            $query .= $this->orderBy;
        }
        if ($this->limit) {
            $query .= $this->limit;
        }
        Log::logInfo("BaseModel", "select", "starting function", "success", "query - $query");
        $statement = $this->prepareQuery($query);

        if ($statement === false) {
            throw new PrepareQueryFailedException("data - $logAndExceptionData", BaseModel::class, 'select');
        }
        Log::logInfo("BaseModel", "select", "query prepared successfully", "success", "query - $query");

        if ($statement->execute() === false) {
            throw new QueryExecuteFailedException("data - $logAndExceptionData", BaseModel::class, 'select');
        }
        Log::logInfo("BaseModel", "select", "query executed successfully", "success", "query - $query");
        $result = $statement->get_result();

        return $result->fetch_all(MYSQLI_ASSOC);
    }

    /** 
     *    subquery for select  
     *    @param array $columns, $case 
     *    @throws PrepareQueryFailedException
     *    @throws QueryExecuteFailedException
     *    @return string
     */
    protected function selectSubQuery(array $columns = [], string $case = ""): string
    {
        $columnsString = "";
        foreach ($columns as $value) {
            if ($value[1] === null) {
                $columnsString .= "{$value[0]} ,";
            } else {
                $columnsString .= implode(" as ", $value) . ",";
            }
        }
        if (count($columns) === 0)
            $columnsString = "* ,";
        if ($case === "") {
            $lastPos = strrpos($columnsString, ",");
            $columnsString = substr_replace($columnsString, "", $lastPos, 1);
        }

        $query = "SELECT $columnsString $case FROM {$this->table}";

        if ($this->subQueryInnerJoin) {
            $query .= $this->subQueryInnerJoin;
        }
        if ($this->subQueryLeftJoin) {
            $query .= $this->subQueryLeftJoin;
        }
        if (!empty($this->subQueryWhere)) {
            $query .= " where " . implode(' ', $this->subQueryWhere);
        }
        if ($this->subQueryOrderBy) {
            $query .= $this->subQueryOrderBy;
        }
        if ($this->subQueryLimit) {
            $query .= $this->subQueryLimit;
        }
        Log::logInfo("BaseModel", "selectSubQuery", "sub query prepaired", "success", "columns to be selected - {$columnsString}; table - {$this->table}; query - $query");

        return $query;
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

        Log::logInfo("BaseModel", "update", "starting function", "success", "query - $query");

        $statement = $this->prepareQuery($query);

        if ($statement === false) {
            throw new PrepareQueryFailedException("data - $logAndExceptionData", BaseModel::class, 'update');
        }
        Log::logInfo("BaseModel", "update", "query prepared successfully", "success", "query - $query");

        $isParametersBind = $this->bindParameters($statement, $data);

        if (!$isParametersBind) {
            throw new ParameterBindFailedException("data - $logAndExceptionData", BaseModel::class, 'update');
        }
        Log::logInfo("BaseModel", "update", "parameters bound successfully", "success", "query - $query");

        if ($statement->execute() === false) {
            throw new QueryExecuteFailedException("data - $logAndExceptionData", BaseModel::class, 'update');
        }
        Log::logInfo("BaseModel", "update", "query ececuted successfully", "success", "query - $query");

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
        Log::logInfo("BaseModel", "delete", "starting fucntion", "success", "query - $query");

        $statement = $this->prepareQuery($query);

        if ($statement === false)
            throw new PrepareQueryFailedException("table - {$this->table}", BaseModel::class, 'delete');
        Log::logInfo("BaseModel", "delete", "prepare query successfully", "success", "query - $query");

        if ($statement->execute() === false) {
            throw new QueryExecuteFailedException("table - {$this->table}", BaseModel::class, "delete");
        }
        Log::logInfo("BaseModel", "delete", "query executed successfully", "success", "query - $query");

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
    function insertInFile(array $columns = [], string $fileExtension, string $charSet = "UTF8", string $fieldsTerminater = ",", string $linesTerminater = "\r\n", string $linesIgnore = "1"): bool
    {
        $columnsString = implode(",", $columns);
        $dbName = Application::$dbName;

        $query = "
        LOAD DATA INFILE '../../htdocs/login-system-php/files/temp-files/temp_file.$fileExtension' 
        IGNORE INTO TABLE {$dbName}.{$this->table} 
        CHARACTER SET $charSet 
        FIELDS TERMINATED BY '$fieldsTerminater' 
        LINES TERMINATED BY '$linesTerminater' 
        IGNORE $linesIgnore LINES 
        ($columnsString)
        ";
        Log::logInfo("BaseModel", "insertInFile", "starting function", "success", "query - $query");

        if (!($this->conn->query($query) === TRUE)) {
            throw new LoadInFileFailedException("table - {$this->table}, columns - $columnsString, file - $fileExtension", BaseModel::class, "insertInFile");
        }
        Log::logInfo("BaseModel", "insertInFile", "query executed successfully", "success", "query - $query");

        return true;
    }

    /** 
     *    set tables and columns for innerJoin property for current query or sub query
     *    @param array $tablesAndColumns  ex: ['joinFromTable'=>["joinFromTableName","joinFromTableColumnName"],'joinToTable'=>["joinToTableName","joinToTableColumnName"]]
     *    @return void
     */
    function innerJoin(array $tablesAndColumns, bool $isForSubQuery = false): void
    {
        if ($isForSubQuery) {
            Log::logInfo("BaseModel", "innerJoin", "set INNER JOIN clauses for sub query", "success", "join from table: {$tablesAndColumns['joinFromTable'][0]} ; main table column: {$tablesAndColumns['joinFromTable'][1]};      join to table: {$tablesAndColumns['joinToTable'][0]} ; sub table column: {$tablesAndColumns['joinToTable'][1]}");
            $this->subQueryInnerJoin .= " INNER JOIN {$tablesAndColumns['joinFromTable'][0]} ON {$tablesAndColumns['joinFromTable'][0]}.{$tablesAndColumns['joinFromTable'][1]} = {$tablesAndColumns['joinToTable'][0]}.{$tablesAndColumns['joinToTable'][1]} ";
        } else {
            Log::logInfo("BaseModel", "innerJoin", "set INNER JOIN clauses", "success", "join from table: {$tablesAndColumns['joinFromTable'][0]} ; main table column: {$tablesAndColumns['joinFromTable'][1]};      join to table: {$tablesAndColumns['joinToTable'][0]} ; sub table column: {$tablesAndColumns['joinToTable'][1]}");
            $this->innerJoin .= " INNER JOIN {$tablesAndColumns['joinFromTable'][0]} ON {$tablesAndColumns['joinFromTable'][0]}.{$tablesAndColumns['joinFromTable'][1]} = {$tablesAndColumns['joinToTable'][0]}.{$tablesAndColumns['joinToTable'][1]} ";
        }

    }

    /** 
     *    set tables and columns for leftJoin property for current query or sub query
     *    @param array $tablesAndColumns  ex: ['joinFromTable'=>["joinFromTableName","joinFromTableColumnName"],'joinToTable'=>["joinToTableName","joinToTableColumnName"]]
     *    @return void
     */
    function leftJoin(array $tablesAndColumns, bool $isForSubQuery = false): void
    {
        if ($isForSubQuery) {
            Log::logInfo("BaseModel", "leftJoin", "set LEFT JOIN clauses for sub query", "success", "main table: {$tablesAndColumns['joinFromTable'][0]} ; main table column: {$tablesAndColumns['joinFromTable'][1]};      sub table: {$tablesAndColumns['joinToTable'][0]} ; sub table column: {$tablesAndColumns['joinToTable'][1]}");
            $this->subQueryLeftJoin .= " LEFT JOIN {$tablesAndColumns['joinFromTable'][0]} ON {$tablesAndColumns['joinFromTable'][0]}.{$tablesAndColumns['joinFromTable'][1]} = {$tablesAndColumns['joinToTable'][0]}.{$tablesAndColumns['joinToTable'][1]} ";
        } else {
            Log::logInfo("BaseModel", "leftJoin", "set LEFT JOIN clauses", "success", "main table: {$tablesAndColumns['joinFromTable'][0]} ; main table column: {$tablesAndColumns['joinFromTable'][1]};      sub table: {$tablesAndColumns['joinToTable'][0]} ; sub table column: {$tablesAndColumns['joinToTable'][1]}");
            $this->leftJoin .= " LEFT JOIN {$tablesAndColumns['joinFromTable'][0]} ON {$tablesAndColumns['joinFromTable'][0]}.{$tablesAndColumns['joinFromTable'][1]} = {$tablesAndColumns['joinToTable'][0]}.{$tablesAndColumns['joinToTable'][1]} ";
        }

    }



}