<div class="container d-flex justify-content-lg-start">
    <div class="row m-2">
        <a href="{{ url()->previous() }}">Go Back</a>
    </div>
</div>

<div class="container-fluid w-75">
    <div class="row">

        <div class="col-6 p-3">
            <div class="pb-3">
                <div class="card">
                    <div class="card-header text-center">
                        Download "Keyword-Found-Urls" as a .csv File:
                    </div>
                    <div class="card-body text-center">
                        <a href="{{route('getFile', ['fileName'=>'found_url.csv'])}}" class="btn btn-success">Export CSV</a>
                    </div>
                </div>
            </div>
            <div class="card">
                <div class="card-header">
                    Keyword Found:
                </div>
                <div class="card-body">
                    <table class="table table-striped">
                        <thead>
                        <tr>
                            <th>URL</th>
                            <th>Found Keyword</th>
                            <th>Message</th>
                            <th>Status</th>
                        </tr>
                        </thead>
                        <tbody>
                        @foreach (Session::all() as $key => $subArray)
                            {{$color = $keyWord = ""}}
                            @if($subArray['found'])
                                @php
                                    $color = "#66FF00";
                                    $keyWord = $subArray['filter'];
                                @endphp
                                <tr style="background-color:{{$color}}">
                                    <td><a href="{{url($subArray['url'])}}" target="_blank">{{ $subArray['url']}}</a>
                                    </td>
                                    <td>{{ $keyWord }}</td>
                                    <td>{{ $subArray['message'] }}</td>
                                    <td>{{ $subArray['http_code'] }}</td>
                                </tr>
                            @endif
                        @endforeach


                        </tbody>
                    </table>
                </div>

            </div>
        </div>

        <div class="col-6 p-3">
            <div class="pb-3">
                <div class="card">
                    <div class="card-header text-center">
                        Download "Keyword-Not-Found-Urls" as a .csv File:
                    </div>
                    <div class="card-body text-center">
                        @php $fileName = "not_found_url.csv" @endphp
                        <a href="{{route('getFile', ['fileName'=>'not_found_url.csv'])}}" class="btn btn-success">Export CSV</a>
                    </div>
                </div>
            </div>
            <div class="card">
                <div class="card-header">
                    Keyword Not Found:
                </div>
                <div class="card-body">
                    <table class="table table-striped">
                        <thead>
                        <tr>
                            <th>URL</th>
                            <th>Found Keyword</th>
                            <th>Message</th>
                            <th>Status</th>
                        </tr>
                        </thead>
                        <tbody>
                        @foreach (Session::all() as $key => $subArray)

                            @if(!$subArray['found'])
                                @php
                                    $keyWord = $subArray['filter'];
                                @endphp
                                <tr>
                                    <td><a href="{{url($subArray['url'])}}" target="_blank">{{ $subArray['url']}}</a>
                                    </td>
                                    <td>{{ $keyWord }}</td>
                                    <td>{{ $subArray['message'] }}</td>
                                    <td>{{ $subArray['http_code'] }}</td>
                                </tr>
                            @endif
                        @endforeach


                        </tbody>
                    </table>
                </div>

            </div>
        </div>

    </div>
</div>
