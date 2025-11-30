<?php

namespace App\Helpers;

class VCardHelper
{
    public static function rumLaut($value)
    {
        return $value ?? '';
    }

    public static function hideEmail($email)
    {
        if (!$email || !str_contains($email, '@')) {
            return $email;
        }

        return str_replace('@', ' [at] ', $email);
    }


    public static function has($value)
    {
        return !empty(trim((string)$value));
    }

    /**
     * Baut die komplette VCard als HTML-Sammelstring
     */
    public static function buildVCard(array $data, array $lang = [])
    {
        $html = '<div class="vcard"><address class="mt-2"><div class="subheader">';

        // Name
        $html .= '<b>' . self::rumLaut($data['name'] ?? '') . '</b><br />';

        // Adresse
        $html .= '<span class="adr">'
            . self::rumLaut($data['strasse'] ?? '') . '<br />'
            . '<span class="postalCode">' . self::rumLaut($data['plz'] ?? '') . '</span>&nbsp;'
            . '<span class="locality">' . self::rumLaut($data['ort'] ?? '') . '</span>'
            . '</span>';

        // Telefon
        if (self::has($data['festnetz'] ?? null)) {
            $label = $lang['phone'] ?? 'Telefon';
            $html .= "<div>{$label}: <a href=\"tel:{$data['festnetz']}\">{$data['festnetz']}</a></div>";
        }

        // Mobil
        if (self::has($data['mobil'] ?? null)) {
            $label = $lang['mobil'] ?? 'Mobil';
            $html .= "<div>{$label}: <a href=\"tel:{$data['mobil']}\">{$data['mobil']}</a></div>";
        }

        // Fax
        if (self::has($data['fax'] ?? null)) {
            $label = $lang['fax'] ?? 'Fax';
            $html .= "<div>{$label}: {$data['fax']}</div>";
        }

        // Email
        if (self::has($data['email'] ?? null)) {
            $label = $lang['email'] ?? 'E-Mail';
            $hidden = self::hideEmail($data['email']);
            $html .= "<div>{$label}: <a href=\"mailto:{$data['email']}\">{$hidden}</a></div>";
        }

        $html .= '</div></address></div>';

        return $html;
    }
}
