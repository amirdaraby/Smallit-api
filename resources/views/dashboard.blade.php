@extends("layouts.master")
@section("title","dashboard")
@section("content_c")
    <div class="space" style="height: 70px"></div>
    <section class="container">
        @if(! count( $data->url))
            <h1>empty</h1>
        @else
            <h1>{{$data->url}}</h1>
        @endif

            <a href="{{route("url.create")}}" class="btn btn-info">Create new url</a>
    </section>


@endsection