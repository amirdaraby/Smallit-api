@extends("layouts.master")
@section("content")
    <div class="space" style="height: 50px"></div>
    <form action="{{route("url.store")}}" method="post">
        @csrf
        @method("post")
        <label for="url">Url</label>
        <input type="url" class="form-control" placeholder="https://google.com/" name="url">
        <button class="btn btn-dark" type="submit">Create link</button>
    </form>
@endsection