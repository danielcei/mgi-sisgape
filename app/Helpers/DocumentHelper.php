<?php
function checkDocument($document): string
{
    $document = preg_replace('/\D/', '', $document);

    if (strlen($document) == 11) {
        return "F";

    }
    if (strlen($document) == 14) {
        return "J";
    }

    return false;
}
