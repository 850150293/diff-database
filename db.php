<?php

/**
 * Created by PhpStorm.
 * User: hxp
 * Date: 16/3/23
 * Time: 上午9:21
 */
class Db
{
    public $link = '';

    /**
     * @param array $dbArray
     * @return mysqli|string
     */
    public function contect($dbArray = [])
    {
        if ($this->link) {
            list($host, $user, $password, $database, $port) = $dbArray;
            return mysqli_connect($host, $user, $password, $database, $port);
        } else {
            return $this->link;
        }
    }

    /**
     * @param $link
     * @param $sql
     * @return bool|mysqli_results
     */
    public function query($link, $sql)
    {
        $result = mysqli_query($link, $sql);
        return $result;
    }

    /**
     * @param array $dbArray
     * @return array
     *
     */
    public function getTables($dbArray = [])
    {

        // 创建连接
        $conn = mysqli_connect($dbArray['host'], $dbArray['user'], $dbArray['password'], $dbArray['database'], $dbArray['port']);
        // 检测连接
        if (!$conn) {
            die("Connection failed: " . mysqli_connect_error());
        }

        $sql = "SHOW TABLES";
        $tables = [];
        if ($rescolumns = mysqli_query($conn, $sql)) {
            while ($row = mysqli_fetch_assoc($rescolumns)) {
                $tables['db'][] = $row['Tables_in_' . $dbArray['database']];
            }
        } else {
            echo "Error creating database: " . mysqli_error($conn);
        }
//        sort($tables['db']);

        return $tables;
        mysqli_close($conn);
    }

    /**
     * @param array $dbArray
     * @param string $tables
     * @return array
     */
    public function getTableRow($dbArray = [], $tables = '')
    {
        // 创建连接
        $conn2 = mysqli_connect($dbArray['host'], $dbArray['user'], $dbArray['password'], $dbArray['database'], $dbArray['port']);
        // 检测连接
        if (!$conn2) {
            die("Connection failed: " . mysqli_connect_error());
        }

        $sql = "SHOW FULL COLUMNS FROM " . $tables . ';';
        $fields = [];
        if ($rescolumns = mysqli_query($conn2, $sql)) {
            while ($row = mysqli_fetch_assoc($rescolumns)) {
                $fields[$row['Field']] = $row;
            }
        } else {
            return [];
        }
//        sort($fields['field']);
        return $fields;
        mysqli_close($conn2);
    }

    /**
     * 比对两个数组的不同
     * @param array $array1
     * @param array $array2
     * @return array
     *
     */
    public function getDiffArray($array1, $array2)
    {
        $result = [];
        if (is_array($array1) && is_array($array2)) {
            foreach ($array1 as $key => $value) {
                if (!in_array($value, $array2)) {
                    $result[] = 'db1_'.$value;
                }
            }

            foreach ($array2 as $key => $value) {
                if (!in_array($value, $array1)) {
                    $result[] = 'db2_'.$value;
                }
            }
        }
        return $result;
    }

    /**
     * 获取相同的数组
     * @param $array1
     * @param $array2
     * @return array
     */
    public function getCommonArray($array1, $array2)
    {
        $result = [];
        foreach ($array1 as $value) {
            if (in_array($value, $array2)) {
                $result[] = $value;
            }
        }
        return $result;
    }

    /**
     * 比较两个字段的不同
     * @param $field_ary1
     * @param $field_ary2
     * @return array
     */
    public function getDiffField($field_ary1, $field_ary2)
    {
        $result = array();

        foreach ($field_ary1 as $k => $ary) {
            $str = '';
            if (isset($field_ary2[$k])) {
                $ary2 = $field_ary2[$k];
            } else {
                $str .= 'db2-unset';
                $result[$k] = $str;
                continue;
            }
            if ($ary['Type'] != $ary2['Type']) {
                $str .= $ary['Type'] . '-' . $ary2['Type'] . '|';
                $result[$k] = $str;
            }

            if ($ary['Null'] != $ary2['Null']) {
                if (isset($result[$k])) {
                    $str = $result[$k];
                    unset($result[$k]);
                }
                $str .= $ary['Null'] . '-' . $ary2['Null'] . '|';
                $result[$k] = $str;
            }

            if ($ary['Key'] != $ary2['Key']) {
                if (isset($result[$k])) {
                    $str = $result[$k];
                    unset($result[$k]);
                }
                $str .= $ary['Key'] . '-' . $ary2['Key'] . '|';
                $result[$k] = $str;
            }
        }

        foreach ($field_ary2 as $k => $ary) {
            $str = '';
            if (!isset($field_ary1[$k])) {
                $str .= 'db1-unset';
                $result[$k] = $str;
            } else {
                continue;
            }
        }
        return $result;
    }

    //去除sk_id==null的子数组
	function test($array)
	{
		$result = array();
		foreach ($array as $key => $val) {
			$flag = false;
			foreach ($val as $k => $v) {
				if ($v['sk_id'] != null) {
					$flag = true;
				}
			}
			if ($flag) {
				$result[] = $val;
			}
		}
		return $result;
	}
}