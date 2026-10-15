<?php

namespace App\Helpers;

use libphonenumber\NumberParseException;
use libphonenumber\PhoneNumberFormat;
use libphonenumber\PhoneNumberUtil;

class PhoneHelper
{
    /**
     * Combine a dial code (e.g. "+234") and a number as typed by the user
     * (e.g. "0706 753 1320") into E.164 ("+2347067531320"). Null if invalid.
     */
    public static function toE164(string $dialCode, string $number): ?string
    {
        $util = PhoneNumberUtil::getInstance();
        $region = $util->getRegionCodeForCountryCode((int) ltrim($dialCode, '+'));

        try {
            $parsed = $util->parse($number, $region);
        } catch (NumberParseException $e) {
            return null;
        }

        // Reject numbers typed with a different "+code" than the one selected
        if ($parsed->getCountryCode() !== (int) ltrim($dialCode, '+') || !$util->isValidNumber($parsed)) {
            return null;
        }

        return $util->format($parsed, PhoneNumberFormat::E164);
    }

    /**
     * National significant number (no trunk "0"), e.g. "7067531320".
     */
    public static function nationalNumber(string $e164): string
    {
        $util = PhoneNumberUtil::getInstance();

        return $util->getNationalSignificantNumber($util->parse($e164));
    }

    /**
     * "+2347067531320" -> "+234 ••• ••• 1320"
     */
    public static function mask(string $e164): string
    {
        $util = PhoneNumberUtil::getInstance();
        $parsed = $util->parse($e164);

        return '+' . $parsed->getCountryCode() . ' ••• ••• ' . substr($e164, -4);
    }
}
