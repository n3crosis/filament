@if (filament()->hasUnsavedChangesAlerts())
    @script
        <script @filamentCspNonce>
            setUpUnsavedActionChangesAlert({
                resolveLivewireComponentUsing: () => @this,
                $wire,
            })
        </script>
    @endscript
@endif
