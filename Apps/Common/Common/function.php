<?php
/**
 * 模块间共享函数集合
 */

/**
 * 跟踪记录自定义日志到数据库
 * @param array $params 入参集合，字段名对应
 * @return integer|boolean
 */
 function tt(array $params) {
	if (count($params) == 0) return false;
	$model = M();
	$fields = array_keys($params);
	$values = array_values($params);
	foreach ($values as $i=>$value) $values[$i] = '\''.$value.'\'';
	$sql  = 'INSERT INTO control_trace(create_time,stamp,';
	$sql .= implode(',', $fields).') VALUES(now(),unix_timestamp(),';
	$sql .= implode(',', $values).') ';
	return $model->execute($sql);
};
