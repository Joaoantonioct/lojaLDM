@extends('layouts.main')

@section('title', 'produto')

@section('conteudo')

<p>PAGINA PRODUTO</p>

@if($busca == '')
    <p>Nenhum produto encontrado</p>
@else
    <p>Produtos encontrados: {{ $busca }}</p>
@endif


@endsection