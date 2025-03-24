@props(['values'])


<table class="table table-borderless table-striped">

    <thead>
        <th scope="col">Title</th>
        <th>Value</th>
    </thead>

    <tbody>
        @foreach ($values->getAttributes() as $key => $value)
            <tr>
                <td>{{ Str::ucfirst($key) }}</td>

                @if ($key == 'category_id' && isset($values->Category['name']))
                    <td> <a href="{{ route('category.show', [$values->Category['id']]) }}"
                            class="text-decoration-none">{{ $values->Category['name'] }}</a> </td>
                @elseif ($key == 'course_id' && isset($values->course['name']))
                    <td> <a href="{{ route('course.show', [$values->course['id']]) }}"
                            class="text-decoration-none">{{ $values->course['name'] }}</a> </td>
                @elseif($key == 'description')
                    <td class="text-wrap">{!! $value !!}</td>
                @elseif($key == 'image1x1')
                    <td class="text-wrap">
                        <img src="{{ asset('storage/' . $values->image1x1) }}" alt="..."
                            class="d-block me-auto px-auto" height="280px" width="auto">
                    </td>
                @else
                    <td>{{ $value }}</td>
                @endif
            </tr>
        @endforeach

        @if (!empty($values->image[0]))
            <tr>
                <td>Image:</td>
                <td>

                    <div id="carouselExampleControlsNoTouching" class="carousel slide" data-bs-touch="false"
                        style="height: 300px;width:500px;">
                        <div class="carousel-inner">
                            @foreach ($values->image as $index => $item)
                                <div class="carousel-item{{ $index === 0 ? ' active' : '' }}  ">
                                    <img src="{{ asset('storage/' . $item->image) }}"
                                        style="max-height: 300px;object-fit:contain" class="d-block w-100"
                                        alt="carousel">
                                </div>
                            @endforeach

                        </div>
                        <button class="carousel-control-prev" type="button"
                            data-bs-target="#carouselExampleControlsNoTouching" data-bs-slide="prev">
                            <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                            <span class="visually-hidden">Previous</span>
                        </button>
                        <button class="carousel-control-next" type="button"
                            data-bs-target="#carouselExampleControlsNoTouching" data-bs-slide="next">
                            <span class="carousel-control-next-icon" aria-hidden="true"></span>
                            <span class="visually-hidden">Next</span>
                        </button>
                    </div>
                </td>
            </tr>
        @endif

    </tbody>


</table>

