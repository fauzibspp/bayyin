<?php

namespace App\Traits;

trait SwalTrait
{
    public function swal(string $message, string $icon = 'success', string $callback = ''): void
    {
        echo <<<HTML
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
Swal.fire({
    title: 'Information',
    html: {$this->jsonEncode($message)},
    icon: {$this->jsonEncode($icon)},
    confirmButtonText: 'OK'
}).then((result) => {
    if (result.isConfirmed) {
        {$callback}
    }
});
</script>
HTML;
    }

    private function jsonEncode(string $value): string
    {
        return json_encode($value, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
    }
}