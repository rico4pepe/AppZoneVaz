@extends('layouts.front.layout_dashboard')

@section('content')
<div class="container-fluid">
    <div class="row">
        {{-- Sidebar --}}
        @include('partials.sidebar')

        <main class="col-md-9 ms-sm-auto col-lg-10 px-md-4">
            {{-- Navbar/Header --}}
            @include('partials.navbar')

               <div class="row mt-4">
                <div class="col-12">
                    <h4 class="mb-4">🗳️ All Quiz</h4>

                    {{-- React Poll App Mount Point --}}
                    <div id="quiz-app"
                         data-user="{{ auth()->id() }}"
                         data-token="{{ csrf_token() }}">
                    </div>
                </div>
            </div>
            


            

            {{-- Footer --}}
            @include('partials.footer')
        </main>
    </div>
</div>

@if (auth()->check() && (is_null(auth()->user()->name) || (auth()->user()->team_id === 0)))
    <script>
   
        document.addEventListener('DOMContentLoaded', function () {
            let modal = new bootstrap.Modal(document.getElementById('onboardingModal'));
            modal.show();
        }); 
    </script>
@endif
@endsection
