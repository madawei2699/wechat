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

/**
 * 给定查询条件获取地区信息
 * 如果预期返回的数据只有一条，则请求方需要使用下标0定位
 * $data = region(array('level'=>1));
 * @param array $where 查询条件，键名=>键值
 * @param string $order 排序条件，字串
 */
function region(array $where, $order='id ASC') {
	$order = trim($order) == '' ? 'id ASC' : $order;
	$region = D('CommonRegion');
	return $region->where($where)->order($order)->select();
}

/**
 * 给定查询条件返回手机归属地信息
 * 如果预期返回的数据只有一条，则请求方需要使用下标0定位
 * $data = mobile(array('code'=>'021'));
 * @param array $where 查询条件，键名=>键值
 * @param string $order 排序条件，字串
 */
function mobile(array $where, $order='id ASC') {
	$order = trim($order) == '' ? 'id ASC' : $order;
	$mobile = D('CommonMobile');
	return $mobile->where($where)->order($order)->select();
}