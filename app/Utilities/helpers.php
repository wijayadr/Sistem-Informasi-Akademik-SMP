<?php

if(!function_exists('date_indo')) {
    function date_indo($date) {
        return \Carbon\Carbon::parse($date)->locale('id_ID')->isoFormat('Do MMMM YYYY');
    }
}
