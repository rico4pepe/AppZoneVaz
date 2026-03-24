@extends('layouts.front.layout_dashboard')

@section('title', 'FanZone — Feed')
@section('page-title', 'Feed')

@section('content')

{{-- ============================================
     FEED HEADER
============================================ --}}
<div class="fz-feed-header">
    <div>
        <h1 class="fz-feed-title">Fans Feed</h1>
        <p class="fz-feed-sub">Polls, quizzes & match banter</p>
    </div>
</div>

{{-- ============================================
     FEED CONTENT
============================================ --}}
<div class="fz-feed-container">

    {{-- React Feed Mount --}}
    <div id="feed-app"
         data-user="{{ auth()->id() }}"
         data-token="{{ csrf_token() }}">
    </div>

</div>

@endsection

@push('styles')
<style>
    /* ============================================
       FEED HEADER
    ============================================ */
    .fz-feed-header {
        display: flex;
        align-items: center;
        justify-content: space-between;
        margin-bottom: var(--fz-space-lg);
        padding: 0 2px;
    }

    .fz-feed-title {
        font-size: 18px;
        font-weight: 700;
        color: var(--fz-text);
    }

    .fz-feed-sub {
        font-size: 12px;
        color: var(--fz-muted);
        margin-top: 2px;
    }

    /* ============================================
       FEED CONTAINER
    ============================================ */
    .fz-feed-container {
        display: flex;
        flex-direction: column;
        gap: var(--fz-space-md);
    }

    /* ============================================
       MOBILE TIGHTENING
    ============================================ */
    @media (max-width: 480px) {
        .fz-feed-title {
            font-size: 16px;
        }

        .fz-feed-sub {
            font-size: 11px;
        }
    }
</style>
@endpush