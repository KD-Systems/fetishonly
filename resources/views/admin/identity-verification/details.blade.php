@extends('voyager::master')

@section('page_title', "Identity Verification Logs")


@section('content')
    <div class="page-content read container-fluid">
        <div class="row">
            <div class="col-md-12">

                <div class="panel panel-bordered" style="padding-bottom:5px;">
                    <!-- form start -->

                    <table class="table">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>status</th>
                                <th>Verfication Status</th>
                                <th style="width: 300px">Response</th>
                                <th>Reason</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($details as $item)
                            <tr>
                                <td>{{ $item->id }}</td>
                                <td>{{ $item->status }}</td>
                                <td>{{ $item->verification_status }}</td>
                                <td>{{ $item->response }}</td>
                                <td>{{ $item->reason }}</td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>

                </div>
            </div>
        </div>
    </div>
@stop
