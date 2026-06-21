@extends('layouts.app')

@section('title', $page->seo_title ?? $page->title)
@section('description', $page->seo_description ?? '')
@section('keywords', $page->seo_keywords ?? '')

@section('content')
    @foreach ($renderedSections as $rendered)
        <div @if($rendered->anchorId) id="{{ $rendered->anchorId }}" @endif @if($rendered->wrapperClass) class="{{ $rendered->wrapperClass }}" @endif>
            {!! $rendered->html !!}
        </div>
    @endforeach
@endsection
