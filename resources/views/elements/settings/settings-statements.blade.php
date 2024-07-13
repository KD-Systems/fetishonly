<div class="">
    <div class="table-wrapper ">
        <div class="">
            <div class="col d-flex align-items-center py-3 border-bottom text-bold">
                <div class="col-lg-2 text-truncate">From</div>
                <div class="col-lg-3 text-truncate">Date/Time</div>
                <div class="col-lg-2 text-truncate">Amount</div>
                <div class="col-lg-1 text-truncate">Fee</div>
                <div class="col-lg-1 text-truncate">Net</div>
                <div class="col-lg-3 text-truncate">Type</div>
            </div>
            @foreach ($statements as $item)
                <div class="col d-flex align-items-center py-3 border-bottom">
                    <div class="col-lg-2 text-truncate">
                        <a href="{{route('profile',['username'=>$item->receiver->username])}}" class="text-dark-r">
                            {{$item->receiver->name}}
                        </a>
                    </div>
                    <div class="col-lg-3 text-truncate">
                        <p class="p-0 m-0">{{ date('M d, Y', strtotime($item->created_at)) }}</p>
                        <small>{{ date('h:m:s A', strtotime($item->created_at)) }}</small>
                    </div>

                    <div class="col-lg-2 text-center">
                        ${{ $item->amount }}
                    </div>
                    <div class="col-lg-1 text-truncate">${{ $item->fee_amount }}</div>
                    <div class="col-lg-1 text-truncate">
                        ${{ $item->amount - $item->fee_amount }}
                    </div>
                    <div class="col-lg-3">
                        {{ $item->type }}
                    </div>
                </div>
            @endforeach
        </div>
    </div>
    <div class="d-flex flex-row-reverse mt-3 mr-4">
        {{ $statements->onEachSide(1)->links() }}
    </div>
</div>
