@php
    $baseVendor = base_path('vendor');
    $pattern = $baseVendor . '/**/guidelines/.ai/guidelines/*.md';
    $files = glob($pattern, GLOB_BRACE);

    // Group by package
    $grouped = [];
    foreach ($files as $file) {
        // Extract package name: vendor/org/package-name
        preg_match('/vendor\/([^\/]+\/[^\/]+)/', $file, $matches);
        $package = $matches[1] ?? 'unknown';
        $grouped[$package][] = $file;
    }
    ksort($grouped);
@endphp

@foreach ($grouped as $package => $files)
    # {{ $package }} Guidelines

    @foreach ($files as $file)
        {!! trim(file_get_contents($file)) !!}

    @endforeach

@endforeach
