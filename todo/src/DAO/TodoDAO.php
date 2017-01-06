<?php

namespace Drupal\todo\DAO;

use Drupal\Core\Database\Connection;

class TodoDAO
{
    private $connection;

    public function __construct(Connection $connection) {
        $this->connection = $connection;
    }

    public function getAll()
    {
        $databaseResource = $this->query('SELECT * FROM todo');
        return $databaseResource->fetchAll();
    }

    public function get($id)
    {
        return $this->query('SELECT * FROM todo WHERE id = '.$id)->fetch();
    }

    public function create($title)
    {
        if(empty($title)) {
            throw new \InvalidArgumentException('The title should not be empty.');
        }

        return $this->query('INSERT INTO todo (title) VALUES(\''.$title.'\');');
    }

    public function close($id)
    {
        return $this->query('UPDATE todo SET is_done = 1 WHERE id = '.$id);
    }

    public function delete($id)
    {
        return $this->query('DELETE FROM todo WHERE id = '.$id);
    }


    private function query($query)
    {
//        $this->connection = $this->connectToDatabase();
//        var_dump($query); die();
        $result = $this->connection->query($query);
//        $result = mysqli_query($this->connection, $query);

        if($result === false) {
            throw new \RuntimeException('MySQL query is not valid.');
        }

        return $result;
    }

    private function connectToDatabase()
    {
        if (!$conn = mysqli_connect('localhost', 'root', 'toor')) {
            die('Unable to connect to MySQL : '.mysqli_errno($conn).' '.mysqli_error($conn));
        }

        mysqli_select_db($conn, 'training_todo') or die('Unable to select database "training_todo"');

        return $conn;
    }
}