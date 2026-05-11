@props(['status', 'size' => 'sm'])

@php
    $map = [
        'pending'    => ['color' => 'yellow', 'dot' => true],
        'confirmed'  => ['color' => 'blue',   'dot' => false],
        'processing' => ['color' => 'indigo', 'dot' => true],
        'shipped'    => ['color' => 'purple', 'dot' => false],
        'delivered'  => ['color' => 'green',  'dot' => false],
        'cancelled'  => ['color' => 'red',    'dot' => false],
        'refunded'   => ['color' => 'gray',   'dot' => false],
        'paid'       => ['color' => 'green',  'dot' => false],
        'failed'     => ['color' => 'red',    'dot' => false],
        'approved'   => ['color' => 'green',  'dot' => false],
        'rejected'   => ['color' => 'red',    'dot' => false],
        'active'     => ['color' => 'green',  'dot' => true],
        'inactive'   => ['color' => 'gray',   'dot' => false],
    ];
    $cfg   = $map[$status] ?? ['color' => 'gray', 'dot' => false];
    $c     = $cfg['color'];
    $pad   = $size === 'sm' ? 'px-2.5 py-0.5 text-xs' : 'px-3 py-1 text-sm';
    $label = ucfirst($status);
@endphp

<span class="badge bg-{{ $c }}-100 text-{{ $c }}-700 {{ $pad }} inline-flex items-center gap-1.5">
    @if($cfg['dot'])
        <span class="w-1.5 h-1.5 bg-{{ $c }}-500 rounded-full animate-pulse flex-shrink-0"></span>
    @endif
    {{ $label }}
</span>