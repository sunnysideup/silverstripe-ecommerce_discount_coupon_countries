<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

class DiscountCouponOptionCountriesExtension extends DataExtension
{

    private static $casting = array(
        "CountryDescription" => "Varchar"
    );

    private static $summary_fields = array(
        "CountryDescription" => "CountryDescription"
    );


    private static $field_labels = array(
        "CountryDescription" => "Where"
    );


    private static $many_many = array(
        "IncludedCountries" => "EcommerceCountry",
        "ExcludedCountries" => "EcommerceCountry"
    );

//    private static $belongs_many_many = array(
//
//    );



    function getCountryDescription()
    {
        $returnString = '';
        $includedCountries = $this->owner->IncludedCountries();
        $excludedCountries = $this->owner->ExcludedCountries();
        //first situation: no country information => ALLOW => return NULL to ignore this.
        if($includedCountries->count() == 0 && $excludedCountries->count() == 0){
            return _t('DiscountCouponOptionCountriesExtension.AVAILABLE_IN_ALL_COUNTRIES', 'Available in all countries');
        }

        //second situation: includes and excludes
        if($includedCountries->count() > 0 && $excludedCountries->count() > 0){
            $includeArray = $includedCountries->column('Code');
            $excludeArray = $excludedCountries->column('Code');
            foreach($includeArray as $key => $countryCode) {
                if(in_array($countryCode, $excludeArray)) {
                    unset($includeArray[$key]);
                }
            }
            return  _t('DiscountCouponOptionCountriesExtension.AVAILABLE_IN', 'Available in: ').implode(', ', $includeArray).
                    _t('DiscountCouponOptionCountriesExtension.SPECIFICALLY_EXCLUDED', ' || Specifically excluded: ').implode(', ', $excludeArray);
            //...
        }

        //third situation: is it included?
        if($includedCountries->count() > 0){
            $includeArray = $includedCountries->column('Code');
            return _t('DiscountCouponOptionCountriesExtension.AVAILABLE_IN', 'Available in: ').implode(', ', $includeArray);
        }

        //fourth situation: is it excluded?
        if($excludedCountries->count() > 0){
            $excludeArray = $excludedCountries->column('Code');
            return _t('DiscountCouponOptionCountriesExtension.NOT_AVAILABLE_IN', 'Not available in: ').implode(', ', $excludeArray);
        }
    }
}
