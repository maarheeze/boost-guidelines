@foreach (Guidelines::discover() as $package => $files)
# {{ $package }} Guidelines

@foreach ($files as $file)
{!! trim(file_get_contents($file)) !!}

@endforeach

@endforeach
