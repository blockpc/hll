<table {{ $attributes->merge(['class' => 'table']) }}>
    <thead class="thead">
        {{ $thead }}
    </thead>
    <tbody class="tbody">
        {{ $tbody }}
    </tbody>
    @isset($tfoot)
    <tfoot class="tfoot">
        {{ $tfoot }}
    </tfoot>
    @endisset
</table>
