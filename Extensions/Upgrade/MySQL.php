<?php


namespace App\Extensions\Upgrade;

use Simflex\Core\Container;

/**
 * Class MySQL
 * @package Simflex\Core\DB
 * @deprecated rewrite to PDO
 */
class MySQL
{

    /**
     *
     * @var mysqli
     */
    private $link;

    public function connect($host, $user, $pass, $db)
    {
        $this->link = mysqli_connect($host, $user, $pass) or die("<b>Error! Can not connect to MySQL.</b>");
        mysqli_select_db($this->link, $db) or die("<b>Error! Can not select database.</b>");
        mysqli_query($this->link, "SET names utf8");
        mysqli_query($this->link, "SET time_zone = '" . date('P') . "'");
    }

    public function bind($vars)
    {
        $q = 'SET';
        foreach ($vars as $key => $val) {
            $q .= ' @' . $key . '=' . (is_numeric($val) ? $val : "'" . mysqli_escape_string($this->link, $val) . "'") . ',';
        }
        return mysqli_query($this->link, substr($q, 0, -1));
    }

    public function query($q)
    {
        $_ENV['lastq'] = $q;
        return mysqli_query($this->link, $q);
    }

    public function fetch(&$result)
    {
        return mysqli_fetch_assoc($result);
    }

    public function seek(&$r, $index)
    {
        return @mysqli_data_seek($r, $index);
    }

    public function result($r, $field = '')
    {
        if (is_int($field)) {
            $result = mysqli_fetch_row($r);
            return $result[$field] ? $result[$field] : false;
        }
        $result = mysqli_fetch_assoc($r);
        return $field ? (isset($result[$field]) ? $result[$field] : false) : $result;
    }

    public function assoc(&$r, $field1 = false, $field2 = false, $q = FALSE)
    {
        $rows = array();
        if ($field1) {
            if ($field2 === false) {
//                if(!$r){
//                    echo $q;die;
//                }
                while ($row = mysqli_fetch_assoc($r)) {
                    $rows[$row[$field1]] = $row;
                }
            } elseif ($field2) {
                while ($row = mysqli_fetch_assoc($r)) {
                    $rows[$row[$field1]][$row[$field2]] = $row;
                }
            } else {
                while ($row = mysqli_fetch_assoc($r)) {
                    $rows[$row[$field1]][] = $row;
                }
            }
        } else {
            if ($r instanceof \mysqli_result) {
                while($row = mysqli_fetch_assoc($r)) {
                    $rows[] = $row;
                }
            } else {

            }
        }
        return $rows;
    }

    public function insertID()
    {
        $ret = mysqli_fetch_row($this->query("SELECT LAST_INSERT_ID()"));
        return $ret[0];
    }

    public function errno()
    {
        return mysqli_errno($this->link);
    }

    public function error()
    {
        $errs = array(
            1451 => 'Нельзя удалить запись, имеются связанные записи'
        );
        $n = mysqli_errno();
        return $n > 0 ? 'Ошибка. Код: ' . $n . '. ' . (isset($errs[$n]) ? $errs[$n] : mysql_error()) : '';
    }

    public function escape($str)
    {
        return mysqli_escape_string($this->link, $str);
    }

    public function affectedRows()
    {
        return mysqli_affected_rows($this->link);
    }

}

