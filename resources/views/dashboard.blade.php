@extends("layouts.master")
@section("title","dashboard")
@section("content_c")

    <div class="space" style="height: 70px"></div>
    <section class="container">
        <h4>name: {{$data->->name}}</h4>
        @if(! count( $url_data ))
            <h1>empty</h1>
        @else

                <h2>Links</h2>
            <table class="table">
                <thead>
                <tr>
                    <th scope="col">No.</th>
                    <th scope="col">Original Url</th>
                    <th scope="col">Short Url</th>
                    <th scope="col">Delete</th>
                </tr>
                </thead>
            @foreach($url_data->shorturl as $url)

@php($i = 1)


                <tbody>
                <tr>
                    <th scope="row">{{$i++}}</th>
                    <td>{{$url->url}}</td>
                    <td>{{$url->short_url}}</td>
                    <td>
                        <form method="post" action="{{route("url.destroy",["id"=>$url->id ])}}">
                            @method("delete")
                            @csrf

                            <button class="btn btn-danger">Delete</button>
                        </form></td>
                </tr>
                </tbody>

                @endforeach
            </table>
        @endif

            <a href="{{route("url.create")}}" class="btn btn-info">Create new url</a>
    </section>


@endsection