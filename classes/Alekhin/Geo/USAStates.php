<?php

namespace Alekhin\Geo;

class USAStates {

    static function get_states() {
        // 2 character state codes reference
        // https://en.wikipedia.org/wiki/List_of_U.S._state_abbreviations
        $states = [];
        $states['AL'] = 'Alabama';
        $states['AK'] = 'Alaska';
        $states['AZ'] = 'Arizona';
        $states['AR'] = 'Arkansas';
        $states['CA'] = 'California';
        $states['CO'] = 'Colorado';
        $states['CT'] = 'Connecticut';
        $states['DE'] = 'Delaware';
        $states['DC'] = 'District of Columbia';
        $states['FL'] = 'Florida';
        $states['GA'] = 'Georgia';
        $states['HI'] = 'Hawaii';
        $states['ID'] = 'Idaho';
        $states['IL'] = 'Illinois';
        $states['IN'] = 'Indiana';
        $states['IA'] = 'Iowa';
        $states['KS'] = 'Kansas';
        $states['KY'] = 'Kentucky';
        $states['LA'] = 'Louisiana';
        $states['ME'] = 'Maine';
        $states['MD'] = 'Maryland';
        $states['MA'] = 'Massachusetts';
        $states['MI'] = 'Michigan';
        $states['MN'] = 'Minnesota';
        $states['MS'] = 'Mississippi';
        $states['MO'] = 'Missouri';
        $states['MT'] = 'Montana';
        $states['NE'] = 'Nebraska';
        $states['NV'] = 'Nevada';
        $states['NH'] = 'New Hampshire';
        $states['NJ'] = 'New Jersey';
        $states['NM'] = 'New Mexico';
        $states['NY'] = 'New York';
        $states['NC'] = 'North Carolina';
        $states['ND'] = 'North Dakota';
        $states['OH'] = 'Ohio';
        $states['OK'] = 'Oklahoma';
        $states['OR'] = 'Oregon';
        $states['PA'] = 'Pennsylvania';
        $states['RI'] = 'Rhode Island';
        $states['SC'] = 'South Carolina';
        $states['SD'] = 'South Dakota';
        $states['TN'] = 'Tennessee';
        $states['TX'] = 'Texas';
        $states['UT'] = 'Utah';
        $states['VT'] = 'Vermont';
        $states['VA'] = 'Virginia';
        $states['WA'] = 'Washington';
        $states['WV'] = 'West Virginia';
        $states['WI'] = 'Wisconsin';
        $states['WY'] = 'Wyoming';
        $states['AS'] = 'American Samoa';
        $states['GU'] = 'Guam';
        $states['MP'] = 'Northern Mariana Islands';
        $states['PR'] = 'Puerto Rico';
        $states['VI'] = 'U.S. Virgin Islands';
        $states['UM'] = 'U.S. Minor Outlying Islands';
        $states['FM'] = 'Micronesia';
        $states['MH'] = 'Marshall Islands';
        $states['PW'] = 'Palau';
        natsort($states);
        return $states;
    }

    static function get_name($code) {
        $c = self::get_states();
        if (isset($c[$code])) {
            return $c[$code];
        }
        return '';
    }

}
