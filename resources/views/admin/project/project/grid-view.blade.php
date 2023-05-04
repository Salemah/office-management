@extends('layouts.dashboard.app')

@section('title', 'Project')
@push('script')
<script src="https://cdn.jsdelivr.net/gh/haltu/muuri@0.9.5/dist/muuri.min.js"></script>

<script src="https://cdnjs.cloudflare.com/ajax/libs/web-animations/2.3.2/web-animations.min.js"> </script>
@endpush
@push('css')
    <link rel="stylesheet" href="{{asset('css/project-kanban.css')}}">
@endpush

@section('breadcrumb')
    <nav aria-label="breadcrumb" class="d-flex align-items-center justify-content-between" style="width: 100%">
        <ol class="breadcrumb my-0 ms-2">
            <li class="breadcrumb-item">
                <a href="{{ route('admin.dashboard') }}">Dashboard</a>
            </li>
            <li class="breadcrumb-item">
                <a href="#">Project</a>
            </li>
        </ol>
        <div class="">
            <a class="btn btn-sm btn-primary text-white" title="Grid View" href="{{ route('admin.project.grid.view') }}">
                <i class='bx bx-grid-alt'></i>
            </a>
            <a class="btn btn-sm btn-warning text-white" title="List View" href="{{ route('admin.projects.index') }}">
                <i class='bx bx-list-ul' ></i>
            </a>
            <a class="btn btn-sm btn-success text-white" href="{{ route('admin.projects.create') }}">
                <i class='bx bx-plus'></i> Create
            </a>
        </div>

    </nav>
@endsection

@section('content')

    <!--Start Alert -->
    @include('layouts.dashboard.partials.alert')
    <!--End Alert -->

    <div class="card ">
        <div class="card-body">
            <div class="drag-container"></div>
                <div class="board row">
                    <div class="board-column ongoing col-12 col-sm-12 col-md-3 " >
                        <div class="board-column-container">
                            <div class="board-column-header">On Going</div>
                            <div class="board-column-content-wrapper">
                                <div class="board-column-content" >
                                    @include('admin.project.project.partial.grid.ongoing')
                                </div>
                            </div>

                        </div>
                    </div>
                    <div class="board-column not-started col-12 col-sm-12 col-md-3 " >
                        @include('admin.project.project.partial.grid.upcoming')
                    </div>
                    <div class="board-column completed col-12 col-sm-12 col-md-3 " >
                        @include('admin.project.project.partial.grid.complete')
                    </div>
                    <div class="board-column cancel col-12 col-sm-12 col-md-3 " >
                        @include('admin.project.project.partial.grid.cancel')
                    </div>
                </div>
        </div>
    </div>
@endsection

@push('script')
<script>

    var dragContainer = document.querySelector('.drag-container');
    var itemContainers = [].slice.call(document.querySelectorAll('.board-column-content'));
    var columnGrids = [];
    var boardGrid;

    // Init the column grids so we can drag those items around.
    itemContainers.forEach(function (container) {
        var grid = new Muuri(container, {
            items: '.board-item',
            dragEnabled: true,
            dragSort: function () {
                return columnGrids;
            },
            dragContainer: dragContainer,
            dragAutoScroll: {
                targets: (item) => {
                    return [
                        { element: window, priority: 0 },
                        { element: item.getGrid().getElement().parentNode, priority: 1 },
                    ];
                }
            },
        })
            .on('dragInit', function (item) {
                item.getElement().style.width = item.getWidth() + 'px';
                item.getElement().style.height = item.getHeight() + 'px';
            })
            .on('dragReleaseEnd', function (item) {
                item.getElement().style.width = '';
                item.getElement().style.height = '';
                item.getGrid().refreshItems([item]);
            })
            .on('layoutStart', function () {
                boardGrid.refreshItems().layout();
            });

        columnGrids.push(grid);
    });

    // Init board grid so we can drag those columns around.
    boardGrid = new Muuri('.board', {
        dragEnabled: true,
        dragHandle: '.board-column-header'
    });
</script>

<script>
    $(document).ready(function () {

        $.ajax({
            url: "ongoing?page=" + 1,
            success: function (data) {
                console.log(data);
                $('#ongoing').html(data);
            }
        });

        $(document).on('click', '.pagination a', function (event) {
            event.preventDefault();
            var page = $(this).attr('href').split('page=')[1];
            fetch_data(page);
        });

        function fetch_data(page) {

        }

    });
</script>
@endpush
