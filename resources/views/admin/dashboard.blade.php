@extends('layouts.admin.main_layout')

@section('content')
    <livewire:content-manager :id="request('id')"/>
@endsection
