<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

class DiscountCouponCountriesModifierExtension extends DataExtension 
{

    /**
     * returns TRUE if the country
     * is not in the included list or if it is in the excluded list
     * in all other cases it returns null to basically ignore the country specific
     * rules
     * 
     * @return bool
     * 
     */
    function checkForExclusions($coupon)
    {
        $countryCode = null;
        if($order = $this->owner->Order()) {
            if($order->OriginatingCountryCode){
               $countryCode = $order->OriginatingCountryCode;
            }
        }
        if(! $countryCode) {
            $countryCode = CountryPrice_EcommerceCountry::get_real_country();
        }
        
        
        if($countryCode) {

            $includedCountries = $coupon->IncludedCountries();
            $excludedCountries = $coupon->ExcludedCountries();        
            
            //first situation: no country information => ALLOW => return NULL to ignore this.
            if($includedCountries->count() == 0 && $excludedCountries->count() == 0){
                return null;
            }

            //second situation: includes and excludes
            if($includedCountries->count() > 0 && $excludedCountries->count() > 0){
                $returnFlag = true;
                
                $includeArray = $includedCountries->column('Code');
                if(in_array($countryCode, $includeArray)){
                    $returnFlag = null;
                }
                
                $excludeArray = $excludedCountries->column('Code');
                if(in_array($countryCode, $excludeArray)) {
                    $returnFlag = true;
                }
                
                return $returnFlag;
                      
            }

            //third situation: is it included?
            if($includedCountries->count() > 0){
                $includeArray = $includedCountries->column('Code');
                if(in_array($countryCode, $includeArray)) {
                    return null;
                }
                else {
                    return true;
                }
            }

            //fourth situation: is it excluded?
            if($excludedCountries->count() > 0){
                $excludeArray = $excludedCountries->column('Code');
                if(in_array($countryCode, $excludeArray)) {
                    return true;
                }
                else {
                    //return true;
                }
            }
        }
        return null;
    }    
}