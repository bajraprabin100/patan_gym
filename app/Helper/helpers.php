<?php
if (!function_exists('importCsv')) {
    /**
     * Gravatar URL from Email address.
     *
     * @param string $email Email address
     * @param string $size Size in pixels
     * @param string $default Default image [ 404 | mm | identicon | monsterid | wavatar ]
     * @param string $rating Max rating [ g | pg | r | x ]
     *
     * @return string
     */
    function importCsv($data, $table)
    {

        if (!empty($data) && $data->count()) {
            foreach ($data as $key => $value) {
                $value_ar = $value->toArray();
                foreach ($value_ar as $i => $v) {
                    if ($v == null) {
                        $value_ar[$i] = '';
                    }
                }
                DB::table($table)
                    ->insert($value_ar);
            }

        }
        return true;
    }
}
if (!function_exists('companyName')) {
    function companyName($attribute)
    {
        $company=\App\Models\Admin\BranchPara::select('branch_company_name')->where('branch_code','=',$attribute)->first();
        return strtoupper($company->branch_company_name) ;
    }
}
if (!function_exists('masterCompanyName')) {
    function masterCompanyName($shipper_code)
    {
        $company=\App\Models\Admin\CustomerPara::where('customer_para.shipper_code','=',$shipper_code)
                                                 ->join('company_informations as c','c.company_code','=','customer_para.company_code')->first();

        return isset($company->company_name)?strtoupper($company->company_name):'' ;
    }
}
if(!function_exists('companyAddress')){
    function companyAddress($attribute){
        $company=\App\Models\Admin\BranchPara::select('address')->where('branch_code','=',$attribute)->first();
        return $company->address;
    }
}
if(!function_exists('getLocationName')){
    function getLocationName($location_code){
        $company=\App\Models\Admin\LocationHierarachy::where('location_code','=',$location_code)->first();
        return isset($company->location_name)?$company->location_name:'';
    }
}
if(!function_exists('english_to_nepali')){
    function english_to_nepali($date)
    {
        $cal = new \App\Services\NepaliDateConvert\NepaliCalender();
        return $cal->eng_to_nep($date);
    }
}
if(!function_exists('getRouteName')){
    function getRouteName($route_code,$branch_code)
    {
        $route=\App\Models\Admin\RoutePara::where('route_code','=',$route_code)->where('branch_code','=',$branch_code)->first();
       return isset($route->route_name)?$route->route_name:'';
    }
}
if(!function_exists('getMailingMode')){
    function getMailingMode($zone_code,$branch_code)
    {
        $zone=\App\Models\Admin\ZoneDetail::where('zone_code','=',$zone_code)->where('branch_code','=',$branch_code)->first();
        return $zone->mailing_mode;
    }
}
function doxS($location_code,$customer_code){
  $price=\App\Models\Admin\CustomerPriceDetail::where('location_code','=',$location_code)
                   ->where('customer_code','=',$customer_code)
      ->where('merchandise_type','=','DOX')
      ->where('mailing_mode','=','S')
      ->first();
  return isset($price->rate)?$price->rate:'';
}
function doxA($location_code,$customer_code){
    $price=\App\Models\Admin\CustomerPriceDetail::where('location_code','=',$location_code)
        ->where('customer_code','=',$customer_code)
        ->where('merchandise_type','=','DOX')
        ->where('mailing_mode','=','A')
        ->first();
    return isset($price->rate)?$price->rate:'';
}
function ndxA($location_code,$customer_code){
    $price=\App\Models\Admin\CustomerPriceDetail::where('location_code','=',$location_code)
        ->where('customer_code','=',$customer_code)
        ->where('merchandise_type','=','NDx')
        ->where('mailing_mode','=','A')
        ->first();
    return isset($price->rate)?$price->rate:'';
}
function ndxS($location_code,$customer_code){
    $price=\App\Models\Admin\CustomerPriceDetail::where('location_code','=',$location_code)
        ->where('customer_code','=',$customer_code)
        ->where('merchandise_type','=','NDx')
        ->where('mailing_mode','=','S')
        ->first();
    return isset($price->rate)?$price->rate:'';
}

function number_to_word( $num = '' )
{

    $num    = ( string ) ( ( int ) $num );

    if( ( int ) ( $num ) && ctype_digit( $num ) )
    {
        $words  = array( );

        $num    = str_replace( array( ',' , ' ' ) , '' , trim( $num ) );

        $list1  = array('','one','two','three','four','five','six','seven',
            'eight','nine','ten','eleven','twelve','thirteen','fourteen',
            'fifteen','sixteen','seventeen','eighteen','nineteen');

        $list2  = array('','ten','twenty','thirty','forty','fifty','sixty',
            'seventy','eighty','ninety','hundred');

        $list3  = array('','thousand','million','billion','trillion',
            'quadrillion','quintillion','sextillion','septillion',
            'octillion','nonillion','decillion','undecillion',
            'duodecillion','tredecillion','quattuordecillion',
            'quindecillion','sexdecillion','septendecillion',
            'octodecillion','novemdecillion','vigintillion');

        $num_length = strlen( $num );
        $levels = ( int ) ( ( $num_length + 2 ) / 3 );
        $max_length = $levels * 3;
        $num    = substr( '00'.$num , -$max_length );
        $num_levels = str_split( $num , 3 );

        foreach( $num_levels as $num_part )
        {
            $levels--;
            $hundreds   = ( int ) ( $num_part / 100 );
            $hundreds   = ( $hundreds ? ' ' . $list1[$hundreds] . ' Hundred' . ( $hundreds == 1 ? '' : 's' ) . ' ' : '' );
            $tens       = ( int ) ( $num_part % 100 );
            $singles    = '';

            if( $tens < 20 )
            {
                $tens   = ( $tens ? ' ' . $list1[$tens] . ' ' : '' );
            }
            else
            {
                $tens   = ( int ) ( $tens / 10 );
                $tens   = ' ' . $list2[$tens] . ' ';
                $singles    = ( int ) ( $num_part % 10 );
                $singles    = ' ' . $list1[$singles] . ' ';
            }
            $words[]    = $hundreds . $tens . $singles . ( ( $levels && ( int ) ( $num_part ) ) ? ' ' . $list3[$levels] . ' ' : '' );
        }

        $commas = count( $words );

        if( $commas > 1 )
        {
            $commas = $commas - 1;
        }

        $words  = implode( ', ' , $words );

        //Some Finishing Touch
        //Replacing multiples of spaces with one space
        $words  = trim( str_replace( ' ,' , ',' , trim( ucwords( $words ) ) ) , ', ' );
        if( $commas )
        {
            $words  = str_replace_last( ',' , ' and' , $words );
        }

        return $words;
    }
    else if( ! ( ( int ) $num ) )
    {
        return 'Zero';
    }
    return '';
}