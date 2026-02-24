<?php

use Carbon\Carbon;


if (!function_exists('convertYmdToMdy')) {
    function convertYmdToMdy($date)
    {
        if ($date) {
            return Carbon::createFromFormat('Y-m-d H:i:s', $date)->format('F d, Y');
        } else {
            return "";
        }
    }
}
if (!function_exists('convertYmdToMy')) {
    function convertYmdToMy($date)
    {
        if ($date) {
            return Carbon::createFromFormat('Y-m-d H:i:s', $date)->format('M Y');
        } else {
            return "";
        }
    }
}

if (!function_exists('truncate_with_length')) {
    function truncate_with_length($string, $length)
    {
        if (strlen($string) > $length) {
            return substr($string, 0, $length - 3) . '...';
        }
        return $string;
    }
}

if (!function_exists('role_permission_list_format')) {
    function role_permission_list_format($role)
    {
        $response = [];
        if ($role) {

            foreach ($role->permissions as $key => $RolePermissions) {
                $response[] = $RolePermissions->name;
            }

            $commaSeparatedText = implode(', ', $response);

            return $commaSeparatedText;
        }
        return "";
    }
}

