<?php

namespace Alekhin\Geo;

class CanadaProvinces {
    
    static function get_provinces(){
        // 2 character province codes reference
        // https://en.wikipedia.org/wiki/Provinces_and_territories_of_Canada
        $provinces = [];
        $provinces['ON'] = 'Ontario';
        $provinces['QC'] = 'Quebec';
        $provinces['NS'] = 'Nova Scotia';
        $provinces['NB'] = 'New Brunswick';
        $provinces['MB'] = 'Manitoba';
        $provinces['BC'] = 'British Columbia';
        $provinces['PE'] = 'Prince Edward Island';
        $provinces['SK'] = 'Saskatchewan';
        $provinces['AB'] = 'Alberta';
        $provinces['NL'] = 'Newfoundland and Labrador';
        natsort($provinces);
        return $provinces;
    }

    static function get_name($code) {
        $c = self::get_provinces();
        if (isset($c[$code])) {
            return $c[$code];
        }
        return '';
    }
}
