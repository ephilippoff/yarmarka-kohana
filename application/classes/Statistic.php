<?php defined('SYSPATH') or die('No direct script access.');

class Statistic {

    private static $format = array(
        'day' => 'YYYY-MM-DD',
        'month' => 'YYYY-MM',
        'week' => 'YYYY-WW',
        'year' => 'YYYY',
    );

    private static function query_for_city_count($group_by, $interval) {

        return DB::select( 
            DB::expr( sprintf("to_char(%s,'%s') as period", 'date_created', self::$format[ $interval ] ) ),
            DB::expr('city_id'),
            DB::expr( " count(id) as count" )
        );
    }

    private static function query_for_category_count($group_by, $interval) {

        return DB::select( 
            DB::expr( sprintf("to_char(%s,'%s') as period", 'date_created', self::$format[ $interval ] ) ),
            DB::expr('category'),
            DB::expr( " count(id) as count" )
        );
    }

    private static function query_for_count($group_by, $interval) {

        return DB::select( 
            DB::expr( sprintf("to_char(%s,'%s') as period", $group_by, self::$format[ $interval ] ) ),
            DB::expr( " count(id) as count" )
        );
    }

     private static function query_for_sum($group_by, $sum_field, $interval) {

        return DB::select( 
            DB::expr( sprintf("to_char(%s,'%s') as period", $group_by, self::$format[ $interval ] ) ),
            DB::expr( sprintf(" sum(%s) as count", $sum_field) )
        );
    }

    private static function result($query) {
        return DB::select('*')->from(array($query,'p') )->order_by('p.period','desc')->execute();
    }

    private static function get_city_count($from, $group_by, $interval = 'day', $filters) 
    {
        $query = self::query_for_city_count($group_by, $interval)->from($from)->group_by(DB::expr('1'), DB::expr('2'));
        
        foreach ($filters as  $filter) {
            $query = $query->where($filter[0], $filter[1], $filter[2]);
        }

        $_result = self::result($query);

        $result = array();

        foreach ($_result as $row) {
            array_push($result, array(
                    'city_id'=> $row['city_id'],
                    'period'=> $row['period'], 
                    'count' => intval($row['count'])
                )
            );
        }

        return $result;
    }

     private static function get_category_count($from, $group_by, $interval = 'day', $filters) 
    {
        $query = self::query_for_category_count($group_by, $interval)->from($from)->group_by(DB::expr('1'), DB::expr('2'));
        
        foreach ($filters as  $filter) {
            $query = $query->where($filter[0], $filter[1], $filter[2]);
        }

        $_result = self::result($query);

        $result = array();

        foreach ($_result as $row) {
            array_push($result, array(
                    'category'=> $row['category'],
                    'period'=> $row['period'], 
                    'count' => intval($row['count'])
                )
            );
        }

        return $result;
    }

    private static function get_count($from, $group_by, $interval = 'day', $filters) 
    {
        $query = self::query_for_count($group_by, $interval)->from($from)->group_by(DB::expr('1'));
        
        foreach ($filters as  $filter) {
            $query = $query->where($filter[0], $filter[1], $filter[2]);
        }

        $_result = self::result($query);

        $result = array();

        foreach ($_result as $row) {
            array_push($result, array(
                    'period'=> $row['period'], 
                    'count' => intval($row['count'])
                )
            );
        }

        return $result;
    }

    private static function get_sum($from, $group_by, $sum_field, $interval = 'day', $filters) 
    {
        $query = self::query_for_sum($group_by, $sum_field, $interval)->from($from)->group_by(DB::expr('1'));
        
        foreach ($filters as  $filter) {
            $query = $query->where($filter[0], $filter[1], $filter[2]);
        }

        $_result = self::result($query);

        $result = array();

        foreach ($_result as $row) {
            array_push($result, array(
                    'period'=> $row['period'], 
                    'count' => intval($row['count'])
                )
            );
        }

        return $result;
    }

    public static function get_new_objects($interval, $filters = array()) {
        
        return self::get_count('object', 'date_created', $interval, $filters);

    }

    public static function get_new_objects_category($interval, $filters = array()) {
        
        return self::get_category_count('object', 'category, date_created', $interval, $filters);

    }

     public static function get_new_objects_city($interval, $filters = array()) {
        
        return self::get_city_count('object', 'city, date_created', $interval, $filters);

    }

    public static function get_new_user($interval, $filters = array()) {
        return self::get_count('user', 'regdate', $interval, $filters);
    }

    public static function get_sent_emails($interval, $filters = array()) {
        return self::get_count('email', 'created_on', $interval, $filters);
    }

    public static function get_sent_sms($interval, $filters = array()) {
        return self::get_count('sms', 'created_on', $interval, $filters);
    }

    public static function get_orders($interval, $filters = array()) {
        return self::get_sum('orders', 'payment_date', 'sum', $interval, $filters);
    }

    public static function get_subscriptions($interval, $filters = array()) {
        return self::get_count('subscription_surgut', 'created', $interval, $filters);
    }

    
}