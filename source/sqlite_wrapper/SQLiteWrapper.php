<?php

class SQLiteWrapper
{
    public $context;
    protected $pdo;
    protected $table;
    protected $mode = 'r';
    protected $currentRow = 0;

    function stream_open($path, $mode, $options, &$opath)
    {
        $url = parse_url($path);
        if (!isset($url['host']) || !isset($url['path'])) {
            return false;
        }
        $option = stream_context_get_options($this->context);
        $filePath = isset($option['sql']['path']) ? $option['sql']['path'] : '/tmp';
        $filePath .= DIRECTORY_SEPARATOR . $url['host'];
        try {
            $this->pdo = new PDO('sqlite:' . $filePath, null, null,
                $options['sql']['options']);
        } catch (PDOException $e) {
            return false;
        }
        $this->table = trim($url['path'], '/');
        $this->pdo->exec(
            "CREATE TABLE IF NOT EXISTS {$this->table} (`id` INTEGER PRIMARY KEY,`data` TEXT);"
        );
        $this->mode = $mode;
        return true;
    }

    function stream_read($rows)
    {
        $statement = $this->pdo->prepare("SELECT id,data FROM {$this->table} ORDER BY id LIMIT :offset, :len");
        $statement->execute(['offset' => $this->currentRow, 'len' => $rows]);
        $rows = $statement->fetchAll(PDO::FETCH_OBJ);
        $buffer = '';
        foreach($rows as $row){
            $this->currentRow = $row->id;
            $buffer .= $row->data.PHP_EOL;
        }
        return $buffer;
    }

    function stream_write($data)
    {
        if (is_object($data) || is_array($data)) {
            $data = json_encode($data);
        }
        $statement = $this->pdo->prepare("INSERT INTO {$this->table} (data) VALUES (:data)");
        $statement->execute(['data' => $data]);
        return strlen($data);
    }

    function stream_tell()
    {
        return $this->currentRow;
    }

    function stream_eof()
    {
        $stmt = $this->pdo->query("SELECT id FROM {$this->table} WHERE id > :id");
        return !(bool)$stmt->execute(['id' => $this->currentRow]);
    }

    function stream_seek($offset, $step)
    {
        $this->currentRow = $offset;
    }

    function stream_stat()
    {
        return [];
    }
}