<!DOCTYPE html>
@php
    $exceptions = [];
@endphp

<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    @include('schematics::head')
    @include('schematics::body')
</html>
