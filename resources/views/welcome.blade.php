@extends('layouts.app')
@section('content')
    <h1>Vue</h1>
    <ex></ex>
    <h1>Redirect</h1>
    <div class="jumbotron central-block">
        <form method="POST" action="/search">
            @csrf
            <input type="text" name="inn" v-mask="'#### ######'" v-model="companyInn" placeholder="7731 456781">
            <button type="submit" class="btn-primary">Найти</button>
        </form>
    </div>
@endsection
@section('footer')
    <script>
        export default{

            data () {
                return {companyInn: { Type: String, default: null }};
            }

        }
    </script>
@endsection
