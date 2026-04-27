@props(['url'])

<table class="action" align="center" width="100%" cellpadding="0" cellspacing="0" role="presentation">
    <tr>
        <td align="center">
            <a href="{{ $url }}" class="btn" target="_blank" rel="noopener">
                {{ $slot }}
            </a>
        </td>
    </tr>
</table>
