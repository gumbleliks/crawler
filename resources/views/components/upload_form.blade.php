<div class="container mt-5 w-80">

    <div class="d-flex justify-content-around">
        <form action="{{route('import')}}" method="post" enctype="multipart/form-data">
            <h3 class="text-center mb-5">Scraping Tool</h3>
            @csrf
            @if ($message = Session::get('success'))
                <div class="alert alert-success">
                    <strong>{{ $message }}</strong>
                </div>
            @endif
            @if (count($errors) > 0)
                <div class="alert alert-danger">
                    <ul>
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif


            <div class="card">
                <div class="card-body">
                    <div class="card-title"></div>
                    <div class="card-text m-3">
                        <div class="custom-file">
                            <input type="file" name="file" class="form-control" id="chooseFile" accept=".csv">
                        </div>
                    </div>
                    <div class="card-text ms-3 mt-5">
                        <label class="" for="free_search">Free Search: </label>
                        <input class="" type="text" name="free_search" id="free_search"/>
                    </div>
                    <div class="card-text ms-3 mb-4 mt-2">
                        <label class="" for="fixed_search">Fixed Search:</label>
                        <select name="fixed_search" id="fixed_search"
                                class="btn btn-secondary">
                            <option value="">Search for</option>
                            <option value="{{App\Enums\SearchTermEnum::HOTLINE}}">Hotline 0800</option>
                            <option value="{{App\Enums\SearchTermEnum::UST_ID_NR}}">USt-IdNr. komplette EU "DE324043322"
                            </option>
                            <option value="{{App\Enums\SearchTermEnum::HANDELS_REG}}">HandelsReg. Nr. "HRB174903"</option>
                            <option value="{{App\Enums\SearchTermEnum::STEUER_NR}}">Steuernummer "47/735/01705"</option>
                            <option value="{{App\Enums\SearchTermEnum::RECHTSFORM}}">Rechtsformen "e.V., GmbH, Ltd."
                            </option>
                            <option value="{{App\Enums\SearchTermEnum::KLEINUNTERNEHMEN}}">Kleinunternehmer § 19 Abs. 1 UStG</option>
                        </select>

                    </div>
                    <div class="card-text mb-4 ms-3 mt-5">
                        <div class="form-check">
                            <input class="form-check-input" type="radio" name="which_page" id="allPages"
                                   value="all_pages">
                            <label class="form-check-label" for="allPages">
                                Search in All Pages
                            </label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="radio" name="which_page" id="impressum"
                                   value="impressum" checked>
                            <label class="form-check-label" for="impressum">
                                Only search in "impressum" Page
                            </label>
                        </div>
                    </div>
                    <div class="card-footer text-center">
                        <button type="submit" name="submit" class="btn btn-primary btn-block m-4 ">
                            Let's Go
                        </button>
                    </div>

                </div>
            </div>


            {{--            <div class="mt-3">--}}
            {{--                <label class="m-3" for="csv_filter"></label><select name="csv_filter" id="csv_filter"--}}
            {{--                                                                    class="btn btn-secondary m-3" disabled>--}}
            {{--                    <option value="">Choose CSV-Filter</option>--}}
            {{--                    <option value="de">nur .de Domains</option>--}}
            {{--                    <option value="com">nur .com Domains</option>--}}
            {{--                    <option value="net">nur .net Domains</option>--}}
            {{--                    <option value="at">nur .at Domains</option>--}}
            {{--                </select>--}}
            {{--            </div>--}}

            <div class="">

            </div>
        </form>
    </div>

</div>
