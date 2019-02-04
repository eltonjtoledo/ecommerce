<?php
function formatVlPrice(float $vlPrice){
    return number_format($vlPrice,2,',','.');
}