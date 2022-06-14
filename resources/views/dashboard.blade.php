@extends("layouts.master")
@section("title","dashboard")
@section("content_c")

    <div class="space" style="height: 70px"></div>
    <section class="container">
        <h4>name: {{\Illuminate\Support\Facades\Auth::user()->name}}</h4>
        @if(! count( $data ))
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
                @php($i = 1)
            @foreach($data as $item)




                <tbody>
                <tr>
                    <th scope="row">{{$i++}}</th>
                    <td>{{$item->domain->url}}</td>
                    <td>{{$item->url}}</td>
                    <td>
                        <form method="post" action="{{route("url.destroy",["id"=>$item->id ])}}">
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