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
     * @param DiscountCouponOption $coupon
     *
     * @return bool | null
     *
     */
    public function checkForExclusions($coupon)
    {
        $countryCode = CountryPrice_EcommerceCountry::get_real_country();
        if ($countryCode) {
            $countryCode = $countryCode->Code;
            $includedCountries = $coupon->IncludedCountries();
            $excludedCountries = $coupon->ExcludedCountries();

            //first situation: no country information => ALLOW => return NULL to ignore this.
            if ($includedCountries->count() == 0 && $excludedCountries->count() == 0) {
                $this->owner->DebugString .= '--- no country rules apply ---';
                return null;
            }

            //second situation: includes and excludes
            if ($includedCountries->count() > 0 && $excludedCountries->count() > 0) {
                $this->owner->DebugString .= '--- inclusions and exclusions apply - checking for '.$countryCode.' ---';
                $returnFlag = true;

                $includeArray = $includedCountries->column('Code');
                if (in_array($countryCode, $includeArray)) {
                    $returnFlag = null;
                }

                $excludeArray = $excludedCountries->column('Code');
                if (in_array($countryCode, $excludeArray)) {
                    $returnFlag = true;
                }

                return $returnFlag;
            }

            //third situation: is it included?
            if ($includedCountries->count() > 0) {
                $this->owner->DebugString .= '--- inclusions apply - checking for '.$countryCode.' ---';
                $includeArray = $includedCountries->column('Code');
                if (in_array($countryCode, $includeArray)) {
                    return null;
                } else {
                    return true;
                }
            }

            //fourth situation: is it excluded?
            if ($excludedCountries->count() > 0) {
                $this->owner->DebugString .= '--- exclusions apply - checking for '.$countryCode.' ---';
                $excludeArray = $excludedCountries->column('Code');
                if (in_array($countryCode, $excludeArray)) {
                    return true;
                } else {
                    //return true;
                }
            }
        } else {
            $this->owner->DebugString .= '--- no country code could be found ---';
        }

        return null;
    }
}
